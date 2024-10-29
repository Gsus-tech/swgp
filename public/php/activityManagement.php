<?php
session_start();
require_once '../controller/generalCRUD.php';
use Controller\GeneralCrud\Crud;

if (isset($_SESSION['rol']) && isset($_SESSION['nombre'])) {
    $allow = false;

    if ($_SESSION['rol'] === 'ADM' || $_SESSION['rol'] === 'SAD'){ $allow = true;}
    else{
        $access=array();
        $query = "SELECT responsable FROM tbl_integrantes WHERE id_usuario = ? AND id_proyecto = ?";
        $access = Crud::executeResultQuery($query, [$_SESSION['id'], $_SESSION['projectSelected']], 'ii');
        $allow = $access[0]['responsable'] == 1 ? true : false;
    }
        
    if ($allow) {
    ?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv=»X-UA-Compatible» content=»IE-edge» charset=UTF-8″>
    <meta name="viewport" content="width=device-width" initial-scale=1.0″>
    <title>SWGP - Panel de inicio</title>
    <link rel="stylesheet" href="../assets/font-awesome-4.7.0/css/font-awesome.min.css">    
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/table-style.css">
    <link rel="stylesheet" href="../css/activities_style.css">
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
            <div class="header flexAndSpaceDiv">
                <h4 class="headerTitle">Actividades del proyecto</h4>
                <?php 
                $needsSelect=array();
                $query = "SELECT responsable FROM tbl_integrantes WHERE id_usuario = ? AND responsable = ?";
                $params = [$_SESSION['id'],1];
                $needsSelect = Crud::executeResultQuery($query, $params, 'ii');
                if(count($needsSelect) > 1 || $_SESSION['rol'] === 'ADM' || $_SESSION['rol'] === 'SAD'){
                    $filterOpt=true; $pagina="activityManagement"; include 'topToolBar.php'; 
                }
                    ?>
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
                                <th class="selectActivities"><input type="checkbox" class="button" id="selectAllActivities"></th>
                                <th class="rowNombre">Actividad</th>
                                <th class="rowEstado">Estado</th>
                                <th class="rowFechaFin">Fecha de finalización</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="activity-list-body">
                        <?php
                        if($_SESSION['responsable'] === true){
                            $id =  $_SESSION['projectSelected'];
                        }else{
                            $id = isset($_GET['id']) ? $_GET['id'] : $_SESSION['projectSelected'] ; //Obtener primer valor del comboBox proyectos
                        }
                        $p = array();
                        $query = "SELECT id_actividad, nombre_actividad, estadoActual, fecha_estimada, descripción, id_usuario, revision
                        FROM tbl_actividades WHERE id_proyecto = ? ORDER BY id_actividad";

                        $estados = [
                            1 => 'pendiente',
                            2 => 'en proceso',
                            3 => 'retrasado',
                            4 => 'finalizado'
                        ];

                        $p = Crud::executeResultQuery($query, [$id], "i");
                        if (count($p) > 0) {
                            for ($i = 0; $i < count($p); $i++) {
                                $revBg = $p[$i]['revision'] === 1 ? 'class="revisionBg" ' : '';
                                $rowN = $i+1;
                                echo "<tr " . $revBg ."row='$rowN' u-d='" . $p[$i]['id_usuario'] . "' a-d='" . $p[$i]['id_actividad'] . "' onclick='SelectThisRowAndDetails(this, \"activity-list-body\")' ondblclick='doubleClickRow(this)'>";
                                $value = $p[$i]['id_actividad'];
                                echo "<td><input type='checkbox' class='activity-checkbox button' value='$value'></td>";
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
                                $y = $p[$i]['id_usuario'];
                                echo "<td><a class='fa fa-trash button' row='$rowN'  title='Eliminar actividad' onclick='DeleteActivity($x, $y)'></a>";
                                echo "<a class='fa fa-edit button editActivityJs' row='$rowN'  title='Editar actividad'></a></td>";
                                echo '</tr>';
                            }
                        } else {
                            echo "<tr id='no-activity-row'><td></td><td colspan='4'>No se encontraron actividades registradas.</td></tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                    <div class="pagination" id="pagination"></div>
                </div>

                <div class="fm-content">
                    <div class="line">
                        <div id="descriptionDiv" class="section1">
                            <label for="descriptionDetails">Descripción de la actividad:</label>
                            <textarea disabled name="descriptionDetails" id="descriptionDetails" class="textarea italic">-- Selecciona una actividad --</textarea>
                        </div>
                        <div class="activityButtonsDiv">
                            
                        </div>
                    </div>
                </div>

                <div class="addBtn" onclick="openAddForm()"><a id="showUserFormBtn" title="Crear actividad" class="fa fa-plus"></a></div>


                <div id="addActivity-form" class="addActivity-form hide">
                    <form class="activity-form" id="activity-form" onsubmit="return submitNewActivity()" method="POST" autocomplete="on">
                        <div class="formContainer">
                            <div class="title"><h4>Nueva actividad de proyecto:</h4></div>
                            <?php
                                //Get project info
                                $projectDates = Crud::executeResultQuery("SELECT fecha_inicio, fecha_cierre FROM tbl_proyectos WHERE id_proyecto = ?", [$id], 'i');
                                $d1 = $projectDates[0]['fecha_inicio'];
                                $d2 = $projectDates[0]['fecha_cierre'];
                                echo "<input type='hidden' id='projectInitDate' value='$d1'>";
                                echo "<input type='hidden' id='projectFinDate' value='$d2'>";
                            ?>
                            <input class='input' type="text" name="Fname" id="Fname" placeholder="Nombre de la actividad" 
                            title="Introduce un nombre identificador para la actividad" value="" oninput="resetField(this)">  
                            <br>
                            <!-- <label for="Fdescription">Descripción:</label> -->
                            <textarea class='textarea' type="text" name="Fdescription" id="Fdescription" placeholder="Descripción" 
                            title="Descripción de la actividad" oninput="resetField(this)"></textarea>
                            <br>
                            <div class="fm-content">
                                <div class="section1" style="margin-bottom:0;">
                                    <div class="dates" end-date="<?php echo $d2; ?>" ini-date="<?php echo $d1; ?>">
                                        <label for="Fdate">Fecha estimada de finalización:</label><br>
                                        <!-- datePicker -->
                                        <input type="date" name="Fdate" class="dateCalendar" onchange="resetField(this)" id="Fdate" value="<?php echo $d2; ?>" lang="es">
                                    </div>
                                </div>

                                <div class="selectDiv section2">
                                <label for="userRespList" class="lbl">Selecciona al responsable de esta actividad:</label><br>
                                    <?php
                                    $account = $_SESSION['id'];
                                    $users = Crud::executeResultQuery("SELECT usuarios.id_usuario, usuarios.nombre FROM tbl_usuarios usuarios JOIN tbl_integrantes integrantes ON usuarios.id_usuario = integrantes.id_usuario WHERE integrantes.id_proyecto = '$id'");
                                    
                                    if(count($users)>=1){
                                    echo "<select name='userRespList' id='userRespList' class='comboBox repSelectCx' onchange='resetField(this); updateRep(this)'>";
                                    echo "<option value='none'> - Selecciona un responsable - </option>";
                                        for($i=0;$i<count($users);$i++){
                                            echo '<option value='.$users[$i]['id_usuario'].'>'.$users[$i]['nombre'].'</option>';
                                        }
                                        $checked = '';
                                    }else{
                                        $checked = 'checked';
                                        echo "<select disabled class='noRepsEncountered comboBox' name='userRespList' id='userRespList' style='margin: .5rem 0 0 .5rem;'>";
                                        echo "<option value='noUsersRegister'>Sin resultados</option>";
                                    }

                                    ?>  
                                    </select>
                                    <br><br>

                                    <input type="hidden" name="myId" id="myId" value="<?php echo $_SESSION['id']; ?>">
                                    <input type="hidden" name="responsableActividad" id="responsableActividad" value="<?php echo $users[0][0] ?>">
                                </div>
                            </div>
                            <div class="selectDiv">
                            <br><label for="objetivoList" class="lbl">Actividad relacionada al objetivo:</label>
                                    <?php
                                    $objetivos = Crud::executeResultQuery("SELECT objetivos.id_objetivo, objetivos.contenido FROM tbl_objetivos objetivos WHERE objetivos.id_proyecto = '$id' AND objetivos.tipo='especifico';");
                                    $flag = false;
                                    echo "<select name='objetivoList' id='objetivoList' class='comboBox' onchange='resetField(this);updateObjectiveDescription(this)'>";
                                    if(count($objetivos)>=1){
                                        $flag = true;
                                    echo "<option value='none'>- Selecciona un objetivo -</option>";
                                        for($i=0;$i<count($objetivos);$i++){
                                            $r = $i+1;
                                            $selected = '';
                                            echo '<option value='.$objetivos[$i]['id_objetivo'].' '.$selected.'>Objetivo: '.$r.'</option>';
                                        }
                                    }else{
                                        echo "<option value='noObjectivesRegister'>Sin objetivos registrados</option>";
                                    }
                                    echo "</select>";
                                    if($flag===true){
                                        echo "<select class='hide' name='objectiveDescriptionList' id='objectiveDescriptionList'>";
                                        for($i=0;$i<count($objetivos);$i++){
                                            echo '<option value='.$objetivos[$i]['id_objetivo'].'>'.$objetivos[$i]['contenido'].'</option>';
                                        }
                                        echo "</select>";
                                    }
                                    ?>
                                <input type="hidden" name="objetivoEnlazado" id="objetivoEnlazado" value="<?php echo $objetivos[0][0] ?>">
                            </div>
                            <textarea disabled type="text" class="textarea objetivoDisplay" name="ObjectiveDescription" id="ObjectiveDescription"></textarea>
                            
                            <div class="form-options">
                                <button class="sumbit-newTask enabled" id="sumbit-editTask" type="submit">Guardar cambios</button>
                                <a id="cancel-editTask" class="close-newTask button" onclick="return confirmCancelEdit()">Cancelar</a>
                            </div>
                            <input type="hidden" name="idProyectoPage" value="<?php echo  $id; ?>">
                        
                        </div> <!-- Fin de form-container --> 
                    </form> <!-- Fin de activity-form -->
                </div>
            </div>
        </div>

    </div> <!-- Fin de container -->
    <script src="../js/tablePagination.js"></script>
    <script src="../js/validate.js"></script>
    <script src="../js/activityMng.js"></script>
    <script src="../js/reportes.js"></script>
    <script src="../js/init.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/html-docx-js@0.3.1/dist/html-docx.min.js"></script>
</body>
</html>
<?php
}else{
    if($_SESSION['responsable'] === true){
        $getMyProject=array();
        $query = "SELECT id_proyecto FROM tbl_integrantes WHERE id_usuario = ? AND responsable = ?";
        $params = [$_SESSION['id'],1];
        $getMyProject = Crud::executeResultQuery($query, $params, 'ii');
        $_SESSION['projectSelected'] = $getMyProject[0]['id_proyecto'];
        echo "<script>
        window.location.href = `activityManagement.php`;
        </script>";
    }else{
        echo "<script>
        alert('No cuentas con los permisos necesarios para ingresar a esta página.')
        window.location.href = `dashboard.php`;
        </script>";
    }
}
}
else{
    header("Location: dashboard.php");
    exit();
}
?>
