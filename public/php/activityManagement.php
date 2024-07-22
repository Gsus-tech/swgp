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
            <div class="activityManagement scroll">
            
             <!-- Filtros de busqueda -->
             <div class="filterDiv closedFilterDiv" id="filterDiv">
                    <i id="filterSlidersIcon" class="fa fa-sliders button" title="Filtrar resultados" onclick="FiltersToggle()"></i>
                    <div class="dropDownFilter hide">
                        <label for="filtersForRol">Estado</label>
                        <select class="dropDownEstadoFilter comboBox mL-2r" id="dropDownEstadoFilter" name="dropDownEstadoFilter" onchange="FilterResults(this)">
                            <option value="noFilter">Todos</option>
                            <option value="pendiente">Pendientes</option>
                            <option value="en proceso">En proceso</option>
                            <option value="retrasado">Retrasadas</option>
                            <option value="finalizado">Terminadas</option>
                        </select>
                    </div>
                </div>


                <div id="selectedRowsOptions" class="selectedRowsOptions hide">
                    <select class="comboBox" name="actionSelected" id="actionSelected">
                        <option value="0"> - Seleccionar acción - </option>
                        <option value="delete">Eliminar actividad(es)</option>
                    </select>
                    <a id="applyAction" title="Aplicar acción a las actividades seleccionadas" class="button apply normalBtn">Aplicar</a>
                    <a id="applyAction2" title="Aplicar acción a las actividades seleccionadas" class="button apply shortBtn fa fa-chevron-right"></a>
                </div>


                <div class="table">
                    <table class="activity-list">
                        <thead>
                            <tr>
                                <th class="selectActivities"><input type="checkbox" id="selectAllActivities"></th>
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
                            $id = isset($_GET['id']) ? $_GET['id'] : "13" ; //Obtener primer valor del comboBox proyectos
                        }
                        $p = array();
                        $query = "SELECT id_actividad, nombre_actividad, estadoActual, fecha_estimada, descripción
                        FROM tbl_actividades WHERE id_proyecto = ?";

                        $estados = [
                            1 => 'pendiente',
                            2 => 'en proceso',
                            3 => 'finalizado',
                            4 => 'retrasado'
                        ];

                        $p = Crud::executeResultQuery($query, [$id], "i");
                        if (count($p) > 0) {
                            for ($i = 0; $i < count($p); $i++) {
                                $rowN = $i+1;
                                echo "<tr row='$rowN' onclick='SelectThisRowAndDetails(this, \"activity-list-body\")'>";
                                $value = $p[$i]['id_actividad'];
                                echo "<td><input type='checkbox' class='activity-checkbox' value='$value'></td>";
                                $camposMostrar = ['nombre_actividad', 'estadoActual', 'fecha_estimada', 'descripción'];
                                foreach ($camposMostrar as $campo) {
                                    $value = $p[$i][$campo];
                                    if ($campo == 'estadoActual') {
                                        $str = $value === null ? "<td><i>Sin especificar</i></td>" : '<td>' . $estados[$value] . '</td>';
                                    }elseif($campo == 'fecha_estimada' && $value === null){
                                        $str = "<td><i>Sin especificar</i></td>";
                                    }elseif($campo == 'descripción'){
                                        $str = "<td class='thisDescription' style='display:none;'>". htmlspecialchars($value, ENT_QUOTES, 'UTF-8') ."</td>";
                                    }else {
                                        $str = $value === null ? "<td><i>Sin especificar</i></td>" : '<td>' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '</td>';
                                    }
                                    echo $str;
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

                <div class="fm-content">
                    <div class="line">
                        <div id="descriptionDiv" class="section1">
                            <label for="descriptionDetails">Descripción de la actividad:</label>
                            <textarea disabled name="descriptionDetails" id="descriptionDetails" class="textarea italic">-- Selecciona una actividad --</textarea>
                        </div>
                        <div class="section2 table">
                            <table id="reportsMade">
                                <thead>
                                    <tr>
                                        <th class="rowNombre">Reporte de actividad</th>
                                        <th class="rowCargo">Fecha de creación</th>
                                    </tr>
                                </thead>
                                <tbody id="reportsMade_tbody">
                                    <tr>
                                        <td colspan="2"><i>Selecciona una actividad</i></td>
                                    </tr>
                                </tbody>    
                            </table>
                        </div>
                    </div>
                    
                </div>
                
                <div class="fm-content">
                    <h4>Mostrar contenido del reporte seleccionado</h4>
                </div>

            </div>
        </div>

    </div> <!-- Fin de container -->
    
    <script src="../js/activityMng.js"></script>
    <script src="../js/init.js"></script>
</body>
</html>
<?php
}else{

    echo "<script>
    alert('No cuentas con los permisos necesarios para ingresar a esta página.')
    window.location.href = `dashboard.php`;
    </script>";
}
}
else{
    header("Location: ../index.php");
    exit();
}
?>
