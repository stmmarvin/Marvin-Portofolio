<?php
// Start the session and include the database configuration
session_start();
// Include the database configuration file
require_once 'db/config.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Initialize variables for error messages and user input
$error = '';
$email = '';
/**
 * Handle the login form submission
 *
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
// Validate the input and check the credentials
    if ($email === '' || $password === '') {
        $error = 'Vul je e-mailadres en wachtwoord in.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Vul een geldig e-mailadres in.';
    } else {
        require_once 'db/config.php';
        $connection = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
        // Check for connection errors
        if ($connection->connect_error) {
            $error = 'Er kon geen verbinding met de database worden gemaakt.';
        } else {
            $statement = $connection->prepare(
                'SELECT Id, Username, PasswordHash FROM User WHERE Email = ? LIMIT 1'
            );
            $statement->bind_param('s', $email);
            $statement->execute();
            $result = $statement->get_result();
            $user = $result->fetch_assoc();

            // Check if the user exists and verify the password
            if ($user && password_verify($password, $user['PasswordHash'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['Id'];
                $_SESSION['username'] = $user['Username'];
                header('Location: Admin.php');
                exit;
            }
        // If the credentials are incorrect, set an error message
            $error = 'Het e-mailadres of wachtwoord is onjuist.';
            $statement->close();
            $connection->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inloggen | Marvin Portfolio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <main class="container min-vh-100 d-flex align-items-center justify-content-center py-4">
        <section class="card shadow-sm border-0" style="max-width: 420px; width: 100%;" aria-labelledby="login-title">
            <div class="card-body p-4 p-md-5">
                <p class="text-secondary text-uppercase fw-bold small mb-2">Marvin Portfolio</p>
                <h1 id="login-title" class="h2 mb-4">Inloggen</h1>

                <?php if ($error !== ''): ?>
                    <div class="alert alert-danger" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>

                <form action="login.php" method="post">
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mailadres</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>" autocomplete="email" required>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Wachtwoord</label>
                        <input type="password" class="form-control" id="password" name="password" autocomplete="current-password" required>
                    </div>

                    <button type="submit" class="btn btn-dark w-100">Inloggen</button>
                </form>
            </div>
        </section>
    </main>
</body>
</html>
