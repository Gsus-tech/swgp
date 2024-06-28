<?php
session_start();

if(isset($_SESSION['rol']) && isset($_SESSION['nombre'])) {
    if($_SESSION['rol']==='ADM'){
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv=»X-UA-Compatible» content=»IE-edge» charset=UTF-8″>
    <meta name="viewport" content="width=device-width" initial-scale=1.0″>
    <title>SWGP - Panel de inicio</title>
    <link rel="stylesheet" href="../assets/font-awesome-4.7.0/css/font-awesome.min.css">    
    <link rel="stylesheet" href="../css/style-dash.css">
    <link rel="stylesheet" href="../css/userMan_style.css">
    <link rel="stylesheet" href="../css/table-style.css">
</head>
<body class="short">
    <div class="container"> 
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <div class="main">
        <?php
        require("../controller/generalCRUD.php");
        if (isset($_GET['error'])) {
            $errorMsg = urldecode($_GET['error']);
            echo "<script>alert('Codigo de error capturado: $errorMsg')</script>";
        }

        if(isset($_GET['detailsId'])){
            $id=$_GET['detailsId'];
            $infoAccount = crud::findRow('id_usuario,rolUsuario,nombre,correo,departamento', 'tbl_usuarios', 'id_usuario', $id);
            ?>
            <div class="accountDetailsDiv scroll">
                <div><h3 for="name">Detalles de cuenta:</h2></div>
                <br>
                <div class="generalInfo">
                    <div class="generalInfo1">
                        <h4 for="name">Nombre de usuario:</h4>
                        <input disabled class ="name input" name="name" value=<?php echo $infoAccount[0][2];?>>
                        <br>
                        <h4 for="mail">Correo:</h4>
                        <input disabled class ="mail input" name="mail" value=<?php echo $infoAccount[0][3];?>>
                    </div>
                    <div class="generalInfo2">
                        <h4 for="userRol">Rol de usuario:</h4>
                        <?php if($infoAccount[0][1]=='ADM'){$type='Superusuario';}
                            elseif($infoAccount[0][1]=='SAD'){$type='Administrador';}
                            else{$type='Estándar';}
                        ?>
                        <input disabled name="userRol" class="input" value="<?php echo $type;?>">
                        <br>
                        <h4 for="name">Departamento:</h4>
                        <input disabled class="input" name="name" value="<?php echo $infoAccount[0][4];?>">
                    </div>
                </div>
                <br>
                <div class="projectsInfo">
                    <h3>Proyectos asignados</h3>
                    <?php
                    $projectParticipation = crud::executeResultQuery("SELECT proyecto.nombre,proyecto.fecha_inicio,proyecto.fecha_cierre, integrante.responsable FROM tbl_proyectos proyecto JOIN tbl_integrantes integrante ON proyecto.id_proyecto = integrante.id_proyecto WHERE integrante.id_usuario=$id;");
                        $divFlag = true;
                        $flag = true;
                        $count = 0;
                        $count1 = 0;
                        if(count($projectParticipation) >0){
                        for($i=0;$i<count($projectParticipation);$i++){ 
                            if($count==4){$flag=true; $count=0;}
                            if($count1==2){$divFlag=true; $count1=0;}
                            if($flag==true){echo "<div class='twoGroups'>"; $flag=false;}
                            if($divFlag==true){echo "<div class='rowOfprojects'>"; $divFlag=false;}
                            ?>
                            <div class="project <?php if($count1==1){ echo "lfMg";} ?>">
                                <h4><?php echo $projectParticipation[$i][0] ?></h4>
                                
                                <label for="dateStart">Fecha de inicio: <i><?php echo $projectParticipation[$i][1] ?></i></label>
                                <br>
                                <label for="dateStart">Fecha de cierre: <i><?php echo $projectParticipation[$i][2] ?></i></label>
                                <h4>Rol: <label><?php if($projectParticipation[$i][3]===true){echo "Responsable de proyecto";}else{echo "Colaborador";} ?></label></h4>
                            </div>  
                            <?php
                            if($count1==1 || $i==count($projectParticipation)-1){echo "</div>"; }
                            if($count==3 || $i==count($projectParticipation)-1){echo "</div>"; }
                            $count++;
                            $count1++;
                        }
                    }else{
                        echo"<br><a for='' class='lfMg'>- No se encontraron proyectos asignados a este usuario -</a><br><br>";
                    }
                    ?>

                </div>

                <a href="userManagement.php" class="button close-AddUser" id="returnD"><i class="fa fa-arrow-circle-left mr-half"></i>Regresar</a>
            </div>
        
        </div> <!-- Fin de main -->
    </div> <!-- Fin de container -->
    <script src="../js/init.js"></script>
    
</body>
</html>
            <?php
        }else{
        ?>
            <div class="header"> 
                <h4>Gestión de usuarios</h4>
            </div>
            <!-- Contenido de gestion de usuarios -->
            <div class="userManagement">            
            
                <!-- Filtros de busqueda -->
                <?php 
                $filterFlag = (isset($_GET['filterRol']) || isset($_GET['filterDto'])) ? true : false; 
                ?>
                <div class="closedFilterDiv filterDiv">
                    <i id="filterUserList" class="fa fa-sliders button" title="Filtrar resultados"></i>
                    <div class="dropDownFilter1 hide">
                    <label for="filtersForRol">Por rol de usuario:</label>
                    <?php 
                        if(isset($_GET['filterRol'])){
                            $selected = $_GET['filterRol'];
                        }else{ $selected = ""; }
                    ?>
                    <select class="dropDownFilter" id="filtersForRol" name="filtersForRol">
                        <option value="noFilter"></option>
                        <option value="EST" <?php $r = ($selected == "EST") ? "selected" : ""; echo $r; ?>>Usuario Estándar</option>
                        <option value="SAD" <?php $r = ($selected == "SAD") ? "selected" : ""; echo $r; ?>>Usuario Administrador</option>
                        <option value="ADM" <?php $r = ($selected == "ADM") ? "selected" : ""; echo $r; ?>>Super-Usuario</option>
                    </select>
                    </div>
                    <div class="dropDownFilter2 hide">
                    <label for="filtersForRol">Por departamento:</label>
                    <select class="dropDownFilter" id="filtersForDto" name="filtersForDto">
                    <option value="noFilter"></option>
                        <?php
                        $filters = crud::getFiltersOptions('tbl_usuarios', 'departamento');
                        if(count($filters)>0){
                            if(isset($_GET['filterDto'])){
                                $selected = $_GET['filterDto'];
                            }else{ $selected = ""; }
                            for($i=0;$i<count($filters);$i++){
                                foreach($filters[$i] as $key=>$value){
                                    $r = ($selected == $value) ? "selected" : "";
                                    echo '<option value=dto'.$i.' '.$r.'>'.$value.'</option>';
                                }
                            }
                        }else{
                            echo '<option>No hay departamentos registrados</option>';
                        }
                        ?>
                    </select>
                    </div>
                </div>

                <!-- Busqueda de cuentas -->
                <div class="nav-buttons">
                    <i id="searchUser" class="fa fa-search button" title="Buscar cuenta"></i>
                    <input type="text" id="search-bar" class="search-bar input hide" placeholder="Buscar...">
                    <i id="searchAccount" class="fa fa-search button hide" title="Buscar" href="userManagement.php?search="></i>
                </div>
            
                <!-- Tabla de usuarios -->
            <div class="table">
                <table class="users-list">
                    <thead>
                        <tr>
                            <th class="rowID">ID</th>
                            <th class="rowRol">Rol de usuario</th>
                            <th class="rowName">Nombre</th>
                            <th class="rowMail">Correo Electrónico</th>
                            <th class="rowDpto">Departamento</th>
                            <th class="rowActions">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="tableContent">
                        <?php
                        if (isset($_GET['search']) || isset($_GET['filterRol']) || isset($_GET['filterDto'])) {
                            if(isset($_GET['search'])){
                                $p = crud::selectUserSearchData('id_usuario,rolUsuario,nombre,correo,departamento', 'tbl_usuarios', "id_usuario", "DESC", $_GET['search']);
                            }
                            elseif(isset($_GET['filterRol']) && isset($_GET['filterDto'])){
                                $p = crud::findRows2Condition('id_usuario,rolUsuario,nombre,correo,departamento', 'tbl_usuarios', 'rolUsuario', $_GET['filterRol'], 'departamento', $_GET['filterDto']);
                            }
                            elseif(isset($_GET['filterRol'])){
                                $p = crud::findRows('id_usuario,rolUsuario,nombre,correo,departamento', 'tbl_usuarios', 'rolUsuario', $_GET['filterRol']);
                            }
                            else{
                                $p = crud::findRows('id_usuario,rolUsuario,nombre,correo,departamento', 'tbl_usuarios', 'departamento', $_GET['filterDto']);
                            }

                            if(!empty($p) && count($p) > 0) {
                                for ($i = 0; $i < count($p); $i++) {
                                    $fl = false;
                                    echo '<tr>';
                                    foreach ($p[$i] as $key => $value) {
                                        if (isset($p[$i]['id_usuario']) && $p[$i]['id_usuario'] != $_SESSION['id']) {
                                            if ($value == 'ADM') { 
                                                echo '<td>Super-Usuario</td>'; 
                                            } else if ($value == 'SAD') { 
                                                echo '<td>Administrador</td>'; 
                                            } else if ($value == 'EST') { 
                                                echo '<td>Estándar</td>'; 
                                            } else {
                                                echo '<td>' . htmlspecialchars($value) . '</td>';
                                            }
                                            $fl = true;
                                        }
                                    }
                                    if ($fl == true) {
                                        $userId = htmlspecialchars($p[$i]['id_usuario']);
                                        ?>
                                        <td>
                                            <a id="seeUser" class="fa fa-eye button" title="Ver cuenta" onclick="seeUserAccount(<?php echo $userId; ?>)"></a>
                                            <a id="editUserBtn" class="fa fa-edit button" title="Editar cuenta" onclick="editUserAccount(<?php echo $userId; ?>)"></a>
                                            <a id="deleteUserBtn" class="fa fa-trash button" title="Eliminar cuenta" onclick="confirmDelete(<?php echo $userId; ?>)"></a>
                                        </td>
                                        <?php
                                        echo '</tr>';
                                    }
                                }
                            } else {
                                echo "<tr><td colspan='6'>No se encontraron resultados.</td></tr>";
                            }
                        } else {
                            $p = crud::selectData('id_usuario,rolUsuario,nombre,correo,departamento', 'tbl_usuarios', "id_usuario", "DESC");

                            if(count($p)>0){
                                for($i=0;$i<count($p);$i++){
                                    $fl = false;
                                    echo '<tr>';
                                    foreach($p[$i] as $key=>$value){
                                        if($p[$i][0]!=$_SESSION['id']){
                                            if($value=='ADM'){ echo '<td>Super-Usuario</td>'; }      
                                            else if($value=='SAD'){ echo '<td>Administrador</td>'; }      
                                            else if($value=='EST'){ echo '<td>Estándar</td>'; }      
                                            else{echo '<td>'.$value.'</td>';}
                                            $fl = true;
                                        }
                                    }
                                    if($fl==true){
                                        $userId = htmlspecialchars($p[$i][0]);
                                    ?>
                                    <td>
                                        <a id="seeUser"class="fa fa-eye button" title="Ver cuenta" onclick="seeUserAccount(<?php echo $userId; ?>)"></a>
                                        <a id="editUserBtn"class="fa fa-edit button" title="Editar cuenta" onclick="editUserAccount(<?php echo $userId; ?>)"></a>
                                        <a id="deleteUserBtn" class="fa fa-trash button" title="Eliminar cuenta" onclick="confirmDelete(<?php echo $userId; ?>)"></a>
                                    </td>
                                    <?php
                                    echo '</tr>';
                                    }
                                }
                            }else {
                                echo "<tr><td colspan='6'>No se encontraron resultados.</td></tr>";
                            }
                        }
                        
                        ?>
                    </tbody>
                </table>
                </div> <!-- Fin de table -->               
           
            

                <div class="addBtn"><a id="showUserFormBtn" title="Crear nueva cuenta de usuario" class="fa fa-user-plus add-user-btn"></a></div>

                <!-- Formulario de alta de usuario -->
                <form class="addUser-form hide" id="addUser-form" action="../controller/userManager.php?addUser=true" method="POST" autocomplete="on">
                <div class="form-bg">
                    <div class="form-container">
                        <div class="fm-content">
                            <div class="title"><h4>Agregar usuario:</h4></div>                            <!-- <label for="name">Nombre:</label> -->
                            <input type="text" name="Fname" id="Fname" placeholder="Nombre de usuario" title="Introducir nombre de usuario" required oninvalid="this.setCustomValidity('El campo nombre es necesario')" oninput="this.setCustomValidity('')"> 
                            <br>

                            <label for="dropDownDepto">Departamento:</label>
                            <select class="dropDownDepto" id="dropDownDepto" name="dropDownDepto" style="margin-left:2rem;">
                            <?php
                                $Deptos = crud::getFiltersOptions('tbl_usuarios', 'departamento');
                                if(count($Deptos)>0){
                                    for($i=0;$i<count($Deptos);$i++){
                                        foreach($Deptos[$i] as $key=>$value){
                                            $selected = ($i == 1) ? 'selected' : '';
                                            echo '<option value='.$i.' '.$selected.'>'.$value.'</option>';
                                        }
                                    }
                                }
                            ?>
                            <option value="other">Otro</option>
                            </select>
                            <div id="Fdpto" class="Fdpto hide">
                                <input type="text" name="Fdpto" id="Fdepto" placeholder="Nuevo departamento" title="Introducir departamento del usuario" required
                                oninvalid="this.setCustomValidity('El campo departamento es necesario')" oninput="this.setCustomValidity('')"> 
                            </div>

                            <!-- <label for="mail">Correo:</label> -->
                            <input type="email" name="Fmail" id="Fmail" placeholder="Correo" title="Introducir correo del usuario" required
                            oninvalid="this.setCustomValidity('Formato de correo incorrecto')" oninput="this.setCustomValidity('')"> 
                            <br>
                            <!-- <label for="dpto">Contraseña:</label> -->
                            <input type="password" class="passInput" name="Fpassword" id="Fpassword" title="Introducir contraseña de la cuenta" placeholder="Contraseña" autocomplete="false" required
                            oninvalid="this.setCustomValidity('Define una contraseña para la cuenta de usuario')" oninput="this.setCustomValidity('')">
                            <i id="passwordVisibility" name="passwordVisibility" class="fa fa-lock button" ></i> 
                            <br>
                            <!-- <label for="dpto">Confirmar contraseña:</label> -->
                            <input type="password" class="passwordConField" name="FpasswordCon" id="FpasswordCon" title="Confirmar contraseña" placeholder="Confirmar contraseña" autocomplete="false" required
                            oninvalid="this.setCustomValidity('Confirma la contraseña de la cuenta')" oninput="this.setCustomValidity('')"> 
                            <br>
                            <label for="comboBoxUserType"> Permisos de usuario: </label>
                            <select class="comboBoxUserType" id="FcmbBox" name="comboBoxUserType">
                                <option value="EST">Usuario Estándar</option>
                                <option value="SAD">Usuario Administrador</option>
                                <option value="ADM">Super-Usuario</option>
                            </select>
                            <div class="form-options">
                                <button disabled class="sumbit-AddUser" id="sumbit-AddUser" type="submit">Agregar usuario</button>
                                <a href="userManagement.php" id="cancel-Adduser" class="close-AddUser" onclick="return confirmCancel()">Cancelar</a>                                
                                
                            </div>
                        </div>
                    </div>
                </div> <!-- Fin de form-container --> 
                </form> <!-- Fin de user-form -->
    
                <?php 
                if(isset($_GET['editId'])){ 
                    $cR = crud::findRow('id_usuario,rolUsuario,nombre,correo,departamento', 'tbl_usuarios', "id_usuario", $_GET['editId']);
                    ?>
                <form class="addUser-form" id="editUser-form" action="../controller/userManager.php?updateUser=true" method="POST" autocomplete="on">
                <div class="form-bg">
                    <div class="form-container">
                        <div class="fm-content">
                            <div class="title"><h4>Editar usuario:</h4></div>                            <!-- <label for="name">Nombre:</label> -->
                            <input type="hidden" name="EditThisID" value="<?php echo $_GET['editId']?>">
                            <input class="input" type="text" name="Ename" id="Ename" value="<?php echo $cR[0][2] ?>" placeholder="Nombre de usuario" title="Introducir nombre de usuario" required oninvalid="this.setCustomValidity('El campo nombre es necesario')" oninput="this.setCustomValidity('')"> 
                            <br>
                            
                            <label for="dropDownDepto">Departamento:</label>
                            <select class="dropDownDepto" id="edropDownDepto" name="dropDownDepto" style="margin-left:2rem;">
                            <?php
                                $Deptos = crud::getFiltersOptions('tbl_usuarios', 'departamento');
                                if(count($Deptos)>0){
                                    for($i=0;$i<count($Deptos);$i++){
                                        foreach($Deptos[$i] as $key=>$value){
                                            $selected = ($value == $cR[0][4]) ? 'selected' : '';
                                            echo '<option value='.$i.' '.$selected.'>'.$value.'</option>';
                                        }
                                    }
                                }
                            ?>
                            <option value="other">Otro</option>
                            </select>
                            <div id="Edepto" class="Fdpto hide">
                                <input type="text" name="eFdpto" id="eFdepto" placeholder="Nuevo departamento" title="Introducir departamento del usuario" required
                                oninvalid="this.setCustomValidity('El campo departamento es necesario')" oninput="this.setCustomValidity('')"> 
                            </div>
                            
                            <!-- <label for="mail">Correo:</label> -->
                            <input class="input" type="email" name="Email" id="Email" value="<?php echo $cR[0][3] ?>" placeholder="Correo" title="Introducir correo del usuario" required
                            oninvalid="this.setCustomValidity('Formato de correo incorrecto')" oninput="this.setCustomValidity('')"> 
                            <br>
                            <label for="comboBoxUserType"> Permisos de usuario: </label><br>
                            <select class="comboBoxUserType" id="FcmbBox2" name="comboBoxUserType">
                                <option value="EST" <?php if ("EST" == $cR[0][1]) echo 'selected="selected"'; ?>>Usuario estándar</option>
                                <option value="SAD" <?php if ("SAD" == $cR[0][1]) echo 'selected="selected"'; ?>>Usuario administrador</option>
                                <option value="ADM" <?php if ("ADM" == $cR[0][1]) echo 'selected="selected"'; ?>>Super-usuario</option>
                            </select>
                            <div class="form-options">
                                <button disabled class="sumbit-AddUser" id="sumbit-editUser" type="submit" onclick="toggleInput()">Guardar cambios</button>
                                <a href="userManagement.php" id="cancel-editUser" class="close-AddUser">Cancelar</a>
                            </div>
                        </div>
                    </div> <!-- Fin de form-container --> 
                </div>
                </form> <!-- Fin de edit-user-form -->

                <?php } ?>


                </div>

            </div> <!-- Fin de userManagement -->

        </div> <!-- Fin de main -->
        
    </div> <!-- Fin de container -->
    <script src="../js/init.js"></script>
    <script src="../js/userManager.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var filter = "<?php echo $filterFlag; ?>";
            if (filter) {
                toggleFilterItems();
            }
        });
    </script>
    <?php } ?>
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
