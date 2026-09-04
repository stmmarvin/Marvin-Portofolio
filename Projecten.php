<?php
session_start();

require_once 'db/config.php';
$connection = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
$connection->set_charset('utf8mb4');
$projects = $connection->query('SELECT Title, Description, ProjectLink, ZipPath, ImagePath FROM Projects ORDER BY CreatedAt DESC')->fetch_all(MYSQLI_ASSOC);
$connection->close();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projecten</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
        <nav class="navbar bg-dark" style="height: 60px;">
                <div class="container d-flex align-items-center">
                        <img src="img/Marvin Logo Transparant.png" alt="Logo" width="80" height="50" class="d-inline-block align-text-top">
                        <a class="navbar-brand text-white" href="index.php">Marvin Portfolio</a>
                        <a class="nav-link text-white ms-auto" href="OverMij.php">Over mij</a>
                </div>
        </nav>
    <main class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Projecten</h1>
            <?php if (isset($_SESSION['user_id'])): ?><a class="btn btn-outline-secondary" href="Admin.php">Beheren</a><?php endif; ?>
        </div>
        <?php if (count($projects) === 0): ?>
            <div class="alert alert-info" role="status">Er zijn momenteel geen projecten toegevoegd.</div>
        <?php endif; ?>
        <div class="row g-4">
            <?php foreach ($projects as $project): ?>
                <div class="col-md-6 col-lg-4">
                    <article class="card h-100 shadow-sm">
                        <?php if ($project['ImagePath']): ?><img src="<?= htmlspecialchars($project['ImagePath']) ?>" class="card-img-top" alt="<?= htmlspecialchars($project['Title']) ?>"><?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h2 class="h5 card-title"><?= htmlspecialchars($project['Title']) ?></h2>
                            <p class="card-text"><?= nl2br(htmlspecialchars($project['Description'])) ?></p>
                            <?php if ($project['ProjectLink']): ?><a class="btn btn-primary mt-auto" href="<?= htmlspecialchars($project['ProjectLink']) ?>" target="_blank" rel="noopener">Project bekijken</a><?php endif; ?>
                            <?php if ($project['ZipPath']): ?><a class="btn btn-outline-secondary mt-2" href="<?= htmlspecialchars($project['ZipPath']) ?>" download>ZIP downloaden</a><?php endif; ?>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</body>
</html>