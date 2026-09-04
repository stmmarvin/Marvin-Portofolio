<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'db/config.php';
$connection = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
$connection->set_charset('utf8mb4');
$error = '';
$success = isset($_GET['deleted']) && $_GET['deleted'] === '1' ? 'Project verwijderd.' : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $projectId = (int) ($_POST['project_id'] ?? 0);

    if ($action === 'delete' && $projectId > 0) {
        $statement = $connection->prepare('DELETE FROM Projects WHERE Id = ?');
        $statement->bind_param('i', $projectId);
        $statement->execute();
        $statement->close();
        header('Location: Admin.php?deleted=1');
        exit;
    } elseif (in_array($action, ['create', 'update'], true)) {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $sourceType = $_POST['source_type'] ?? '';
        $projectLink = $sourceType === 'github' ? trim($_POST['project_link'] ?? '') : '';
        $zipPath = $sourceType === 'zip' ? trim($_POST['existing_zip_path'] ?? '') : '';
        $imagePath = null;

        if ($title === '' || $description === '') {
            $error = 'Vul een titel en beschrijving in.';
        } elseif (!in_array($sourceType, ['github', 'zip'], true)) {
            $error = 'Kies een GitHub-link of een ZIP-bestand.';
        } elseif ($sourceType === 'github' && (!filter_var($projectLink, FILTER_VALIDATE_URL) || !preg_match('/(^|\.)github\.com$/i', parse_url($projectLink, PHP_URL_HOST) ?? ''))) {
            $error = 'Vul een geldige GitHub-link in.';
        } elseif ($sourceType === 'zip' && $action === 'create' && (!isset($_FILES['zip_file']) || $_FILES['zip_file']['error'] !== UPLOAD_ERR_OK)) {
            $error = 'Kies een ZIP-bestand van het project.';
        } elseif ($sourceType === 'zip' && isset($_FILES['zip_file']) && $_FILES['zip_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            $zip = $_FILES['zip_file'];
            if ($zip['error'] !== UPLOAD_ERR_OK || $zip['size'] > 50 * 1024 * 1024 || strtolower(pathinfo($zip['name'], PATHINFO_EXTENSION)) !== 'zip') {
                $error = 'Upload een ZIP-bestand van maximaal 50 MB.';
            } else {
                $fileName = bin2hex(random_bytes(16)) . '.zip';
                if (move_uploaded_file($zip['tmp_name'], __DIR__ . '/img/projects/' . $fileName)) {
                    $zipPath = 'img/projects/' . $fileName;
                } else {
                    $error = 'Het ZIP-bestand kon niet worden opgeslagen.';
                }
            }
        } elseif (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $image = $_FILES['image'];
            $imageInfo = $image['error'] === UPLOAD_ERR_OK ? getimagesize($image['tmp_name']) : false;
            $allowedTypes = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp', IMAGETYPE_GIF => 'gif'];
            if ($imageInfo === false || !isset($allowedTypes[$imageInfo[2]]) || $image['size'] > 5 * 1024 * 1024) {
                $error = 'Upload een JPG, PNG, WEBP of GIF van maximaal 5 MB.';
            } else {
                $fileName = bin2hex(random_bytes(16)) . '.' . $allowedTypes[$imageInfo[2]];
                $target = __DIR__ . '/img/projects/' . $fileName;
                if (move_uploaded_file($image['tmp_name'], $target)) {
                    $imagePath = 'img/projects/' . $fileName;
                } else {
                    $error = 'De afbeelding kon niet worden opgeslagen.';
                }
            }
        }

        if ($error === '') {
            if ($action === 'create') {
                $statement = $connection->prepare('INSERT INTO Projects (Title, Description, ProjectLink, ZipPath, ImagePath) VALUES (?, ?, NULLIF(?, \'\'), NULLIF(?, \'\'), ?)');
                $statement->bind_param('sssss', $title, $description, $projectLink, $zipPath, $imagePath);
                $statement->execute();
            } else {
                $statement = $connection->prepare('UPDATE Projects SET Title = ?, Description = ?, ProjectLink = NULLIF(?, \'\'), ZipPath = NULLIF(?, \'\'), ImagePath = COALESCE(?, ImagePath) WHERE Id = ?');
                $statement->bind_param('sssssi', $title, $description, $projectLink, $zipPath, $imagePath, $projectId);
                $statement->execute();
            }
            $statement->close();
        }
    }
}

$projects = $connection->query('SELECT Id, Title, Description, ProjectLink, ZipPath, ImagePath FROM Projects ORDER BY CreatedAt DESC')->fetch_all(MYSQLI_ASSOC);
$connection->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projecten beheren</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
   
</head>
<body class="bg-light">
    <main class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <p class="text-secondary mb-1">Welkom, <?= htmlspecialchars($_SESSION['username']) ?>!</p>
                <h1 class="mb-0">Projecten beheren</h1>
            </div>
            <a class="btn btn-outline-danger" href="logout.php">Uitloggen</a>
        </div>
        <?php if ($error !== ''): ?><div class="alert alert-danger alert-dismissible fade show" role="alert"><?= htmlspecialchars($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Sluiten"></button></div><?php endif; ?>
        <?php if ($success !== ''): ?><div id="statusAlert" class="alert alert-success" role="status"><?= htmlspecialchars($success) ?></div><?php endif; ?>

        <div class="mb-5">
            <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#addProjectModal">Project toevoegen</button>
        </div>

        <div class="modal fade" id="addProjectModal" tabindex="-1" aria-labelledby="addProjectModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="create">
                        <div class="modal-header"><h2 class="modal-title fs-5" id="addProjectModalLabel">Project toevoegen</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Sluiten"></button></div>
                        <div class="modal-body">
                            <div class="mb-3"><label class="form-label" for="title">Titel</label><input class="form-control" id="title" name="title" required maxlength="150"></div>
                            <div class="mb-3"><label class="form-label" for="description">Beschrijving</label><textarea class="form-control" id="description" name="description" rows="4" required></textarea></div>
                            <fieldset class="mb-3"><legend class="form-label fs-6">Projectbron</legend><div class="form-check"><input class="form-check-input" id="source-github" name="source_type" value="github" type="radio" required><label class="form-check-label" for="source-github">GitHub-link</label></div><div class="form-check"><input class="form-check-input" id="source-zip" name="source_type" value="zip" type="radio"><label class="form-check-label" for="source-zip">ZIP-bestand</label></div></fieldset>
                            <div class="mb-3"><label class="form-label" for="project_link">GitHub-link</label><input class="form-control" id="project_link" name="project_link" type="url" placeholder="https://github.com/gebruikersnaam/project"></div>
                            <div class="mb-3"><label class="form-label" for="zip_file">ZIP-bestand</label><input class="form-control" id="zip_file" name="zip_file" type="file" accept=".zip,application/zip"></div>
                            <div class="mb-3"><label class="form-label" for="image">Afbeelding</label><input class="form-control" id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp,image/gif"></div>
                        </div>
                        <div class="modal-footer"><button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Annuleren</button><button class="btn btn-primary" type="submit">Project toevoegen</button></div>
                    </form>
                </div>
            </div>
        </div>

        <h2 class="h4 mb-3">Bestaande projecten</h2>
        <div class="row g-4">
            <?php foreach ($projects as $project): ?>
                <div class="col-md-6 col-lg-4">
                    <article class="card h-100 shadow-sm">
                        <?php if ($project['ImagePath']): ?><img class="card-img-top" src="<?= htmlspecialchars($project['ImagePath']) ?>" alt="<?= htmlspecialchars($project['Title']) ?>" style="max-height: 180px; object-fit: cover;"><?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h3 class="h5 card-title"><?= htmlspecialchars($project['Title']) ?></h3>
                            <p class="card-text text-secondary"><?= htmlspecialchars(mb_strimwidth($project['Description'], 0, 140, '...')) ?></p>
                            <button class="btn btn-primary mt-auto" type="button" data-bs-toggle="modal" data-bs-target="#editProject<?= (int) $project['Id'] ?>">Bewerken</button>
                        </div>
                    </article>
                </div>

                <div class="modal fade" id="editProject<?= (int) $project['Id'] ?>" tabindex="-1" aria-labelledby="editProjectLabel<?= (int) $project['Id'] ?>" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <form method="post" enctype="multipart/form-data">
                                <div class="modal-header"><h2 class="modal-title fs-5" id="editProjectLabel<?= (int) $project['Id'] ?>">Project bewerken</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Sluiten"></button></div>
                                <div class="modal-body">
                                    <input type="hidden" name="action" value="update"><input type="hidden" name="project_id" value="<?= (int) $project['Id'] ?>"><input type="hidden" name="existing_zip_path" value="<?= htmlspecialchars($project['ZipPath'] ?? '') ?>">
                                    <div class="mb-3"><label class="form-label" for="title-<?= (int) $project['Id'] ?>">Titel</label><input class="form-control" id="title-<?= (int) $project['Id'] ?>" name="title" value="<?= htmlspecialchars($project['Title']) ?>" required maxlength="150"></div>
                                    <div class="mb-3"><label class="form-label" for="description-<?= (int) $project['Id'] ?>">Beschrijving</label><textarea class="form-control" id="description-<?= (int) $project['Id'] ?>" name="description" rows="4" required><?= htmlspecialchars($project['Description']) ?></textarea></div>
                                    <fieldset class="mb-3"><legend class="form-label fs-6">Projectbron</legend><div class="form-check"><input class="form-check-input" id="github-<?= (int) $project['Id'] ?>" name="source_type" value="github" type="radio" <?= $project['ProjectLink'] ? 'checked' : '' ?> required><label class="form-check-label" for="github-<?= (int) $project['Id'] ?>">GitHub-link</label></div><div class="form-check"><input class="form-check-input" id="zip-<?= (int) $project['Id'] ?>" name="source_type" value="zip" type="radio" <?= $project['ZipPath'] ? 'checked' : '' ?>><label class="form-check-label" for="zip-<?= (int) $project['Id'] ?>">ZIP-bestand</label></div></fieldset>
                                    <div class="mb-3"><label class="form-label" for="link-<?= (int) $project['Id'] ?>">GitHub-link</label><input class="form-control" id="link-<?= (int) $project['Id'] ?>" name="project_link" type="url" value="<?= htmlspecialchars($project['ProjectLink'] ?? '') ?>"></div>
                                    <div class="mb-3"><label class="form-label" for="zip-file-<?= (int) $project['Id'] ?>">Nieuw ZIP-bestand</label><input class="form-control" id="zip-file-<?= (int) $project['Id'] ?>" name="zip_file" type="file" accept=".zip,application/zip"></div>
                                    <div class="mb-3"><label class="form-label" for="image-<?= (int) $project['Id'] ?>">Nieuwe afbeelding</label><input class="form-control" id="image-<?= (int) $project['Id'] ?>" name="image" type="file" accept="image/jpeg,image/png,image/webp,image/gif"></div>
                                </div>
                                <div class="modal-footer"><button class="btn btn-outline-danger me-auto" type="submit" name="action" value="delete" formnovalidate>Verwijderen</button><button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Annuleren</button><button class="btn btn-primary" type="submit">Opslaan</button></div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
    <script>
        const statusAlert = document.getElementById('statusAlert');
        if (statusAlert) {
            window.history.replaceState({}, document.title, 'Admin.php');
            window.setTimeout(() => statusAlert.remove(), 2000);
        }
    </script>
</body>
</html>
