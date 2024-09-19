<?php
session_start();
require_once '../controller/generalCRUD.php';
use Controller\GeneralCrud\Crud;

if (isset($_SESSION['rol']) && isset($_SESSION['nombre'])) {
    $allow = false;

    $access=array();
    $query = "SELECT id_proyecto FROM tbl_actividades WHERE id_usuario = ?";
    $access = Crud::executeResultQuery($query, [$_SESSION['id']], 'i');
    $allow = count($access) >= 1;

    if ($allow) {
    ?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv=»X-UA-Compatible» content=»IE-edge» charset=UTF-8″>
    <meta name="viewport" content="width=device-width" initial-scale=1.0″>
    <title>SWGP - gestión de reportes</title>
    <link rel="stylesheet" href="../assets/font-awesome-4.7.0/css/font-awesome.min.css">    
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/table-style.css">
</head>
<body class="short">
    <div class="container"> 
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <div class="main">
            <div class="header flexAndSpaceDiv">
                <h4 class="headerTitle">Reporte de actividades</h4>
                <?php 
                $reportAccessOnly=true; $pagina="activityManagement"; include 'topToolBar.php'; 
                ?>
            </div>
        </div>

    </div> <!-- Fin de container -->
    <script src="../js/tablePagination.js"></script>
    <script src="../js/validate.js"></script>
    <script src="../js/init.js"></script>
</body>
</html>
<?php
    }else{
        echo "<script>
        alert('No se encontró ninguna actividad en la que puedas agregar reportes.')
        window.location.href = `dashboard.php`;
        </script>";
    }
}
else{
    header("Location: dashboard.php");
    exit();
}
?>
