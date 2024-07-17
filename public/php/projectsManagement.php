<?php
session_start();    
require_once '../controller/generalCRUD.php';
use Controller\GeneralCrud\Crud;

if(isset($_SESSION['rol']) && isset($_SESSION['nombre'])) {
    if($_SESSION['rol']==='ADM' || $_SESSION['rol']==='SAD'){
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv=»X-UA-Compatible» content=»IE-edge» charset=UTF-8″>
    <meta name="viewport" content="width=device-width" initial-scale=1.0″>
    <meta http-equiv=»Content-Type» content=»text/html; charset=ISO-8859-1″ />
    <title>SWGP - Panel de inicio</title>
    <link rel="stylesheet" href="../assets/font-awesome-4.7.0/css/font-awesome.min.css">    
    <link rel="stylesheet" href="../css/style-dash.css">
    <link rel="stylesheet" href="../css/projectMan_style.css">
    <link rel="stylesheet" href="../css/table-style.css">
</head>
<body class="short">
    <div class="container"> 
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <div class="main">
            <?php
            if (isset($_GET['error'])) {
                
                $errorMsg = urldecode($_GET['error']);
                echo "<script>alert('Codigo de error capturado: $errorMsg')</script>";

            }elseif (isset($_GET['projectDetails'])) {
                $projectID = $_GET['projectDetails'];

                $projectData = Crud::findRow("*", "tbl_proyectos", "id_proyecto", $projectID);
                $objectivesGData = Crud::findRow2Condition("id_objetivo,contenido", "tbl_objetivos", "id_proyecto", $projectID,"tipo","general");
                $objectivesEData = Crud::findRow2Condition("id_objetivo,contenido", "tbl_objetivos", "id_proyecto", $projectID,"tipo","especifico");
                $integrantes = Crud::executeResultQuery('SELECT nombre,departamento,responsable FROM tbl_integrantes JOIN tbl_usuarios ON tbl_integrantes.id_usuario = tbl_usuarios.id_usuario WHERE tbl_integrantes.id_proyecto='.$projectID.';');
                $d1 = date("m-d-Y", strtotime($projectData[0]['fecha_inicio']));
                $d2 = date("m-d-Y", strtotime($projectData[0]['fecha_cierre']));
               ?>
                <div class="header">
                    <h4>Gestión de Proyectos</h4>
                </div>
                <div class="detailsContainer scroll">
                    <div class="detailsContainerTitle">
                        <div class="name">
                            <i><?php echo $projectData[0]['nombre']?></i>
                        </div>
                        <div class="fechas">
                            <label class="fechaInicio">Fecha de inicio: <?php echo $d1?></label><br>
                            <label class="fechaCierre">Fecha de cierre: <?php echo $d2?></label>
                        </div>
                    </div>
                    <div class="detailsContainerDiv">
                        <div class="descripcion">
                            <h3>Descripción:</h3>
                            <i style="font-style: normal;"><?php echo $projectData[0]['descripción']?></i>
                        </div>
                    </div>
                    <div class="detailsContainerDiv">
                        <div class="meta">
                            <h3>Meta:</h3>
                            <i style="font-style: normal;"><?php echo $projectData[0]['meta']?></i>
                        </div>
                        <div class="objetivosGen">
                            <h3>Objetivos generales:</h3>
                        <?php   if(count($objectivesGData)!=0){
                                for($i=0;$i<count($objectivesGData);$i++){
                                    echo '<a style="font-style: normal;">'.$objectivesGData[$i]['id_objetivo'].':  '.$objectivesGData[$i]['contenido'].'</a><br>';
                                    $fl = true;
                                } 
                            }else{
                                echo '<a style="font-style: normal;color:#9a9a9a;">Aún no se han registrado objetivos específicos</a><br>';
                            }?>
                        </div>
                    </div>
                    <div class="detailsContainerDiv">
                        <div class="objetivosEsp">
                            <h3>Objetivos específicos:</h3>
                        <?php   if(count($objectivesEData)!=0){
                                for($i=0;$i<count($objectivesEData);$i++){
                                    echo '<a style="font-style: normal;">'.$objectivesEData[$i]['id_objetivo'].':  '.$objectivesEData[$i]['contenido'].'</a><br>';
                                    $fl = true;
                                } 
                            }else{
                            echo '<a style="font-style: normal;color:#9a9a9a;">Aún no se han registrado objetivos específicos</a><br>';
                        }?>
                        </div>
                    </div>
                    <div class="detailsContainerDiv">
                        <div class="integrantes">
                            <h3 >Integrantes:</h3><br>
                        <?php   
                            if(count($integrantes)!=0){
                                for($i=0;$i<count($integrantes);$i++){
                                    echo '<a style="font-style: normal;margin: 1rem;">Nombre: '.$integrantes[$i]['nombre'].'</a><br>';
                                    echo '<a style="font-style: normal;margin: 1rem;">Departamento:  '.$integrantes[$i]['departamento'].'</a><br><br>';
                                    $fl = true;
                                } 
                            }else{
                                echo '<a style="font-style: normal;margin: 1rem;color:#9a9a9a;">Aún no se han registrado integrantes</a><br>';
                            }?>
                        </div>
                    </div>
                    <a id="returnToProjects" class="button redBtn" onclick="returnToProjectsList()" title="Lista de Proyectos"><i class="fa fa-arrow-circle-left"></i></a>
                    <div class="optionsDiv">
                        <a id="printDetails" class="button hide"><i class="fa fa-print" onclick="imprimirProyecto()" title="Imprimir"></i></a>
                        <a id="editProject" class="button hide"><i class="fa fa-share-square-o" onclick="exportarProyecto()" title="Exportar"></i></a>
                        <a id="toggleDocumentOptions" class="button"><i class="fa fa-ellipsis-v" onclick="toggleDocumentOptions()" title="Opciones"></i></a>
                    </div>
                        
                </div>   <!-- Fin detailsContainer -->
               <script src="../js/projectDetails.js"></script>


               <?php } elseif(isset($_GET['editProject'])){ 
                $projectId = $_GET['editProject'] ?? null;
                // Verificar si el ID es un entero
                if ($projectId === null || !filter_var($projectId, FILTER_VALIDATE_INT)) {
                    echo "
                    <script>
                    alert('No se encontro ningun proyecto con el ID proporcionado');
                    window.location.href = `projectsManagement.php`;
                    </script>";       
                }else{
                
                $cR=Crud::findRow("*", "tbl_proyectos", "id_proyecto", $projectId)
                ?>
                <!-- EDITAR PROYECTO -->
                <div class="header">
                    <h4>Gestión de Proyectos</h4>
                </div>
                <div class="editContainer scroll">
                <div class="form-container">
                    <div class="title"><h4>Editar proyecto</h4></div>   
                    <form class="editProject-form" id="basicInfo-form" onsubmit="return updateBasicInfo()"  method="POST" autocomplete="off">
                        <div class="fm-content">
                            <div class="title"><h5>Datos generales:</h5></div>
                            <div class="section1">
                                <label class="bold" for="Fname">Nombre del proyecto:</label><br>
                                <input class="NameInput" type="text" name="Fname" id="Fname" placeholder="Nombre del Proyecto" title="Nombre del proyecto" required value="<?php echo $cR[0]['nombre'] ?>"
                                oninvalid="this.setCustomValidity('El nombre del proyecto es un campo necesario')" oninput="this.setCustomValidity('')"> 
                                <br>

                                <div class="deptoDiv">
                                <label for="deptoAsign">Departamento asignado:</label>
                                <select class="deptoAsign comboBox" id="deptoAsign" name="deptoAsign" style="margin-left:2rem;">
                                    <?php
                                    $Deptos = array();
                                    $query = "SELECT DISTINCT departamento FROM tbl_usuarios;";
                                    $Deptos = Crud::executeResultQuery($query);
                                    $currentDto = $cR[0]['departamentoAsignado'];
                                    if(count($Deptos)>0){
                                        if(in_array($currentDto, $Deptos)){
                                            for($i=0;$i<count($Deptos);$i++){
                                                foreach($Deptos[$i] as $key=>$value){
                                                    echo '<option value="'.$i.'" '.($currentDto == $value ? 'selected' : '').'>'.$value.'</option>';
                                                }
                                            }
                                        }else{
                                            for($i=0;$i<count($Deptos);$i++){
                                                foreach($Deptos[$i] as $key=>$value){
                                                    echo '<option value="'.$i.'">'.$value.'</option>';
                                                }
                                            }
                                            echo '<option value="'.count($Deptos)+1 .' selected">'.$currentDto.'</option>';
                                        }
                                    }
                                    ?>
                                </select>
                                </div>
                            </div>
                            <br>
                            <div class="section2">
                            <div class="datesEditForm">
                                    <div id="fechaIni" class="fechaIni">
                                        <label class="bold" for="fechaInicio">Fecha de inicio:</label><br>
                                        <div class="inline">
                                            <span id="displayDate1"><?php echo $cR[0]['fecha_inicio']; ?></span>
                                            <i id="inDt-edit" onclick="initialDate()" class="fa fa-edit button" title="Editar"></i>
                                            <i id="inDt-save" onclick="saveDate1()" class="fa fa-check-square-o button hide" title="Guardar"></i>
                                            <i id="inDt-cancel" onclick="initialDate()" class="fa fa-times button hide" title="Cancelar"></i>
                                        </div>
                                        <input type="hidden" name="thisDate_inicio" id="thisDate_inicio" value="<?php echo $cR[0]['fecha_inicio']; ?>">
                                        <!-- datePicker -->
                                        <br>
                                        <div id="initDatePicker" class="initDatePicker hide">
                                            <?php $idUnico = "inicio"; include 'datePicker.php'; ?>
                                        </div>
                                    </div> 
                                    <div id="fechaFin" class="fechaFin">
                                        <label class="bold spacer" for="fechaCierre">Fecha de cierre:</label><br>
                                        <div class="inline">
                                            <span id="displayDate2"><?php echo $cR[0]['fecha_cierre']; ?></span>
                                            <i id="fnDt-edit" onclick="finalDate()" class="fa fa-edit button" title="Editar"></i>
                                            <i id="fnDt-save" onclick="saveDate2()" class="fa fa-check-square-o button hide" title="Guardar"></i>
                                            <i id="fnDt-cancel" onclick="finalDate()" class="fa fa-times button hide" title="Cancelar"></i>
                                        </div>
                                        <input type="hidden" name="thisDate_cierre" id="thisDate_cierre" value="<?php echo $cR[0]['fecha_cierre']; ?>">
                                        <!-- datePicker -->
                                        <br>
                                        <div id="endDatePicker" class="endDatePicker hide">
                                            <?php $idUnico = "cierre"; include 'datePicker.php'; ?>
                                        </div>
                                    </div> 
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="fm-content">
                            <div class="section1">

                                <label class="bold" for="Fdescription">Descripción del proyecto:</label><br>
                                <textarea type="text" name="Fdescription" id="Fdescription" placeholder="Descripción del Proyecto" title="Descripción del proyecto" required
                                oninvalid="this.setCustomValidity('Escribe una descripcion del proyecto')" oninput='this.setCustomValidity("");this.style.height = "";this.style.height = this.scrollHeight + "px"'><?php echo $cR[0]['descripción'] ?></textarea>
                                <br>
                                
                            </div>
                            <br>
                            <div class="section2">
                            
                                <label class="bold" for="Fmeta">Meta del proyecto:</label><br>
                                <textarea type="text" name="Fmeta" id="Fmeta" placeholder="Introduzca la meta del proyecto" title="Meta del proyecto" required
                                oninvalid="this.setCustomValidity('Define al menos una meta de proyecto')" oninput='this.setCustomValidity("");this.style.height = "";this.style.height = this.scrollHeight + "px"'><?php echo $cR[0]['meta'] ?></textarea>
                                
                            </div> 
                        </div> <!-- Fin de fm-content -->

                        
                        <div class="form-options">
                            <button class="sumbit-editProject" id="sumbit-editProject" type="submit">Guardar cambios</button>
                            <a href="projectsManagement.php" id="cancel-editProject" class="close-editProject" onclick="return confirmCancel()">Cancelar</a>
                            <!-- <a href="setObjetivos.php?id=<?php echo $_GET['editProject']; ?>" class="objectivesBtn button icon-right" id="edit  Objectives">Editar objetivos del proyecto<i class="fa fa-arrow-circle-o-right"></i></a> -->
                        </div>
                    </form> <!-- Fin de edit-user-form -->

                    <form class="editProject-form" id="editProject-form" action="updateProject.php" method="POST" autocomplete="off">
                        <div class="fm-content specs">
                            <div class="section1">
                            <div class="gestionIntegrantes"> 
                                    <div class="topTable flexAndSpaceDiv">
                                        <label class="bold" for="Fmeta">Integrantes del proyecto:</label><br>
                                        
                                        <a id="manageProjectsLink" class="button addMemberBtn" href="gestionarMiembros.php?id=<?php echo $_GET['editProject']?>">Gestionar integrantes</a>
                                    </div>
                                    <div class="table">
                                        <table class="members-list">
                                            <thead>
                                                <tr>
                                                    <th class="rowNombre">Nombre de integrante</th>
                                                    <th class="rowCargo">Cargo</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            $id=$_GET['editProject'];
                                            $p = array();
                                            $query = "SELECT usuarios.nombre, integrantes.responsable 
                                            FROM tbl_integrantes integrantes JOIN tbl_usuarios usuarios 
                                            ON integrantes.id_usuario = usuarios.id_usuario WHERE integrantes.id_proyecto = ?";
                                            
                                            $p = Crud::executeResultQuery($query, [$id], "i");
                                            if(count($p)>0){
                                                for($i=0;$i<count($p);$i++){
                                                    echo '<tr>';
                                                    foreach($p[$i] as $key=>$value){
                                                        if($p[$i]['responsable'] == $value){
                                                            if($value == 1){
                                                                echo '<td>Responsable de proyecto</td>';
                                                            }else{
                                                                echo '<td>Colaborador</td>';
                                                            }
                                                        }else{
                                                            echo '<td>'.$value.'</td>';
                                                        }
                                                    
                                                    }
                                                    echo '</tr>';
                                                }
                                            }else {
                                                echo "<tr><td colspan='4'>No se encontraron integrantes registrados.</td>";
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                    </div> <!-- Fin de .table -->
                                </div> <!-- Fin de .gestionIntegrantes -->
                            </div>
                            <div class="section2 projectSpecs">
                            <div class="gestionObjetivos"> 
                            <div class="table"> 
                            <table class="objectiveG-list">
                                <thead>
                                    <tr>
                                        <th class="rowIdObj">No.</th>
                                        <th class="rowObjetivo">Descripcion de Objetivo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $id=$_GET['editProject'];
                                $p = array();
                                $query = "SELECT id_objetivo, contenido 
                                FROM tbl_objetivos WHERE id_proyecto = ? AND tipo = ?";
                                
                                $p = Crud::executeResultQuery($query, [$id, 'general'], "is");
                                if(count($p)>0){
                                    for($i=0;$i<count($p);$i++){
                                        echo '<tr>';
                                        foreach($p[$i] as $key=>$value){
                                            echo '<td>'.$value.'</td>';
                                        }
                                        echo '</tr>';
                                    }
                                }else {
                                    echo "<tr><td colspan='4'>No se encontraron objetivos registrados.</td>";
                                }
                                ?>
                                </tbody>
                            </table>
                            </div> <!-- .table -->
                            </div> <!-- .gestionIntegrantes -->
                            </div>
                        </div>
                    </form> <!-- Fin de edit-user-form -->


            </div> <!-- Fin de form-container -->   

           <script src='../js/editProject.js'></script>






                </div>
            <?php } } else{ ?>

            <div class="header">
                <h4>Gestión de Proyectos</h4>
            </div>

            <div class="projectManagement">


                <!-- Filtros de busqueda -->
                <div class="filterDiv closedFilterDiv">
                    <i id="historialProyectos" class="fa fa-history button" title="Historial de proyectos" style="margin-right:.5rem;"></i>
                    <i id="filterProjectsList" class="fa fa-sliders button" title="Filtrar resultados"></i>
                    <!-- <button id="filtroFecha" class="filtroFecha button hide" disabled>Por porcentaje de avance</button> -->
                    <div class="dropDownFilter1 hide ">
                        <label for="filtersForRol">Departamento asignado:</label>
                        <select class="dropDownDeptoFilter comboBox" id="dropDownDeptoFilter" name="dropDownDeptoFilter" style="margin-left:2rem;">
                            <option value="noFilter"></option>
                            <?php
                                $Deptos = Crud::getFiltersOptions('tbl_proyectos', 'departamentoAsignado');
                                if(count($Deptos)>0){
                                    for($i=0;$i<count($Deptos);$i++){
                                        foreach($Deptos[$i] as $key=>$value){
                                            echo '<option value='.$i.'>'.$value.'</option>';
                                        }
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>

                <!-- Buscar -->
                <div class="nav-buttons">
                    <i id="searchProject" class="fa fa-search button" title="Buscar proyecto"></i>
                    <input type="text" id="search-bar" class="search-bar input hide" placeholder="Buscar...">
                    <i id="searchProyecto" class="fa fa-search button hide" title="Buscar" href="userManagement.php?search="></i>
                </div>

                <!-- Listado de proyectos registrados -->
                <div class="table">
                    <table class="project-list hoverTable">
                        <thead>
                            <tr>
                                <th class="selectProjects"><input type="checkbox" id="selectAllBoxes"></th>
                                <th class="rowName">Nombre del proyecto</th>
                                <th class="rowName">Departamento asignado</th>
                                <th class="rowFechaIni">Fecha de inicio</th>
                                <th class="rowFechaFin">Fecha de cierre</th>
                                <th class="rowActions">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if(isset($_GET['search']) || isset($_GET['filterDto'])){ 
                                if(isset($_GET['search'])){
                                    $p = Crud::selectProjectSearchData("id_proyecto,nombre,departamentoAsignado,fecha_inicio,fecha_cierre", "tbl_proyectos", "id_proyecto", "DESC", $_GET['search']);    
                                }else{
                                    $p = Crud::findRows("id_proyecto,nombre,departamentoAsignado,fecha_inicio,fecha_cierre", "tbl_proyectos", "departamentoAsignado", $_GET['filterDto']);
                                }
                                if (!empty($p) && count($p) > 0) {
                                    for ($i = 0; $i < count($p); $i++) {
                                        $fl = false;
                                        echo '<tr>';
                                        $count = 0;
                                        $currentId = 0;
                                        foreach ($p[$i] as $key => $value) {
                                            if ($count == 0) {
                                                $currentId = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                                            }
                                            if($value === $p[$i]['id_proyecto']){
                                                echo "<td><input type='checkbox' class='project-checkbox' value='$value'></td>";
                                            }else if($value === $p[$i]['nombre']){
                                                $cId = htmlspecialchars($p[$i]['id_proyecto']);
                                                echo "<td><i class='blueText' onclick=seeProjectAccount('$cId') title='Ver detalles de proyecto'>" . htmlspecialchars($value) . "</i></td>";
                                            }else{
                                                echo '<td>' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '</td>';
                                            }
                                            $fl = true;
                                            $count++;
                                        }
                                        if ($fl == true) {
                                            ?>
                                            <td>
                                                <!-- <a id="seeProject" class="fa fa-eye button" title="Ver detalles de proyecto" href="projectMng/projectDetails.php?id=<?php echo urlencode($currentId); ?>" style="color:#333;"></a> -->
                                                <a id="editProjectBtn" class="fa fa-edit button" title="Editar proyecto" href="projectsManagement.php?id=<?php echo urlencode($currentId); ?>"></a>
                                                <a id="closeProject" class="fa fa-close button" title="Cerrar proyecto" href="projectsManagement.php?cerrar=<?php echo urlencode($currentId); ?>"></a>
                                            </td>
                                            <?php
                                            echo '</tr>';
                                        }
                                    }
                                }else {
                                    echo "<tr><td colspan='6'>No se encontraron resultados.</td></tr>";
                                }

                            }else{
                                $p = Crud::selectData("id_proyecto,nombre,departamentoAsignado,fecha_inicio,fecha_cierre", "tbl_proyectos", "id_proyecto", "DESC");
                                if(count($p)>0){
                                    for($i=0;$i<count($p);$i++){
                                        $fl = false;
                                        echo '<tr>';
                                        foreach($p[$i] as $key=>$value){
                                            if($value === $p[$i]['id_proyecto']){
                                                echo "<td><input type='checkbox' class='project-checkbox' value='$value'></td>";
                                            }else if($value === $p[$i]['nombre']){
                                                $cId = htmlspecialchars($p[$i]['id_proyecto']);
                                                echo "<td><i class='blueText' onclick=seeProjectAccount('$cId') title='Ver detalles de proyecto'>" . htmlspecialchars($value) . "</i></td>";
                                            }else{
                                                echo '<td>' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '</td>';
                                            }
                                            $fl = true;
                                        }
                                        if($fl==true){
                                        ?>
                                        <td>
                                            <!-- <a id="seeProject"class="fa fa-eye button" title="Ver detalles de proyecto" href="projectMng/projectDetails.php?id=<?php echo $p[$i][0];?>" style="color:#333;"></a> -->
                                            <a id="editProjectBtn"class="fa fa-edit button" title="Editar proyecto" href="projectsManagement.php?editProject=<?php echo $p[$i]['id_proyecto'];?>"></a>
                                            <a id="closeProject" class="fa fa-close button" title="Cerrar proyecto" href="projectsManagement.php?endProject=<?php echo $p[$i]['id_proyecto'];?>"></a>
                                        </td>
                                        <?php
                                        echo '</tr>';
                                        }
                                    }
                                }else{
                                    echo "<tr><td colspan='6'>No se encontraron resultados.</td></tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div> <!-- Fin de table -->      
         
                <!-- Boton de añadir proyecto -->
                <div class="addBtn"><a id="showProjectFormBtn" title="Crear proyecto" class="fa fa-plus add-project-btn button" style="margin-top:0;"></a></div>

                <div id="projectSelected" class="projectSelected hide">
                    <select class="comboBox" name="actionSelected" id="actionSelected">
                        <option value="0"> - Seleccionar acción - </option>
                        <option value="delete">Cerrar proyecto(s)</option>
                    </select>
                    <a id="applyAction" title="Aplicar acción a los proyectos seleccionadas" class="button apply deleteAll">Aplicar</a>
                    <a id="applyAction2" title="Aplicar acción a los proyectos seleccionadas" class="button apply deleteAllShort fa fa-chevron-right"></a>
                </div>

                <!-- Formulario de alta de proyecto -->
                <form class="addProject-form hide scroll" id="addProject-form" action="projectMng/addProject.php" method="POST" autocomplete="on">
                <div class="form-bg">
                    <div class="form-container">
                        <div class="fm-content">
                            <div class="title"><h4>Agregar proyecto:</h4></div> <br> 
                            <label for="Pname">Nombre del proyecto:</label>
                            <input class="NameInput" type="text" name="Pname" id="Fname" placeholder="Nombre del Proyecto" title="Nombre del proyecto" required oninvalid="this.setCustomValidity('El nombre del proyecto es un campo necesario')" oninput="this.setCustomValidity('')"> 
                            <br>
                            <div class="dates">
                                <div class="fechaInicio">
                                    <label for="fechaInicio">Fecha de inicio:</label><br>
                                    <!-- datePicker -->
                                    <?php $idUnico = "inicio"; include 'datePicker.php'; ?>
                                </div> 
                                
                                <div class="fechaCierre">
                                    <label for="fechaCierre">Fecha de cierre:</label><br>
                                    <!-- datePicker -->
                                    <?php $idUnico = "cierre"; include 'datePicker.php'; ?>
                                </div> 
                            </div>
                            <br>
                            <label for="dropDownDepto">Departamento asignado:</label>
                            <!-- <br> -->
                            <select class="dropDownDepto comboBox" id="dropDownDepto" name="dropDownDepto" style="margin-left:2rem;">
                            <?php
                                $Deptos = Crud::getFiltersOptions('tbl_usuarios', 'departamento');
                                if(count($Deptos)>0){
                                    for($i=0;$i<count($Deptos);$i++){
                                        foreach($Deptos[$i] as $key=>$value){
                                            echo '<option value='.$i.'>'.htmlspecialchars($value, ENT_QUOTES, 'UTF-8').'</option>';
                                        }
                                    }
                                }
                            ?>
                            <option value="other">Otro</option>
                            </select>
                            <div id="newDepto" class="newDepto hide">
                            <input class="newDepto NameInput" type="text" name="newDepto" id="newDepto" placeholder="Departamento a cargo del proyecto..." title="Departamento asignado"> 
                            </div>
                            <div class="midSpacers"><br></div>
                            <label for="Fdescription">Descripción del proyecto:</label>
                            <textarea type="text" name="Fdescription" id="Fdescription" placeholder="Descripción del Proyecto" title="Descripción del proyecto" required
                            oninvalid="this.setCustomValidity('Escribe una descripcion del proyecto')" oninput='this.setCustomValidity("");this.style.height = "";this.style.height = this.scrollHeight + "px"'></textarea> 
                            <br>
                            
                            <label for="Fmeta">Meta del proyecto:</label>
                            <textarea type="text" name="Fmeta" id="Fmeta" placeholder="Introduzca la meta del proyecto" title="Meta del proyecto" required
                            oninvalid="this.setCustomValidity('Define al menos una meta de proyecto')" oninput='this.setCustomValidity("");this.style.height = "";this.style.height = this.scrollHeight + "px"'></textarea>
                            <br>
                            
                            <div class="form-options">
                            <a id="cancel-AddProject" class="close-AddProject" onclick="cerrarFormulario()">Cancelar</a>
                            <button disabled name="sumbit-AddProject" class="sumbit-AddProject" id="sumbit-AddProject" type="submit">Crear proyecto</button>
                            </div>
                            <br>
                            <button disabled name="sumbit-AddProject-obj" class="sumbit-AddProject-obj" id="sumbit-AddProject-obj" type="submit">Crear e ir a definición de objetivos <span class="fa fa-arrow-right"></span></button>
                           
                        </div>
                    </div>
                </div> <!-- Fin de form-container --> 
                </form> <!-- Fin de project-form -->

            </div>
            <script src="../js/projectMng.js"></script>
            <?php
            }
            ?>
        </div>

    </div> <!-- Fin de container -->

    <script src="../js/init.js"></script>
</body>
</html>

<?php
    }else{
        echo "<script>
        alert('No cuentas con los permisos necesarios para acceder a esta página.');
        window.location.href = 'dashboard.php';
    </script>";
    exit();
    }
}
else{
    header("Location: ../index.php");
    exit();
}
?>

