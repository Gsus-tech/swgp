<?php
session_start();
require_once '../controller/generalCRUD.php';
use Controller\GeneralCrud\Crud;

if (isset($_SESSION['rol']) && isset($_SESSION['nombre'])) {
    if ($_SESSION['rol'] === 'ADM' || $_SESSION['rol'] === 'SAD') {
        ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv=»X-UA-Compatible» content=»IE-edge» charset=UTF-8″>
    <meta name="viewport" content="width=device-width" initial-scale=1.0″>
    <meta http-equiv=»Content-Type» content=»text/html; charset=ISO-8859-1″ />
    <title>SWGP - Panel de inicio</title>
    <link rel="stylesheet" href="../assets/font-awesome-4.7.0/css/font-awesome.min.css">    
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/projectMan_style.css">
    <link rel="stylesheet" href="../css/table-style.css">
</head>
<body class="short">
    <div class="container"> 
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <div class="main">
            <?php
            function getActPercentage($id){
                $query = "SELECT estadoActual FROM tbl_actividades WHERE id_proyecto = ?";
                $params = [$id];
                $actP = Crud::executeResultQuery($query, $params, 'i');
                $totalActividades = count($actP);
                if($totalActividades != 0){
                    $actividadesCompletadas = 0;
                    for($j=0; $j<$totalActividades; $j++){
                        if((int)$actP[$j]['estadoActual'] === 4){
                            $actividadesCompletadas++;
                        }
                    }
                    if($actividadesCompletadas > 0){
                        return number_format(($actividadesCompletadas / $totalActividades) * 100, 2);
                    }else{
                        return 0;
                    }
                }else{
                    return 0;
                }
            }
            if (isset($_GET['error'])) {
                $errorMsg = urldecode($_GET['error']);
                echo "<script>alert('Codigo de error capturado: $errorMsg')</script>";
            } elseif (isset($_GET['projectDetails'])) {
                $projectID = filter_var($_GET['projectDetails'], FILTER_VALIDATE_INT);
            
                if ($projectID === false) {
                    // Si el ID del proyecto no es un entero válido
                    echo "<script>
                        alert('ID de proyecto no válido.');
                        window.location.href = 'projectsManagement.php?projectDetails=" . $_SESSION['projectSelected'] . "';
                    </script>";
                    exit;
                }
            
                // Consulta para obtener los datos del proyecto
                $query = "SELECT * FROM `tbl_proyectos` WHERE id_proyecto = ?";
                $projectData = Crud::executeResultQuery($query, [$projectID], 'i');
            
                // Comprobar si el proyecto existe
                if (!$projectData || count($projectData) == 0) {
                    echo "<script>
                        alert('ID de proyecto no registrado.');
                        window.location.href = 'projectsManagement.php?projectDetails=" . $_SESSION['projectSelected'] . "';
                    </script>";
                    exit;
                }
                $_SESSION['projectSelected'] = $projectID;
                $objectivesGData = Crud::findRow2Condition("id_objetivo,contenido", "tbl_objetivos", "id_proyecto", $projectID, "tipo", "general");
                $objectivesEData = Crud::findRow2Condition("id_objetivo,contenido", "tbl_objetivos", "id_proyecto", $projectID, "tipo", "especifico");
                $integrantes = Crud::executeResultQuery('SELECT nombre,departamento,responsable FROM tbl_integrantes JOIN tbl_usuarios ON tbl_integrantes.id_usuario = tbl_usuarios.id_usuario WHERE tbl_integrantes.id_proyecto=' . $projectID . ';');
                $d1 = date("m-d-Y", strtotime($projectData[0]['fecha_inicio']));
                $d2 = date("m-d-Y", strtotime($projectData[0]['fecha_cierre']));
                ?>
                <div class="header flexAndSpaceDiv">
                    <h4 class="headerTitle">Gestión de Proyectos</h4>
                    <?php $pagina="projectsManagement"; $projectDetails=true; include 'topToolBar.php'; ?>
                </div>
                <div class="detailsContainer scroll">
                    <div class="detailsContainerTitle">
                        <div class="name">
                            <i><?php echo htmlspecialchars($projectData[0]['nombre'], ENT_QUOTES, 'UTF-8');?></i>
                        </div>
                        <div class="fechas">
                            <label class="fechaInicio">Fecha de inicio: <?php echo $d1?></label><br>
                            <label class="fechaCierre">Fecha de cierre: <?php echo $d2?></label>
                        </div>
                    </div>
                    <div class="detailsContainerDiv">
                        <div class="descripcion">
                            <h3>Descripción:</h3>
                            <i style="font-style: normal;"><?php echo htmlspecialchars($projectData[0]['descripción'], ENT_QUOTES, 'UTF-8');?></i>
                        </div>
                    </div>
                    <div class="detailsContainerDiv">
                        <div class="meta">
                            <h3>Meta:</h3>
                            <i style="font-style: normal;"><?php echo htmlspecialchars($projectData[0]['meta'], ENT_QUOTES, 'UTF-8');?></i>
                        </div>
                        <div class="objetivosGen">
                            <h3>Objetivos generales:</h3>
                        <?php   if (count($objectivesGData) != 0) {
                            for ($i = 0; $i < count($objectivesGData); $i++) {
                                $no = $i + 1;
                                echo '<a style="font-style: normal;">' . $no . ':  ' . htmlspecialchars($objectivesGData[$i]['contenido'], ENT_QUOTES, 'UTF-8') . '</a><br>';
                                $fl = true;
                            }
                        } else {
                            echo '<a style="font-style: normal;color:#9a9a9a;">Aún no se han registrado objetivos específicos</a><br>';
                        }?>
                        </div>
                    </div>
                    <div class="detailsContainerDiv">
                        <div class="objetivosEsp">
                            <h3>Objetivos específicos:</h3>
                        <?php   if (count($objectivesEData) != 0) {
                            for ($i = 0; $i < count($objectivesEData); $i++) {
                                $no = $i + 1;
                                echo '<a style="font-style: normal;">' . $no . ':  ' . htmlspecialchars($objectivesEData[$i]['contenido'], ENT_QUOTES, 'UTF-8') . '</a><br>';
                                $fl = true;
                            }
                        } else {
                            echo '<a style="font-style: normal;color:#9a9a9a;">Aún no se han registrado objetivos específicos</a><br>';
                        }?>
                        </div>
                    </div>
                    <div class="detailsContainerDiv">
                        <div class="integrantes">
                            <h3 >Integrantes:</h3><br>
                        <?php
                        if (count($integrantes) != 0) {
                            for ($i = 0; $i < count($integrantes); $i++) {
                                echo '<a style="font-style: normal;margin: 1rem;">Nombre: ' . htmlspecialchars($integrantes[$i]['nombre'], ENT_QUOTES, 'UTF-8') . '</a><br>';
                                echo '<a style="font-style: normal;margin: 1rem;">Departamento:  ' . htmlspecialchars($integrantes[$i]['departamento'], ENT_QUOTES, 'UTF-8') . '</a><br><br>';
                                $fl = true;
                            }
                        } else {
                            echo '<a style="font-style: normal;margin: 1rem;color:#9a9a9a;">Aún no se han registrado integrantes</a><br>';
                        }?>
                        </div>
                    </div>
                    <a id="returnToProjects" class="button redBtn" onclick="returnToProjectsList()" title="Lista de Proyectos"><i class="fa fa-arrow-circle-left"></i></a>
                    <div class="optionsDiv">
                        <a id="printDetails" class="button hide"><i class="fa fa-print" onclick="imprimirProyecto()" title="Imprimir"></i></a>
                        <a id="shareProject" class="button hide"><i class="fa fa-share-square-o" onclick="exportarProyecto()" title="Exportar"></i></a>
                        <a id="editProject" class="button hide"><i class="fa fa-edit" onclick="editarProyecto(<?php echo $projectID; ?>)" title="Editar"></i></a>
                        <a id="toggleDocumentOptions" class="button"><i class="fa fa-ellipsis-v" onclick="toggleDocumentOptions()" title="Opciones"></i></a>
                    </div>
                        
                </div>   <!-- Fin detailsContainer -->
                <script src="../js/validate.js"></script>
               <script src="../js/projectDetails.js"></script>









                <?php   
            } elseif (isset($_GET['editProject'])) {
                $projectId = $_GET['editProject'] ?? null;
                // Verificar si el ID es un entero
                if ($projectId === null || !filter_var($projectId, FILTER_VALIDATE_INT)) {
                    echo "
                    <script>
                        alert('ID de proyecto inválido.');
                        window.location.href = 'projectsManagement.php';                  
                    </script>";
                } else {
                    $cR = Crud::findRow("*", "tbl_proyectos", "id_proyecto", $projectId);
                    if(count($cR) != 0){
                    ?>
                <!-- EDITAR PROYECTO -->
                <div class="header">
                    <h4>Editar Proyecto</h4>
                </div>
                <div class="editContainer scroll">
                <div class="form-container">
                    <!-- <div class="title"><h4>Editar proyecto</h4></div>    -->
                    <form class="editProject-form" id="editProject-form" onsubmit="return updateBasicInfo()"  method="POST" autocomplete="off">
                        <div class="title mb1r"><h4>Datos generales:</h4></div>
                        <div class="fm-content">
                            <div class="section1">
                                <label class="bold" for="Fname">Nombre del proyecto:</label><br>
                                <input class="NameInput" type="text" name="Fname" id="Fname" placeholder="Nombre del Proyecto" 
                                title="Nombre del proyecto" value="<?php echo $cR[0]['nombre'] ?>"
                                oninput="resetField(this)"> 
                                <br>

                                <div class="deptoDiv">
                                <label for="deptoAsign">Departamento asignado:</label>
                                <select class="deptoAsign comboBox" id="deptoAssign" name="deptoAsign" style="margin-left:2rem;" onchange="updateDeptoInput(this)">
                                    <?php
                                    $Deptos = array();
                                    $query = "SELECT DISTINCT departamento FROM tbl_usuarios;";
                                    $Deptos = Crud::executeResultQuery($query);
                                    $currentDto = $cR[0]['departamentoAsignado'];

                                    if (count($Deptos) > 0) {
                                        if (Crud::isInArray($Deptos, $cR[0]['departamentoAsignado'])) {
                                            for ($i = 0; $i < count($Deptos); $i++) {
                                                foreach ($Deptos[$i] as $key => $value) {
                                                    echo '<option value="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '" ' . ($currentDto == $value ? 'selected' : '') . '>' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '</option>';
                                                }
                                            }
                                        } else {
                                            for ($i = 0; $i < count($Deptos); $i++) {
                                                foreach ($Deptos[$i] as $key => $value) {
                                                    echo '<option value="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '</option>';
                                                }
                                            }
                                            echo '<option value="' . count($Deptos) + 1 . '" selected>' . $currentDto . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                                <input type="hidden" id="eFdptoText" name="eFdptoText" value='<?php echo $cR[0]['departamentoAsignado'];?>'>
                                </div>
                            </div>
                            <div class="section2">
                                <div class="datesEditForm">
                                    <div id="fechaIni" class="fechaIni">
                                        <label class="bold" for="fechaInicio" id="date1Label">Fecha de inicio:</label><br>
                                        <div class="inline">
                                            <fieldset id="date1Fs">
                                            <span id="displayDate1" name="displayDate1"><?php echo $cR[0]['fecha_inicio']; ?></span>
                                            </fieldset>    
                                            
                                            <i id="inDt-edit" onclick="initialDate()" class="fa fa-edit button" title="Editar" tabindex="0"></i>
                                            <i id="inDt-save" onclick="saveDate1()" class="fa fa-check-square-o button hide" title="Guardar" tabindex="0"></i>
                                            <i id="inDt-cancel" onclick="initialDate()" class="fa fa-times button hide" title="Cancelar" tabindex="0"></i>
                                        </div>
                                        <input type="hidden" name="thisDate_inicio" id="thisDate_inicio" value="<?php echo $cR[0]['fecha_inicio']; ?>">
                                        <!-- datePicker -->
                                        <br>
                                        <div id="initDatePicker" class="initDatePicker hide">
                                            <?php $idUnico = "inicio";
                                            include 'datePicker.php'; ?>
                                        </div>
                                    </div> 
                                    <div id="fechaFin" class="fechaFin">
                                        <label class="bold spacer" for="fechaCierre" id="date2Label">Fecha de cierre:</label><br>
                                        <div class="inline">
                                            <fieldset id="date2Fs">
                                                <span id="displayDate2" name="displayDate2"><?php echo $cR[0]['fecha_cierre']; ?></span>
                                            </fieldset>
                                            <i id="fnDt-edit" onclick="finalDate()" class="fa fa-edit button" title="Editar" tabindex="0"></i>
                                            <i id="fnDt-save" onclick="saveDate2()" class="fa fa-check-square-o button hide" title="Guardar" tabindex="0"></i>
                                            <i id="fnDt-cancel" onclick="finalDate()" class="fa fa-times button hide" title="Cancelar" tabindex="0"></i>
                                        </div>
                                        <div class="hide mT-half" id="errorMessageDate2"><span class="invalidField">La fecha de cierre debe ser posterior a la fecha de inicio.</span></div>
                                        <input type="hidden" name="thisDate_cierre" id="thisDate_cierre" value="<?php echo $cR[0]['fecha_cierre']; ?>">
                                        <!-- datePicker -->
                                        <br>
                                        <div id="endDatePicker" class="endDatePicker hide">
                                            <?php $idUnico = "cierre";
                                            include 'datePicker.php'; ?>
                                        </div>
                                    </div> 
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="fm-content">
                            <div class="section1">

                                <label class="bold" for="Fdescription">Descripción del proyecto:</label><br>
                                <textarea type="text" name="Fdescription" id="Fdescription" placeholder="Descripción del Proyecto" title="Descripción del proyecto"
                                oninput='resetField(this);this.style.height = "";this.style.height = this.scrollHeight + "px"'><?php echo $cR[0]['descripción'] ?></textarea>
                                <br>
                                
                            </div>
                            <br>
                            <div class="section2 metaDiv">
                                <label class="bold" for="Fmeta">Meta del proyecto:</label><br>
                                <textarea type="text" name="Fmeta" id="Fmeta" placeholder="Introduzca la meta del proyecto" title="Meta del proyecto"
                                oninput='resetField(this);this.style.height = "";this.style.height = this.scrollHeight + "px"'><?php echo $cR[0]['meta'] ?></textarea>
                                
                            </div> 
                        </div> <!-- Fin de fm-content -->


                        <!-- ADMINISTRAR INTEGRANTES DEL PROYECTO -->
                        <div class="title mb1r"><h4>Datos de Integrantes:</h4></div>
                        <div class="fm-content specs">
                            <div class="section1">
                                <h4 class="mt1r ml1r" for="Fmeta">Integrantes del proyecto:</h4>
                                <div class="gestionIntegrantes"> 
                                    <div class="topTable flexAndSpaceDiv">
                                        
                                    </div>
                                    <div class="table">
                                        <table class="members-list">
                                            <thead>
                                                <tr>
                                                    <th class="rowNombre">Nombre de integrante</th>
                                                    <th class="rowCargo">Cargo</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="members-list-body">
                                            <?php
                                            $id = $_GET['editProject'];
                                            $p = array();
                                            $query = "SELECT usuarios.id_usuario, usuarios.nombre, integrantes.responsable 
                                            FROM tbl_integrantes integrantes JOIN tbl_usuarios usuarios 
                                            ON integrantes.id_usuario = usuarios.id_usuario WHERE integrantes.id_proyecto = ?";

                                            $p = Crud::executeResultQuery($query, [$id], "i");
                                            if (count($p) > 0) {
                                                for ($i = 0; $i < count($p); $i++) {
                                                    echo "<tr onclick='SelectThisRow(this, \"members-list-body\")'>";
                                                    foreach ($p[$i] as $key => $value) {
                                                        if ($p[$i]['responsable'] == $value) {
                                                            if ($value == 1) {
                                                                echo '<td>Responsable de proyecto</td>';
                                                            } else {
                                                                echo '<td>Colaborador</td>';
                                                            }
                                                        } elseif ($p[$i]['nombre'] == $value) {
                                                            echo '<td>' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '</td>';
                                                        }
                                                    }
                                                    $x = $p[$i]['id_usuario'];
                                                    $rowN = $i+1;
                                                    echo "<td><a class='fa fa-user-times tableIconBtn' row='$rowN'  title='Remover integrante' onclick='ConfirmDeleteMember($x, this)'></a></td>";
                                                    echo '</tr>';
                                                }
                                            } else {
                                                echo "<tr id='no-integrantes-row'><td colspan='3'>No se encontraron integrantes registrados.</td></tr>";
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                    </div> <!-- Fin de .table -->
                                </div> <!-- Fin de .gestionIntegrantes -->

                            </div>
                        <div class="section2">
                            <div class="manageMembersDiv">
                            <h4>Selecciona el integrante y haz click en 'añadir':</h4>

                                <div id="addMemberDiv" class="topTable flexAndSpaceDiv">
                            <i>Filtrar:</i>
                            <select name="filtroDepartamento" id="filtroDepartamento" class="comboBox" onchange="filtrarUsuariosPorDepartamento()">
                                <option value="noFilter">Todos los departamentos</option>
                                <?php
                                $deptos = Crud::getFiltersOptions('tbl_usuarios', 'departamento');
                                $selectedFilter = $_GET['filterDepto'] ?? '';

                                if (count($deptos) > 0) {
                                    foreach ($deptos as $index => $depto) {
                                        $value = htmlspecialchars($depto['departamento'], ENT_QUOTES, 'UTF-8');
                                        $selected = ($selectedFilter == $value) ? 'selected' : '';
                                        echo "<option value='$value' $selected>$value</option>";
                                    }
                                }
                                ?>
                            </select>
                            </div>
                            <div id="addMemberDiv" class="topTable flexAndSpaceDiv">
                                <i>Usuario:</i>
                                <select name="listaUsuariosDisponibles" id="listaUsuariosDisponibles" class="comboBox" onchange='resetField(this);'>
                                <option value="non">- Selecciona un usuario -</option>
                                    <?php
                                    $projectID = $_GET['editProject'];
                                    $existinUsers = Crud::executeResultQuery("SELECT id_usuario FROM tbl_integrantes WHERE id_proyecto = ?;", [$projectID], 'i');
                                    ;

                                    if (isset($_GET['filterDepto'])) {
                                        $deptoF = $_GET['filterDepto'];
                                        $users = Crud::executeResultQuery("SELECT id_usuario,nombre,departamento FROM tbl_usuarios WHERE departamento = ? AND rolUsuario = ?;", [$deptoF, 'EST','ss']);
                                    } else {
                                        $users = Crud::executeResultQuery("SELECT id_usuario,nombre,departamento FROM tbl_usuarios WHERE rolUsuario = 'EST';");
                                    }
                                    if (count($users) > 0) {
                                        for ($i = 0; $i < count($users); $i++) {
                                            $userID = $users[$i]['id_usuario'];
                                            $flag = false;
                                            for ($j = 0; $j < count($existinUsers); $j++) {
                                                if ($existinUsers[$j]['id_usuario'] === $users[$i]['id_usuario']) {
                                                    $flag = true;
                                                }
                                            }
                                            if ($flag === false) {
                                                $dto = htmlspecialchars($users[$i]['departamento'], ENT_QUOTES, 'UTF-8');
                                                $n = htmlspecialchars($users[$i]['nombre'], ENT_QUOTES, 'UTF-8');
                                                $usID = $users[$i]['id_usuario'];
                                                echo "<option value='$usID' data-depto='$dto'>$n</option>";
                                            }
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div id="addMemberDiv" class="topTable flexAndSpaceDiv">
                                <i>Rol:</i>
                                <select name="tipoMiembro" id="tipoMiembro" class="comboBox" onchange='resetField(this);'>
                                    <option value="non">- Selecciona un rol -</option>
                                    <option value="0">Colaborador</option>
                                    <option value="1">Responsable</option>
                                </select>
                            </div>
                            <input type="hidden" id="membersTableFlagAdd" name="membersTableFlagAdd" value="false">
                            <input type="hidden" id="addedMembers" name="addedMembers" value="">
                            <input type="hidden" id="membersTableFlagDel" name="membersTableFlagDel" value="false">
                            <input type="hidden" id="removedMembers" name="removedMembers" value="">
                            <a id="manageProjectsLink" class="button addRowBtn" onclick="agregarMiembro(<?php echo $_GET['editProject'];?>)" tabindex="0">Añadir</a>
                        </div>
                    </div>
                </div>

                    <!-- ADMINISTRAR OBJETIVOS DEL PROYECTO -->
                    <div class="title mb1r"><h4>Objetivos del proyecto:</h4></div>
                    <div class="fm-content">
                        <div class="section1">
                        <h4 class="mt1r ml1r">Objetivos generales:</h4>
                        <div class="gestionObjetivos"> 
                        <div class="table"> 
                        <table id="objectiveG-list" class="objectiveG-list">
                            <thead>
                                <tr>
                                    <!-- <th class="rowIdObj">No.</th> -->
                                    <th class="rowObjetivo">Descripcion de Objetivo</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="objectiveG-list-body" class="objectiveG-list-body">
                            <?php
                            $id = $_GET['editProject'];
                            $p = array();
                            $query = "SELECT id_objetivo, contenido 
                            FROM tbl_objetivos WHERE id_proyecto = ? AND tipo = ?";

                            $p = Crud::executeResultQuery($query, [$id, 'general'], "is");
                            if (count($p) > 0) {
                                for ($i = 0; $i < count($p); $i++) {
                                    echo '<tr value=' . $p[$i]['id_objetivo'] . ' onclick="SelectThisRow(this, \'objectiveG-list-body\')">';
                                    
                                    $value = $p[$i]['contenido'];
                                    $value = str_replace("\\n", "\n", $value); // Corrige los dobles slashes si están presentes
                                    $textoS = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                                    $textoF = nl2br($textoS);
                                    echo '<td class="descripcion">' . $textoF . '</td>';
                                        
                                    $objId = $i + 1;
                                    echo "<td class='ObjTableOptions'>
                                    <a class='fa fa-trash tableIconBtn' title='Eliminar objetivo' onclick=\"DeleteObjective(this,'general',$id,$objId)\"></a>
                                    <a class='fa fa-edit tableIconBtn mt1r' title='Editar objetivo' onclick=\"EditObjective(this)\"></a>
                                    <a id='saveChangesObj' class='fa fa-save tableIconBtn mt1r hide' title='Guardar cambios' onclick=\"SaveObjectiveChanges(this,'general',$id,$objId)\"></a>
                                    </td>";
                                    echo '</tr>';
                                }
                            } else {
                                echo "<tr id='no-objectiveG-row'><td colspan='3'>No se encontraron objetivos registrados.</td>";
                            }
                            ?>
                            </tbody>
                        </table>
                        </div> <!-- .table -->
                        </div> <!-- .gestionIntegrantes -->
                        </div> <!-- .section1 -->  
                        <div class="section2">
                            <h4>Describe el objetivo general y haz clic en 'añadir':</h4>
                            <textarea type="text" name="objetivoG" id="objetivoG" placeholder="Descripción del objetivo general" title="Descripción del objetivo general"></textarea>
                            
                            <input type="hidden" id="objGTableFlagAdd" name="objGTableFlagAdd" value="false">
                            <input type="hidden" id="addedObjG" name="addedObjG" value="">
                            <input type="hidden" id="objGTableFlagDel" name="objGTableFlagDel" value="false">
                            <input type="hidden" id="removedObjG" name="removedObjG" value="">
                            <input type="hidden" id="objGTableFlagUpd" name="objGTableFlagUpd" value="false">
                            <input type="hidden" id="updatedObjG" name="updatedObjG" value="">
                            <a id="addObjectiveGBtn" class="button addRowBtn" onclick="agregarObjetivo(<?php echo $_GET['editProject'];?>, 'general')" tabindex="0">Añadir</a>
                        </div>
                    </div>

                    <br>
                    <div class="fm-content">
                        <div class="section1">
                        <h4 class="mt1r ml1r">Objetivos específicos:</h4>
                        <div class="gestionObjetivos"> 
                        <div class="table"> 
                        <table id="objectiveE-list" class="objectiveE-list">
                            <thead>
                                <tr>
                                    <!-- <th class="rowIdObj">No.</th> -->
                                    <th class="rowObjetivo">Descripcion de Objetivo</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="objectiveE-list-body" class="objectiveE-list-body">
                            <?php
                            $id = $_GET['editProject'];
                            $p = array();
                            $query = "SELECT id_objetivo, contenido 
                            FROM tbl_objetivos WHERE id_proyecto = ? AND tipo = ?";

                            $p = Crud::executeResultQuery($query, [$id, 'especifico'], "is");
                            if (count($p) > 0) {
                                for ($i = 0; $i < count($p); $i++) {
                                    echo '<tr value=' . $p[$i]['id_objetivo'] . 'onclick="SelectThisRow(this, \'objectiveE-list-body\')">';
                                    foreach ($p[$i] as $key => $value) {
                                        if ($value != $p[$i]['id_objetivo']) {
                                            $textoS = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                                            $textoF = nl2br($textoS);
                                            echo '<td class="descripcion">' . $textoF . '</td>';
                                        }
                                    }
                                    $objId = $i + 1;
                                    echo "<td class='ObjTableOptions'>
                                    <a class='fa fa-trash tableIconBtn' title='Eliminar objetivo' onclick=\"DeleteObjective(this,'especifico',$id,$objId)\"></a>
                                    <a class='fa fa-edit tableIconBtn mt1r' title='Editar objetivo' onclick=\"EditObjective(this)\"></a>
                                    <a id='saveChangesObj' class='fa fa-save tableIconBtn mt1r hide' title='Guardar cambios' onclick=\"SaveObjectiveChanges(this,'especifico',$id,$objId)\"></a>
                                    </td>";
                                    echo '</tr>';
                                }
                            } else {
                                echo "<tr id='no-objectiveE-row'><td colspan='3'>No se encontraron objetivos registrados.</td>";
                            }
                            ?>
                            </tbody>
                        </table>
                        </div> <!-- .table -->
                        </div> <!-- .gestionIntegrantes -->
                        </div> <!-- .section1 -->  
                        <div class="section2">
                            <h4>Describe el objetivo específico y haz clic en 'añadir':</h4>
                            <textarea type="text" name="objetivoE" id="objetivoE" placeholder="Descripción del objetivo específico" title="Descripción del objetivo específico"></textarea>
                            
                            <input type="hidden" id="objETableFlagAdd" name="objETableFlagAdd" value="false">
                            <input type="hidden" id="addedObjE" name="addedObjE" value="">
                            <input type="hidden" id="objETableFlagDel" name="objETableFlagDel" value="false">
                            <input type="hidden" id="removedObjE" name="removedObjE" value="">
                            <input type="hidden" id="objETableFlagUpd" name="objETableFlagUpd" value="false">
                            <input type="hidden" id="updatedObjE" name="updatedObjE" value="">
                            <a id="addObjectiveEBtn" class="button addRowBtn" onclick="agregarObjetivo(<?php echo $_GET['editProject'];?>, 'especifico')" tabindex="0">Añadir</a>
                        </div>
                        
                        <div class="form-options">
                            <button disabled class="sumbit-editProject" id="sumbit-editProject" type="submit" tabindex="0">Guardar cambios</button>
                            <a id="cancel-editProject" class="close-editProject" onclick="return confirmCancel()" tabindex="0">Cancelar</a>
                        </div>
                    </div>
                    </form> <!-- Fin de edit-user-form -->


                </div> <!-- Fin de form-container -->   
                <script src="../js/validate.js"></script>
                <script src='../js/editProject.js'></script>

                </div>
                <?php }else{
                    echo "
                    <script>
                        alert('Búsqueda sin resultados. ID de proyecto no encontrado.');
                        window.location.href = 'projectsManagement.php';
                    </script>";
                }}


                
            } else if(isset($_GET['project-history'])){
                ?>
                <div class="header">
                    <h4>Historial de Proyectos</h4>
                </div>
                
                <div class="projectManagement projectHistory">
                    <!-- Filtros de busqueda -->
                    <div class="filterDiv closedFilterDiv">
                        <i id="filterProjectsList" class="fa fa-sliders button" title="Filtrar resultados"></i>
                        <div class="dropDownFilters hide ">
                            <h3>Filtrar por:</h3>
                            <div>
                            <select class="dropDownDeptoFilter comboBox mL-2r" id="dropDownDeptoFilter" name="dropDownDeptoFilter" onchange="FilterHistoryResults()">
                                <option value="noFilter">- Departamento asignado -</option>
                                <?php
                                    $Deptos = Crud::executeResultQuery("SELECT DISTINCT departamentoAsignado FROM tbl_proyectos WHERE estado = ? OR estado = ?", [0, 2], 'ii');
                                    if (count($Deptos) > 0) {
                                        for ($i = 0; $i < count($Deptos); $i++) {
                                            foreach ($Deptos[$i] as $key => $value) {
                                                echo '<option value="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '">' . $value . '</option>';
                                            }
                                        }
                                    }
                                ?>
                            </select>
                            </div>
                            <div>
                            <select class="dropDownDeptoFilter comboBox mL-2r" id="dropDownStateFilter" name="dropDownStateFilter" onchange="FilterHistoryResults(this)">
                                <option value="noFilter">- Estado del proyecto -</option>
                                <option value="concluded">Concluido</option>
                                <option value="canceled">Cancelado</option>
                            </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tabla de historial de proyectos -->
                    <div class="table">
                        <table class="project-list hoverTable">
                            <thead>
                                <tr>
                                    <th class="selectProjects"><input type="checkbox" id="selectAllBoxes"></th>
                                    <th class="rowName">Nombre del proyecto</th>
                                    <th class="rowDepto">Departamento asignado</th>
                                    <th class="rowFechaIni">Estado final</th>
                                    <th class="rowFechaFin">Progreso alcanzado</th>
                                    <th class="rowActions">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id='projects-list-body'>
                                <?php
                                    $query = "SELECT id_proyecto, nombre, departamentoAsignado, estado 
                                    FROM tbl_proyectos 
                                    WHERE estado = ? OR estado = ?
                                    ORDER BY id_proyecto DESC";
                          
                                    $params = [0, 2];
                                    $p = Crud::executeResultQuery($query, $params, 'ii');
                                    if (count($p) > 0) {
                                        for ($i = 0; $i < count($p); $i++) {
                                            $fl = false;
                                            $percentage = 0;
                                            $percentage = getActPercentage($p[$i]['id_proyecto']);   
                                            $currentId = $p[$i]['id_proyecto'];   
                                            echo '<tr onclick="SelectThisRow(this, \'projects-list-body\')" p-p="' . $percentage . '" p-i="' . $p[$i]['id_proyecto'] . '">';
                                            $count = 0;
                                            foreach ($p[$i] as $key => $value) {
                                                if ($count===0) {
                                                    echo "<td><input type='checkbox' class='project-checkbox' value='$value'></td>";
                                                } elseif ($count===3) {
                                                    $estados = [
                                                        0 => 'Cancelado',
                                                        1 => 'Abierto',
                                                        2 => 'Concluido'
                                                    ];
                                                    $color = (int)$value === 0 ? 'redStateBg' : 'greenStateBg';
                                                    echo '<td class="' . $color . '" >' . htmlspecialchars($estados[(int)$value], ENT_QUOTES, 'UTF-8') . '</td>';
                                                }else {
                                                    $cId = htmlspecialchars($p[$i]['id_proyecto']);
                                                    echo "<td>" . htmlspecialchars($value) . "</td>";
                                                } 
                                                $fl = true;
                                                $count++;
                                            }
                                            echo '<td>' . htmlspecialchars($percentage, ENT_QUOTES, 'UTF-8') . '%</td>';
                                            if ($fl == true) {
                                                    echo "<td>";                                                
                                                if($p[$i]['estado'] == 0){
                                                        echo "<a id='reactivate' class='fa fa-retweet button' title='Reactivar proyecto' onclick='reactivateProject(this)'></a>";
                                                    }
                                                    echo "<a class='fa fa-file-pdf-o button' title='Ver reporte de proyecto' onclick='seePojectReport(this)'></a>";
                                                    echo '</td></tr>';
                                            }
                                        }
                                    } else {
                                        echo "<tr><td colspan='6'>No se encontraron resultados.</td></tr>";
                                    }
                                ?>
                            </tbody>
                        </table>
                        <div class="pagination" id="pagination"></div>

                        <div id="projectSelected" class="selectedRowsOptions hide">
                            <select class="comboBox" name="actionSelected" id="actionSelected">
                                <option value="0"> - Seleccionar acción - </option>
                                <option value="delete">Eliminar proyecto(s) permanentemente</option>
                            </select>
                            <a id="applyAction" title="Aplicar acción a los proyectos seleccionados" class="button apply normalBtn">Aplicar</a>
                            <a id="applyAction2" title="Aplicar acción a los proyectos seleccionados" class="button apply shortBtn fa fa-chevron-right"></a>
                        </div>

                    </div> <!-- Fin de table -->   
                    <a id="returnToProjects" class="button redBtn" onclick="returnToProjectsList()" title="Regresar"><i class="fa fa-arrow-circle-left"></i></a>
                </div>
               
                <script src="../js/projectHistory.js"></script>
                <script src="../js/tablePagination.js"></script>
                <?php
            } else { 
                ?>
                <div class="header">
                    <h4>Gestión de Proyectos</h4>
                </div>

                <div class="projectManagement">


                <!-- Filtros de busqueda -->
                <div class="filterDiv closedFilterDiv">
                    <i id="historialProyectos" class="fa fa-history button" title="Historial de proyectos" style="margin-right:.5rem;" onclick="projectHistory()"></i>
                    <i id="filterProjectsList" class="fa fa-sliders button" title="Filtrar resultados"></i>
                    <div class="dropDownFilter1 hide ">
                        <label for="filtersForRol">Departamento asignado:</label>
                        <select class="dropDownDeptoFilter comboBox mL-2r" id="dropDownDeptoFilter" name="dropDownDeptoFilter" onchange="FilterResults(this)">
                            <option value="noFilter"></option>
                            <?php
                                $Deptos = Crud::getFiltersOptions('tbl_proyectos', 'departamentoAsignado');
                            if (count($Deptos) > 0) {
                                for ($i = 0; $i < count($Deptos); $i++) {
                                    foreach ($Deptos[$i] as $key => $value) {
                                        echo '<option value=' . $i . '>' . $value . '</option>';
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
                    <i id="searchProyecto" class="fa fa-search button hide" title="Buscar"></i>
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
                                <th class='rowActions'>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id='projects-list-body'>
                            <?php
                            if (isset($_GET['search']) || isset($_GET['filterDto'])) {
                                if (isset($_GET['search'])) {
                                    $p = Crud::selectProjectSearchData("id_proyecto,nombre,departamentoAsignado,fecha_inicio,fecha_cierre", "tbl_proyectos", "id_proyecto", "DESC", $_GET['search']);
                                } else {
                                    $query = "SELECT id_proyecto,nombre,departamentoAsignado,fecha_inicio,fecha_cierre FROM tbl_proyectos WHERE departamentoAsignado = ? AND estado = ?";
                                    $params = [$_GET['filterDto'],1];
                                    $p = Crud::executeResultQuery($query, $params, 'ii');
                                }
                                if (!empty($p) && count($p) > 0) {
                                    for ($i = 0; $i < count($p); $i++) {
                                        $fl = false;
                                        $percentage = 0;
                                        $percentage = getActPercentage($p[$i]['id_proyecto']);      
                                        echo '<tr onclick="SelectThisRow(this, \'projects-list-body\')" p-p="' . $percentage . '" p-i="' . $p[$i]['id_proyecto'] . '">';
                                        $count = 0;
                                        $currentId = 0;
                                        foreach ($p[$i] as $key => $value) {
                                            if ($count == 0) {
                                                $currentId = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                                                echo "<td><input type='checkbox' class='project-checkbox' value='$currentId'></td>";
                                            } 
                                            elseif ($count === 1) {
                                                $cId = htmlspecialchars($p[$i]['id_proyecto']);
                                                echo "<td><i class='blueText' onclick=seeProjectAccount('$cId') title='Ver detalles de proyecto'>" . htmlspecialchars($value) . "</i></td>";
                                            } elseif($count === 5){
                                                $estados = [
                                                    0 => 'Cancelado',
                                                    1 => 'Abierto',
                                                    2 => 'Concluido'
                                                ];
                                                echo '<td>' . htmlspecialchars($estados[(int)$value], ENT_QUOTES, 'UTF-8') . '</td>';
                                            }else {
                                                echo '<td>' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '</td>';
                                            }
                                            $fl = true;
                                            $count++;
                                        }
                                        if ($fl == true) {
                                            echo "<td>";
                                            echo "<a id='editProjectBtn' class='fa fa-edit button' title='Editar proyecto' href='projectsManagement.php?editProject=" . $p[$i]['id_proyecto'] . "'></a>";
                                            
                                            if((int)$percentage == 100){
                                                echo "<a id='$currentId' class='fa fa-check-square button' title='Cerrar proyecto' onclick='concluirProyecto(this)'></a>";
                                            }else{
                                                echo "<a id='$currentId' class='fa fa-close button' title='Cerrar proyecto' onclick='cerrarProyecto(this)'></a>";
                                            }
                                            echo "</td>";
                                            echo '</tr>';
                                        }
                                    }
                                } else {
                                    echo "<tr><td></td><td colspan='5'>No se encontraron proyectos registrados.</td></tr>";
                                }
                            } else {
                                
                                $query = "SELECT id_proyecto,nombre,departamentoAsignado,fecha_inicio,fecha_cierre FROM tbl_proyectos WHERE estado = ? ORDER BY id_proyecto DESC";
                                $params = [1];
                                $p = Crud::executeResultQuery($query, $params, 'i');
                                if (count($p) > 0) {
                                    for ($i = 0; $i < count($p); $i++) {
                                        $fl = false;
                                        $percentage = 0;
                                        $percentage = getActPercentage($p[$i]['id_proyecto']);   
                                        $currentId = $p[$i]['id_proyecto'];   
                                        echo '<tr onclick="SelectThisRow(this, \'projects-list-body\')" p-p="' . $percentage . '" p-i="' . $p[$i]['id_proyecto'] . '">';
                                        foreach ($p[$i] as $key => $value) {
                                            if ($value === $p[$i]['id_proyecto']) {
                                                echo "<td><input type='checkbox' class='project-checkbox' value='$value'></td>";
                                            } elseif ($value === $p[$i]['nombre']) {
                                                $cId = htmlspecialchars($p[$i]['id_proyecto']);
                                                echo "<td><i class='blueText' onclick=seeProjectAccount('$cId') title='Ver detalles de proyecto'>" . htmlspecialchars($value) . "</i></td>";
                                            } else {
                                                echo '<td>' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '</td>';
                                            }
                                            $fl = true;
                                        }
                                        if ($fl == true) {
                                            echo "<td>";
                                            echo "<a id='editProjectBtn' class='fa fa-edit button' title='Editar proyecto' href='projectsManagement.php?editProject=" . $p[$i]['id_proyecto'] . "'></a>";
                                            
                                        if((int)$percentage == 100){
                                                echo "<a id='$currentId' class='fa fa-check-square button' title='Cerrar proyecto' onclick='concluirProyecto(this)'></a>";
                                            }else{
                                                echo "<a id='$currentId' class='fa fa-close button' title='Cerrar proyecto' onclick='cerrarProyecto(this)'></a>";
                                            }
                                            echo "</td>";
                                            echo '</tr>';
                                        }
                                    }
                                } else {
                                    echo "<tr><td colspan='6'>No se encontraron resultados.</td></tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                    <div class="pagination" id="pagination"></div>

                </div> <!-- Fin de table -->      
         
                <!-- Boton de añadir proyecto -->
                <div class="addBtn"><a id="showProjectFormBtn" title="Crear proyecto" class="fa fa-plus add-project-btn button" style="margin-top:0;"></a></div>

                <div id="projectSelected" class="selectedRowsOptions hide">
                    <select class="comboBox" name="actionSelected" id="actionSelected">
                        <option value="0"> - Seleccionar acción - </option>
                        <option value="endProject">Cerrar proyecto(s)</option>
                    </select>
                    <a id="applyAction" title="Aplicar acción a los proyectos seleccionadas" class="button apply normalBtn">Aplicar</a>
                    <a id="applyAction2" title="Aplicar acción a los proyectos seleccionadas" class="button apply shortBtn fa fa-chevron-right"></a>
                </div>

                <!-- Formulario de alta de proyecto -->
                <form class="addProject-form hide scroll" id="addProject-form" onsubmit="return submitNewProject()" method="POST" autocomplete="on">
                <div class="form-bg">
                    <div class="form-container">
                        <div class="fm-content">
                            <div class="title"><h4>Agregar proyecto:</h4></div> <br> 
                            <label for="Pname">Nombre del proyecto:</label>
                            <input class="NameInput" type="text" name="Pname" id="Fname" placeholder="Nombre del Proyecto" 
                            title="Nombre del proyecto" oninput="resetField(this)"> 
                            <br>
                            <div class="dates">
                                <div class="fechaInicio" id="fechaInicioDiv">
                                    <label for="fechaInicio">Fecha de inicio:</label><br>
                                    <!-- datePicker -->
                                    <?php $idUnico = "inicio";
                                    include 'datePicker.php'; ?>
                                </div> 
                                
                                <div class="fechaCierre" id="fechaCierreDiv">
                                    <label for="fechaCierre">Fecha de cierre:</label><br>
                                    <!-- datePicker -->
                                    <?php $idUnico = "cierre";
                                    include 'datePicker.php'; ?>
                                </div> 
                            </div>
                            <br>
                            <label for="dropDownDepto">Departamento asignado:</label>
                            <!-- <br> -->
                            <select class="dropDownDepto comboBox" id="dropDownDepto" name="dropDownDepto" style="margin-left:2rem;" onchange='changeDepto()'>
                            <?php
                                $Deptos = Crud::getFiltersOptions('tbl_usuarios', 'departamento');
                            if (count($Deptos) > 0) {
                                for ($i = 0; $i < count($Deptos); $i++) {
                                    foreach ($Deptos[$i] as $key => $value) {
                                        $i==0 ? $selected = "selected" : $selected = ""; 
                                        echo '<option value=' . $i .' '.$selected.'>' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '</option>';
                                    }
                                }
                            }
                            ?>
                            <option value="other">Otro</option>
                            </select>
                            <div id="newDepto" class="newDepto hide">
                            <input class="NameInput" type="text" name="newDepto" id="newDeptoInput" placeholder="Departamento a cargo del proyecto..." 
                            title="Departamento asignado"  oninput='resetField(this)'>
                            </div>
                            <div class="midSpacers"><br></div>
                            <label for="Fdescription">Descripción del proyecto:</label>
                            <textarea type="text" name="Fdescription" id="Fdescription" placeholder="Descripción del Proyecto" title="Descripción del proyecto"
                            oninput='resetField(this);this.style.height = "";this.style.height = this.scrollHeight + "px"'></textarea> 
                            <br>
                            
                            <label for="Fmeta">Meta del proyecto:</label>
                            <textarea type="text" name="Fmeta" id="Fmeta" placeholder="Introduzca la meta del proyecto" title="Meta del proyecto"
                            oninput='resetField(this);this.style.height = "";this.style.height = this.scrollHeight + "px"'></textarea> 
                            <br>
                            
                            <div class="form-options">
                            <a id="cancel-AddProject" class="close-AddProject" onclick="cerrarFormulario()">Cancelar</a>
                            <button name="sumbit-AddProject" class="sumbit-AddProject enabled" id="sumbit-AddProject" type="submit">Crear proyecto</button>
                            </div>
                        </div>
                    </div>
                </div> <!-- Fin de form-container --> 
                </form> <!-- Fin de project-form -->

                </div>
                <script src="../js/validate.js"></script>
                <script src="../js/tablePagination.js"></script>
                <script src="../js/projectMng.js"></script>
                                <?php
            }
            ?>
        </div>

    </div> <!-- Fin de container -->
    <?php if(isset($_GET['consultFailed'])){ echo "<script>alert('Hubo un error al realizar los cambios en la BD.\nIntenta de nuevo.)</script>"; }?>
    <script src="../js/init.js"></script>
</body>
</html>

        <?php
    } else {
        echo "<script>
        alert('No cuentas con los permisos necesarios para acceder a esta página.');
        window.location.href = 'dashboard.php';
    </script>";
        exit();
    }
} else {
    header("Location: dashboard.php");
    exit();
}
?>