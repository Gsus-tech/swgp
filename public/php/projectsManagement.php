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
            }
            ?>

            <div class="header">
                <h4>Gestión de Proyectos</h4>
            </div>

            <div class="projectManagement">


                <!-- Filtros de busqueda -->
                <div class="filterDiv closedFilterDiv">
                    <i id="historialProyectos" class="fa fa-history button" title="Historial de proyectos" style="margin-right:.5rem;"></i>
                    <i id="filterProjectsList" class="fa fa-sliders button" title="Filtrar resultados"></i>
                    <button id="filtroFecha" class="filtroFecha button hide" disabled>Por porcentaje de avance</button>
                    <div class="dropDownFilter1 hide ">
                        <label for="filtersForRol">Departamento asignado:</label>
                        <select class="dropDownDeptoFilter" id="dropDownDeptoFilter" name="dropDownDeptoFilter" style="margin-left:2rem;">
                            <option value="noFilter"></option>
                            <?php
                            // require("../controller/generalCRUD.php");
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
                                                <a id="editProjectBtn" class="fa fa-edit button" title="Editar proyecto" href="projectMng/editProject.php?id=<?php echo urlencode($currentId); ?>"></a>
                                                <a id="closeProject" class="fa fa-close button" title="Cerrar proyecto" href="projectMng/manageProjects.php?cerrar=<?php echo urlencode($currentId); ?>"></a>
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
                                            if($value === $p[$i][0]){
                                                echo "<td><input type='checkbox' class='project-checkbox' value='$value'></td>";
                                            }else if($value === $p[$i][1]){
                                                $cId = htmlspecialchars($p[$i][0]);
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
                                            <a id="editProjectBtn"class="fa fa-edit button" title="Editar proyecto" href="projectMng/editProject.php?id=<?php echo $p[$i][0];?>"></a>
                                            <a id="closeProject" class="fa fa-close button" title="Cerrar proyecto" href="projectMng/manageProjects.php?cerrar=<?php echo $p[$i][0];?>"></a>
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
                            <select class="dropDownDepto" id="dropDownDepto" name="dropDownDepto" style="margin-left:2rem;">
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
                            <a href="projectsManagement.php" id="cancel-AddProject" class="close-AddProject" onclick="return confirmCancel()">Cancelar</a>
                                <script>
                                    function confirmCancel() {
                                        const projectName = document.getElementById("Fname").value;
                                        const description = document.getElementById("Fdescription").value;
                                        const metas = document.getElementById("Fmeta").value;

                                        if (projectName !== '' || description !== '' || metas !== '') {
                                             return confirm("¿Estás seguro de que deseas cancelar? Se perderá la información ingresada.");
                                        }
                                        return true;
                                    }
                                </script>
                                <button disabled name="sumbit-AddProject" class="sumbit-AddProject" id="sumbit-AddProject" type="submit">Crear proyecto</button>
                            </div>
                            <br>
                            <button disabled name="sumbit-AddProject-obj" class="sumbit-AddProject-obj" id="sumbit-AddProject-obj" type="submit">Crear e ir a definición de objetivos <span class="fa fa-arrow-right"></span></button>
                           
                        </div>
                    </div>
                </div> <!-- Fin de form-container --> 
                </form> <!-- Fin de project-form -->

            </div>
        </div>

    </div> <!-- Fin de container -->

    <script src="../js/projectMng.js"></script>
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

