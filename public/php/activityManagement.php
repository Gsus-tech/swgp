<?php
session_start();
require_once '../controller/generalCRUD.php';
use Controller\GeneralCrud\Crud;

if (isset($_SESSION['rol']) && isset($_SESSION['nombre'])) {
    if ($_SESSION['rol'] === 'ADM' || $_SESSION['rol'] === 'SAD' || $_SESSION['responsable'] === true) {
    ?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv=»X-UA-Compatible» content=»IE-edge» charset=UTF-8″>
    <meta name="viewport" content="width=device-width" initial-scale=1.0″>
    <title>SWGP - Panel de inicio</title>
    <link rel="stylesheet" href="../assets/font-awesome-4.7.0/css/font-awesome.min.css">    
    <link rel="stylesheet" href="../css/style-dash.css">
    <link rel="stylesheet" href="../css/style-userTools.css">
    <link rel="stylesheet" href="../css/table-style.css">
    <link rel="stylesheet" href="../css/activities_style.css">
</head>
<body class="short">
    <div class="container"> 
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <div class="main">
            <div class="header flexAndSpaceDiv">
                <h4 class="headerTitle">Gestión de actividades</h4>
                <?php $pagina="activityManagement"; include 'topToolBar.php'; ?>
            </div>
            <div class="activityManagement">
            

                <div class="table">
                    <table class="activity-list">
                        <thead>
                            <tr>
                                <th><input id='selectAllActivities' type='checkbox' class='activiy-checkbox' value='$value'></th>
                                <th class="rowNombre">Actividad</th>
                                <th class="rowCargo">Estado</th>
                                <th class="rowCargo">Fecha de finalización</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="activity-list-body">
                        <?php
                        if($_SESSION['responsable'] === true){
                            $id =  $_SESSION['projectSelected'];
                        }else{
                            $id = isset($_GET['id']) ? $_GET['id'] : "1" ; //Obtener primer valor del comboBox proyectos
                        }
                        $p = array();
                        $query = "SELECT id_actividad, nombre_actividad, estadoActual, fecha_estimada 
                        FROM tbl_actividades WHERE id_proyecto = ?";

                        $p = Crud::executeResultQuery($query, [$id], "i");
                        if (count($p) > 0) {
                            for ($i = 0; $i < count($p); $i++) {
                                $rowN = $i+1;
                                echo "<tr row='$rowN' onclick='SelectThisRow(this, \"activity-list-body\")'>";
                                $value = $p[$i]['id_actividad'];
                                echo "<td><input type='checkbox' class='activiy-checkbox' value='$value'></td>";
                                $j=0;
                                foreach($p[$i] as $key => $value) {
                                    if($j>0){
                                        $str = $value === null ? "<td><i>Sin especificar</i></td>" : '<td>' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '</td>';
                                        echo $str;
                                    }
                                    $j++;
                                }
                                $x = $p[$i]['id_actividad'];
                                echo "<td><a class='fa fa-trash tableIconBtn' row='$rowN'  title='Eliminar actividad' onclick='DeleteActivity($x, this)'></a></td>";
                                echo '</tr>';
                            }
                        } else {
                            echo "<tr id='no-activity-row'><td></td><td colspan='4'>No se encontraron actividades registradas.</td></tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        <script>


            document.addEventListener("DOMContentLoaded", function() {

            });
        </script>
    </div> <!-- Fin de container -->

    <script src="../js/init.js"></script>
</body>
</html>
<?php
}else{
    echo "<script>console.log('No cuentas con los permisos necesarios para ingresar a esta página.')</script>";
}
}
else{
    header("Location: ../index.php");
    exit();
}
?>
