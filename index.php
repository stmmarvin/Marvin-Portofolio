<?php
require_once 'db/config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marvin Portfolio</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
   </head>
<body>
    <!-- Navbar -->
    <nav class="navbar bg-dark" style="height: 60px;">
  <div class="container d-flex align-items-center">
    <img src="img/Marvin Logo Transparant.png" alt="Logo" width="80" height="50" class="d-inline-block align-text-top">
    <span class="navbar-brand text-white">Marvin Portfolio</span>
    <div class="ms-auto d-flex gap-3">
        <a class="nav-link text-white" href="OverMij.php">Over mij</a>
        <a class="nav-link text-white" href="Projecten.php">Gemaakte projecten</a>
    </div>
  </div>
</nav>

    <!-- Main Content -->
    <div class="container my-5">
        <!-- Profile picture (Circle) -->
        <div class="row mb-5">
        <div class="col-12 text-center">
            <img src="img/marvin.jpg" alt="Profile Image" class="rounded-circle border border-3" width="250" height="250" style="object-fit: cover;">
        </div>
   </div>

      <!-- Title and description -->
                  <div class="row mb-5">
                    <div class="col-12 text-center">
                        <h1>Welkom bij mijn portfolio</h1>

                        <p class="lead">
                            Mijn naam is Marvin Akpabot. Ik ben 22 jaar en woon in Zeist. 
                            Ik volg de opleiding HBO Open ICT aan de hogeschool Utrecht.
                            Naast mijn studie werk ik bij de Albert Heijn als Kassamedewerker.
                            </p>
                         
                          <p>
                            Mijn hobby's zijn gamen, koken, bakken en fietsen. 
                            Ik ben gek op lekker eten en hou van dagjes uit als ik daar zin in heb.
                            </p>
                            
                          
                        
                    </div>
                  </div>
                
                <!-- 3 Project Boxen (Wireframe) -->
        <div class="row g-4">
            <div class="col-md-4">
                <div class="bg-dark p-4 rounded" style="height: 150px;">
                <h5><a href="Projecten.php" class="text-white">Gemaakte projecten</a></h5>
                </div>
            </div>
            <div class="col-md-4">
                <div class="bg-dark p-4 rounded" style="height: 150px;">
                    <h5 class="text-white">Project 2</h5>
                </div>
            </div>
            <div class="col-md-4">
                <div class="bg-dark p-4 rounded" style="height: 150px;">
                    <h5 class="text-white">Project 3</h5>
                </div>
            </div>
        </div>
 <!-- Footer content -->
  <div class="footer text-center text-muted py-3">
    <p>&copy; 2026 Marvin Akpabot. All rights reserved.</p>
  </div>
    </div>
    <script src="script.js"></script>
      
</body>
</html>