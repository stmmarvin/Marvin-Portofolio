<?php
session_start();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Over mij | Marvin Portfolio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar bg-dark" style="height: 60px;">
        <div class="container d-flex align-items-center">
            <img src="img/Marvin Logo Transparant.png" alt="Logo" width="80" height="50" class="d-inline-block align-text-top">
            <a class="navbar-brand text-white" href="index.php">Marvin Portfolio</a>
            <a class="nav-link text-white ms-auto" href="Projecten.php">Gemaakte projecten</a>
        </div>
    </nav>

    <main class="container py-5">
        <div class="row align-items-center g-5">
            <div class="col-md-5 text-center">
                <img src="img/marvin.jpg" alt="Portret van Marvin Akpabot" class="rounded-circle border border-3" width="250" height="250" style="object-fit: cover;">
            </div>
            <div class="col-md-7">
                <h1>Over mij</h1>
                <p class="lead">Mijn naam is Marvin Akpabot. Ik ben 22 jaar en woon in Zeist.</p>
                <p>Ik volg de opleiding HBO Open ICT aan de Hogeschool Utrecht. Naast mijn studie werk ik bij Albert Heijn als kassamedewerker.</p>
                <p>In mijn vrije tijd houd ik van gamen, koken, bakken en fietsen. Ook ga ik graag een dagje uit.</p>
                <p>Ik heb hiervoor de opleiding Sofware Development niveau 4 bij MBO Utrecht gevolgd.</p>
                <p>Waar ik mijn kennis in wil ontwikkelen is een nieuwe taal leren zoals python. En sta er voor open om een bestaande taal te verbeteren zoals Javascript.</p>
            </div>
        </div>
    </main>
</body>
</html>