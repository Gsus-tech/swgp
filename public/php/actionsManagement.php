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
    <link rel="stylesheet" href="../css/actions_style.css">
    <link rel="stylesheet" href="../css/reportes.css">
    <?php
    $query = "SELECT notificaciones,tema,tLetra FROM `tbl_preferencias_usuario` WHERE id_usuario = ?";
    $params = [$_SESSION['id']];
    $preferences = Crud::executeResultQuery($query, $params, 'i');
    if(count($preferences) > 0){
        $tema = '';
        if($preferences[0]['tema'] !== 'Sistema'){
            $tema = $preferences[0]['tema'] === 'Claro' ? 'lightMode' : 'darkMode';
        }
        $fontStyle = '';
        if($preferences[0]['tLetra'] === 'Grande'){
            $fontStyle = 'bigFont';
        }
    }
    
    ?>
</head>
<body class="short <?php $classes="$tema $fontStyle"; echo $classes; ?>">
    <div class="container"> 
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <div class="main">
            <div class="fondoCobach"></div>
            <div class="header flexAndSpaceDiv">
                <h4 class="headerTitle">Reporte de actividades</h4>
                <?php 
                $reportAccessOnly=true; $pagina="activityManagement"; include 'topToolBar.php'; 
                ?>
            </div>
            <?php
            if(isset($_SESSION['currentActivityEdition'])){
                echo "<input type='hidden' id='updateDataNow' value='". $_SESSION['currentActivityEdition'] ."'>";
                unset($_SESSION['currentActivityEdition']);
            }
            ?>
            <div class="activity-wrapper">
                <div class="selectActivityDiv">
                    <p for="actividad" class="select-label">Actividad seleccionada:</p>
                    <br>
                    <select name="actividad" class="comboBox" id="select-actividad" onchange="updatePageData()">
                        <option value="none">-- Selecciona una actividad --</option>
                        <?php
                        $query = "SELECT id_actividad, nombre_actividad, estadoActual FROM tbl_actividades WHERE id_usuario = ? AND id_proyecto = ?";
                        $actList = Crud::executeResultQuery($query, [$_SESSION['id'], $_SESSION['projectSelected']], 'ii');          
                        if(count($actList) > 0){
                            foreach ($actList as $actividad) {
                                echo '<option value="' . htmlspecialchars($actividad['id_actividad'], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($actividad['nombre_actividad'], ENT_QUOTES, 'UTF-8') . '</option>';
                            }
                        } else {
                            echo '<option value="non-register">No hay actividades disponibles</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="activityStatusDiv">
                    <label class="status-label">Estado de la actividad:</label>
                    <p id="estadoActividad"><i>No disponible</i></p>
                        <br>
                    <label class="reports-label">No. de reportes realizados:</label>
                    <p id="numeroReportes"><i>0</i></p>
                </div>
            </div>

            <div class="table">
                <table id="reportsMade">
                    <thead>
                        <tr>
                            <th class="rowNombre">Reporte de actividad</th>
                            <th class="rowFecha">Fecha de creación</th>
                            <th class="rowAcciones">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="reportsMade_tbody">
                        <tr>
                            <td colspan="3"><i>Selecciona una actividad</i></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            
            
            
        </div>

    </div> <!-- Fin de container -->
    <script src="../js/tablePagination.js"></script>
    <script src="../js/validate.js"></script>
    <script src="../js/actionsMng.js"></script>
    <script src="../js/reportes.js"></script>
    <script src="../js/init.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/html-docx-js@0.3.1/dist/html-docx.min.js"></script>
</body>
</html>
<script>
//Cargar la informacion de una actividad tras insertar un reporte
document.addEventListener('DOMContentLoaded', function() {
    const redirectedToReport = localStorage.getItem('openReportEditor');
    if (redirectedToReport) {
        const actId =  localStorage.getItem('actId');

        const selectElement = document.getElementById('select-actividad');
        const reportBtn = document.querySelector('.addBtn');
        if (actId && selectElement && reportBtn) {
            selectElement.value = actId;
    
            const event = new Event('change');
            selectElement.dispatchEvent(event);
            
            localStorage.removeItem('openReportEditor');
            localStorage.removeItem('actId');
        }
    }
});
</script>
<?php

        if (isset($_SESSION['error_message'])) {
            $error_message = $_SESSION['error_message'];
            unset($_SESSION['error_message']);
            echo "<script>alert('Error: $error_message');</script>";
        }
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
