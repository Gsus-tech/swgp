<?php
session_start();
require_once '../controller/generalCRUD.php';
use Controller\GeneralCrud\Crud;

if(isset($_SESSION['rol']) && isset($_SESSION['nombre'])) {
    if($_SESSION['rol']=='SAD' || $_SESSION['rol']=='ADM'){ 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv=»X-UA-Compatible» content=»IE-edge» charset=UTF-8″>
    <meta name="viewport" content="width=device-width" initial-scale=1.0″>
    <title>SWGP - Panel de inicio</title>
    <link rel="stylesheet" href="../assets/font-awesome-4.7.0/css/font-awesome.min.css">    
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/userMan_style.css">
    <link rel="stylesheet" href="../css/table-style.css">
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
        <?php

        if(isset($_GET['detailsId'])){
            $id=$_GET['detailsId'];
            $infoAccount = Crud::findRow('id_usuario,rolUsuario,nombre,correo,departamento', 'tbl_usuarios', 'id_usuario', $id);
            $x = Count($infoAccount) != 0;
            if($x == false){
                echo "<script>
                    alert('No se encontro una cuenta con el ID -$id-');
                    window.location.href = 'userManagement.php';
                </script>";
            }
            ?>
            <div class="header"> 
                <h4>Gestión de usuarios</h4>
            </div>
            <div class="accountDetailsDiv scroll">
                <div><h3 for="name">Detalles de cuenta:</h2></div>
                <br>
                <div class="generalInfo">
                    <div class="generalInfo1">
                        <h4 for="name">Nombre de usuario:</h4>
                        <input disabled class ="name input" name="name" value="<?php echo htmlspecialchars($infoAccount[0]['nombre']);?>">
                        <br>
                        <h4 for="mail">Correo:</h4>
                        <input disabled class ="mail input" name="mail" value="<?php echo htmlspecialchars($infoAccount[0]['correo']);?>">
                    </div>
                    <div class="generalInfo2">
                        <h4 for="userRol">Rol de usuario:</h4>
                        <?php if($infoAccount[0]['rolUsuario']=='ADM'){$type='Superusuario';}
                            elseif($infoAccount[0]['rolUsuario']=='SAD'){$type='Administrador';}
                            else{$type='Estándar';}
                        ?>
                        <input disabled name="userRol" class="input" value="<?php echo htmlspecialchars($type);?>">
                        <br>
                        <h4 for="name">Departamento:</h4>
                        <input disabled class="input" name="name" value="<?php echo htmlspecialchars($infoAccount[0]['departamento']);?>">
                    </div>
                </div>
                <br>
                <div class="projectsInfo">
                    <h3>Proyectos asignados</h3>
                    <?php
                    $projectParticipation = Crud::executeResultQuery("SELECT proyecto.nombre,proyecto.fecha_inicio,proyecto.fecha_cierre, integrante.responsable FROM tbl_proyectos proyecto JOIN tbl_integrantes integrante ON proyecto.id_proyecto = integrante.id_proyecto WHERE integrante.id_usuario=$id;");
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
                                <h4><?php echo $projectParticipation[$i]['nombre'] ?></h4>
                                
                                <label for="dateStart">Fecha de inicio: <i><?php echo $projectParticipation[$i]['fecha_inicio'] ?></i></label>
                                <br>
                                <label for="dateStart">Fecha de cierre: <i><?php echo $projectParticipation[$i]['fecha_cierre'] ?></i></label>
                                <h4>Rol: <label><?php if($projectParticipation[$i]['responsable']===true){echo "Responsable de proyecto";}else{echo "Colaborador";} ?></label></h4>
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
                    
                    <select class="comboBox dropDownFilter" id="filtersForRol" name="filtersForRol" onchange="FilterResults(this, 'filtersForDto', 2, 4)">
                        <option value="noFilter"> - Sin filtros - </option>
                        <option value="Estándar">Usuario Estándar</option>
                        <option value="Administrador">Usuario Administrador</option>
                        <option value="Super-Usuario">Super-Usuario</option>
                    </select>
                    </div>
                    <div class="dropDownFilter2 hide">
                    <label for="filtersForRol">Por departamento:</label>
                    <select class="comboBox dropDownFilter" id="filtersForDto" name="filtersForDto" onchange="FilterResults(this, 'filtersForRol', 4, 2)">
                    <option value="noFilter"> - Sin filtros - </option>
                        <?php
                        $filters = Crud::getFiltersOptions('tbl_usuarios', 'departamento');
                        if(count($filters)>0){
                            for($i=0;$i<count($filters);$i++){
                                foreach($filters[$i] as $key=>$value){
                                    echo '<option value=dto'.$i.'>'.$value.'</option>';
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
                    <i id="searchAccount" class="fa fa-search button hide" title="Buscar"></i>
                </div>
            
                <!-- Tabla de usuarios -->
            <div class="table">
                <table class="users-list">
                    <thead>
                        <tr>
                            <?php
                            if($_SESSION['rol']==='ADM'){ ?>
                                <th class="selectAccounts"><input type="checkbox" class="button" id="selectAllBoxes"></th>
                           <?php }
                            ?>
                            <th class="rowName">Nombre</th>
                            <th class="rowRol">Rol de usuario</th>
                            <th class="rowMail">Correo Electrónico</th>
                            <th class="rowDpto">Departamento</th>
                            <th class="rowActions">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id='users-list-body' class="tableContent">
                        <?php
                            $p = array();
                            if(isset($_GET['search'])){
                                $busqueda = Crud::antiNaughty($_GET['search']);
                                $clear = Crud::containsMaliciousPattern($busqueda);
                                if(!$clear){
                                    $p = Crud::selectUserSearchData('id_usuario,nombre,rolUsuario,correo,departamento', 'tbl_usuarios', "id_usuario", "DESC", $busqueda);
                                }else{
                                    echo "<script>alert('Se detectaron patrones maliciosos en la búsqueda.\\nIntenta de nuevo con diferentes parámetros de búsqueda.');</script>";
                                    $p = Crud::selectData('id_usuario,nombre,rolUsuario,correo,departamento', 'tbl_usuarios', "id_usuario", "DESC");
                                }
                            }
                            else{
                                $p = Crud::selectData('id_usuario,nombre,rolUsuario,correo,departamento', 'tbl_usuarios', "id_usuario", "DESC");
                            }

                            if (!empty($p) && count($p) > 0) {
                                $sessionId = $_SESSION['id'];
                            
                                // Filtrar los resultados que no coinciden con el id de la sesión actual
                                $filteredResults = array_filter($p, function ($item) use ($sessionId) {
                                    return $item['id_usuario'] != $sessionId;
                                });
                            
                                // Verificar si hay resultados después de filtrar
                                if (count($filteredResults) > 0) {
                                    foreach ($filteredResults as $user) {
                                        echo '<tr onclick="SelectThisRow(this, \'users-list-body\')">';                                        // echo generateUserRow($user);
                                        $row='';
                                        foreach ($user as $key => $value) {
                                            if ($value === $user['id_usuario']) {
                                                if($_SESSION['rol']==='ADM'){
                                                    $row .= "<td><input type='checkbox' class='account-checkbox button' value='$value'></td>";
                                                }
                                            } else if ($value === $user['nombre']) {
                                                $cId = htmlspecialchars($user['id_usuario']);
                                                $row .= "<td><i class='blueText' onclick=seeUserAccount('$cId') title='Ver detalles de cuenta'>" . htmlspecialchars($value) . "</i></td>";
                                            } else if ($value == 'ADM') {
                                                $row .= '<td>Super-Usuario</td>';
                                            } else if ($value == 'SAD') {
                                                $row .= '<td>Administrador</td>';
                                            } else if ($value == 'EST') {
                                                $row .= '<td>Estándar</td>';
                                            } else {
                                                $row .= '<td>' . htmlspecialchars($value) . '</td>';
                                            }
                                        }

                                        $userId = htmlspecialchars($user['id_usuario']);
                                        $row .= "<td><a id='editUserBtn' class='fa fa-edit button' title='Editar cuenta' onclick='editUserAccount($userId)'></a>";
                                        if($_SESSION['rol']=='ADM' ){
                                            $row .= "<a id='deleteUserBtn' class='fa fa-trash button' title='Eliminar cuenta' onclick='confirmDelete($userId)'></a>
                                            </td>";
                                        }else{
                                            $row .= "</td>";
                                        }
                                        echo $row;
                                        echo '</tr>';
                                    }
                                } else {
                                    echo "<tr><td colspan='6'>No se encontraron resultados.</td></tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6'>No se encontraron resultados.</td></tr>";
                            }
                        
                        ?>
                    </tbody>
                </table>
                <div class="pagination" id="pagination"></div>

                </div> <!-- Fin de table -->               
           
            

                <?php
                if($_SESSION['rol']=='ADM' ){
                    ?>
                    <div class="addBtn"><a id="showUserFormBtn" title="Crear nueva cuenta de usuario" class="fa fa-user-plus add-user-btn"></a></div>
                    <div id="accountsSelected" class="selectedRowsOptions hide">
                        <select class="comboBox" name="actionSelected" id="actionSelected">
                            <option value="0"> - Seleccionar acción - </option>
                            <option value="delete">Eliminar cuenta(s)</option>
                        </select>
                        <a id="applyAction" title="Aplicar acción a las cuentas seleccionadas" class="button apply normalBtn">Aplicar</a>
                        <a id="applyAction2" title="Aplicar acción a las cuentas seleccionadas" class="button apply shortBtn fa fa-chevron-right"></a>
                    </div>
                    
                <!-- Formulario de alta de usuario -->
                <form class="addUser-form hide" id="addUser-form" 
                onsubmit="return submitFormUser()" method="POST" autocomplete="on">
                <div class="form-bg">
                    <div class="form-container">
                        <div class="fm-content">
                            <div class="title"><h4>Agregar usuario:</h4></div>             
                            <input type="text" name="Uname" id="Fname" placeholder="Nombre de usuario" 
                            title="Introducir nombre de usuario" oninput="resetField(this)"> 
                            <br>

                            <label for="dropDownDepto">Departamento:</label>
                            <select class="comboBox dropDownDepto" id="dropDownDepto" name="dropDownDepto" style="margin-left:2rem;" oninput="resetField(this)">
                            <?php
                                $Deptos = Crud::getFiltersOptions('tbl_usuarios', 'departamento');
                                if(count($Deptos)>0){
                                    echo '<option value="noSelected">Seleccione una opción</option>';
                                    for($i=0;$i<count($Deptos);$i++){
                                        foreach($Deptos[$i] as $key=>$value){
                                            echo '<option value='.$i.'>'.$value.'</option>';
                                        }
                                    }
                                }
                            ?>
                            <option value="other">Otro</option>
                            </select>
                            <div id="Fdpto" class="Fdpto hide">
                                <input type="text" name="Fdpto" id="Fdepto" placeholder="Nuevo departamento" 
                                title="Introducir departamento del usuario" oninput="resetField(this)"> 
                            </div>

                            <!-- <label for="mail">Correo:</label> -->
                            <input type="email" name="Fmail" id="Fmail" placeholder="Correo" title="Introducir correo del usuario" required
                            oninvalid="invalidEmail(this)" oninput="resetField(this)"> 
                            <br>
                            <!-- <label for="dpto">Contraseña:</label> -->
                            <input type="password" class="passInput" name="Fpassword" id="Fpassword" title="Introducir contraseña de la cuenta" 
                            placeholder="Contraseña" autocomplete="false" oninput="resetField(this)">
                            <i id="passwordVisibility" name="passwordVisibility" class="fa fa-lock button" ></i> 
                            <br>
                            <!-- <label for="dpto">Confirmar contraseña:</label> -->
                            <input type="password" class="passwordConField" name="FpasswordCon" id="FpasswordCon" title="Confirmar contraseña" 
                            placeholder="Confirmar contraseña" autocomplete="false" oninput="resetField(this)"> 
                            <br>
                            <label for="comboBoxUserType"> Permisos de usuario: </label>
                            <select class="comboBox comboBoxUserType" id="FcmbBox" name="comboBoxUserType" oninput="resetField(this)">
                                <option value="noSelected">Seleccione una opción</option>
                                <option value="EST">Usuario Estándar</option>
                                <option value="SAD">Usuario Administrador</option>
                                <option value="ADM">Super-Usuario</option>
                            </select>
                            <div class="form-options">
                                <button disabled class="sumbit-AddUser" id="sumbit-AddUser" type="submit">Agregar usuario</button>
                                <a id="cancel-Adduser" class="close-AddUser" onclick="cerrarFormulario()">Cancelar</a>                                
                                
                            </div>
                        </div>
                    </div>
                </div> <!-- Fin de form-container --> 
                </form> <!-- Fin de user-form -->`;
                <?php
                } else{
                    echo '<div id="accountsSelected" class="selectedRowsOptions hide"></div>';
                }

                // Editar usuario
                if(isset($_GET['editId'])){ 
                    $thisId = filter_var($_GET['editId'], FILTER_VALIDATE_INT);
                    if($thisId === false){
                        // Si el ID del proyecto no es un entero válido
                        echo "<script>
                            alert('ID de usuario no válido.');
                            window.location.href = 'userManagement.php';
                        </script>";
                        exit;
                    }

                    $query = "SELECT id_usuario,rolUsuario,nombre,correo,departamento FROM `tbl_usuarios` WHERE id_usuario = ?";
                    $cR = Crud::executeResultQuery($query, [$thisId], 'i');

                    // Comprobar si el usuario existe
                    if (!$cR || count($cR) == 0) {
                        echo "<script>
                            alert('ID de usuario no registrado.');
                            window.location.href = 'userManagement.php';
                        </script>";
                        exit;
                    }
                    ?>
                <form id="editUser-form" class="addUser-form" onsubmit="return submitFormEditUser()" method="POST" autocomplete="on">
                <div class="form-bg">
                    <div class="form-container">
                        <div class="fm-content">
                            <div class="title"><h4>Editar usuario:</h4></div>                            <!-- <label for="name">Nombre:</label> -->
                            <input type="hidden" name="EditThisID" value="<?php echo $_GET['editId']?>">
                            <input class="input" type="text" name="Ename" id="Ename" value="<?php echo $cR[0]['nombre'] ?>" 
                            placeholder="Nombre de usuario" title="Introducir nombre de usuario" oninput="resetField(this)"> 
                            <br>
                            
                            <label for="dropDownDepto">Departamento:</label>
                            <select class="comboBox dropDownDepto" id="edropDownDepto" name="dropDownDepto" style="margin-left:2rem;">
                            <?php
                                $Deptos = Crud::getFiltersOptions('tbl_usuarios', 'departamento');
                                if(count($Deptos)>0){
                                    for($i=0;$i<count($Deptos);$i++){
                                        foreach($Deptos[$i] as $key=>$value){
                                            $selected = ($value == $cR[0]['departamento']) ? 'selected' : '';
                                            echo '<option value='.$i.' '.$selected.'>'.$value.'</option>';
                                        }
                                    }
                                }
                            ?>
                            <option value="other">Otro</option>
                            </select>
                            <div id="Edepto" class="Fdpto">
                                <input type="hidden" name="eFdpto" id="eFdepto" placeholder="Nuevo departamento" 
                                title="Introducir departamento del usuario"  oninput="resetField(this)"> 
                            </div>
                            
                            <!-- <label for="mail">Correo:</label> -->
                            <input class="input" type="email" name="Email" id="Email" value="<?php echo $cR[0]['correo'] ?>" placeholder="Correo"
                            oninvalid="invalidEmail(this)" title="Introducir correo del usuario"  oninput="resetField(this)"> 
                            <br>
                            <?php
                                if($_SESSION['rol']=='ADM'){
                            ?>
                            <label for="comboBoxUserType"> Permisos de usuario: </label><br>
                            <select class="comboBox comboBoxUserType" id="FcmbBox2" name="comboBoxUserType">
                                <option value="EST" <?php if ("EST" == $cR[0]['rolUsuario']) echo 'selected="selected"'; ?>>Usuario estándar</option>
                                <option value="SAD" <?php if ("SAD" == $cR[0]['rolUsuario']) echo 'selected="selected"'; ?>>Usuario administrador</option>
                                <option value="ADM" <?php if ("ADM" == $cR[0]['rolUsuario']) echo 'selected="selected"'; ?>>Super-usuario</option>
                            </select>
                            <?php
                                }else if($_SESSION['rol']=='SAD'){
                                    $rolU = $cR[0]['rolUsuario'];
                                    $_SESSION['editUser-rol'] = $rolU;
                                    echo "<input type='hidden' name='comboBoxUserType' id='comboBoxUserType' value='$rolU'>";
                                }
                                ?>
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
    <?php
     if (isset($_GET['error'])) {
        $errorMsg = urldecode($_GET['error']);
        echo "<script>alert('Codigo de error capturado: $errorMsg')</script>";
    }
    if($_SESSION['rol']=='ADM' ){
        echo "<script src='../js/adminUserActions.js'></script>";
    }
    ?>
    <script src="../js/tablePagination.js"></script>
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
    <?php 
    } ?>
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
    header("Location: dashboard.php");
    exit();
}
?> 
