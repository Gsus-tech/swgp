<?php
session_start();
require_once '../controller/generalCRUD.php';
use Controller\GeneralCrud\Crud;

if (isset($_SESSION['rol']) && isset($_SESSION['nombre'])) {

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv=»X-UA-Compatible» content=»IE-edge» charset=UTF-8″>
    <meta name="viewport" content="width=device-width" initial-scale=1.0″>
    <title>SWGP - Soporte</title>
    <link rel="stylesheet" href="../assets/font-awesome-4.7.0/css/font-awesome.min.css">    
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/soporte.css">
</head>
<body class="short">
    <div class="container">
<?php
    include 'sidebar.php';
    echo " <div class='main'>";
        if (isset($_SESSION['error_message'])) {
            $error_message = $_SESSION['error_message'];
            unset($_SESSION['error_message']);
            echo "<script>alert('Error: $error_message');</script>";
        }

        if ($_SESSION['rol'] === 'ADM' || $_SESSION['rol'] === 'SAD') {
        ?>
        <div class="header flexAndSpaceDiv">
            <h4 class="headerTitle">Gestión de Soporte</h4>
        </div>

        <?php

        }else{
        ?>
        <div class="header flexAndSpaceDiv">
            <h4 class="headerTitle">Módulo de Soporte</h4>
        </div>
        <?php
        }
        ?>
        </div> <!-- Main -->
    </div> <!-- Container -->
<script src="../js/init.js"></script>
</body>
</html>
<?php
}
else{
    header("Location: dashboard.php");
    exit();
}
?>

