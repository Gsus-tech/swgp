<?php
session_start();
require_once '../controller/generalCRUD.php';
use Controller\GeneralCrud\Crud;

$destination = "../php/support.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar el rol del usuario
    if (isset($_SESSION['rol'])) {
        if ($_SESSION['rol'] === 'ADM' || $_SESSION['rol'] === 'SAD') {

            if(isset($_GET['switchTicketState']) && $_GET['switchTicketState'] == 'true'){
                $crud = new Crud();
                $mysqli = $crud->getMysqliConnection();
                $idTicket = filter_var($_POST['ticketId'], FILTER_VALIDATE_INT);
                $newState = Crud::antiNaughty($_POST['toState']);
                
                if($newState === 'Abierto' || $newState === 'Pendiente' || $newState === 'Cerrado'){

                    $query = "UPDATE tbl_solicitudes_soporte SET estado = ? WHERE id_solicitud = ?";
                    $stmt = $mysqli->prepare($query);
                    
                    if ($stmt) {
                        $stmt->bind_param('si', $newState, $idTicket);
                        $stmt->execute();
                        
                        if ($stmt->affected_rows > 0) {
                            echo json_encode(['success' => true, 'message' => 'Estado de ticket actualizado correctamente.']);
                        } else {
                            echo json_encode(['success' => false, 'message' => 'No se realizaron cambios.']);
                        }
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Error en la preparación de la consulta.']);
                    }
                }else {
                    echo json_encode(['success' => false, 'message' => 'Nuevo estado de ticket inválido.']);
                }
            }
            
            if(isset($_GET['changeUserRolInProject']) && $_GET['changeUserRolInProject'] == 'true'){
                $crud = new Crud();
                $mysqli = $crud->getMysqliConnection();
                $user = filter_var($_POST['user'], FILTER_VALIDATE_INT);
                $project = filter_var($_POST['project'], FILTER_VALIDATE_INT);
                $rol = Crud::antiNaughty($_POST['rol']);
                
                if($rol === 'rep' || $rol === 'member'){
                    if($user !== false && $project !== false){
                        $confirmCurrentRol = Crud::executeResultQuery('SELECT responsable FROM tbl_integrantes WHERE id_usuario = ? AND id_proyecto = ?',[$user, $project], 'ii');
                        if(count($confirmCurrentRol)>0){
                            $rol = $rol === 'rep' ? 1 : 0;
                            if($rol===$confirmCurrentRol[0]['responsable']){
                                $newRol = $rol === 1 ? 0 : 1;
                                
                                $query = "UPDATE tbl_integrantes SET responsable = ? WHERE id_usuario = ? AND id_proyecto = ?";
                                $stmt = $mysqli->prepare($query);
                                
                                if ($stmt) {
                                    $stmt->bind_param('iii', $newRol, $user, $project);
                                    $stmt->execute();
                                    
                                    if ($stmt->affected_rows > 0) {
                                        echo json_encode(['success' => true, 'chg'=>true, 'message' => 'Permisos de usuario actualizados.']);
                                    } else {
                                        echo json_encode(['success' => false, 'message' => 'No se realizaron cambios.']);
                                    }
                                } else {
                                    echo json_encode(['success' => false, 'message' => 'Error en la preparación de la consulta.']);
                                }
                            }else{
                                if($confirmCurrentRol[0]['responsable']===1){
                                    echo json_encode(['success' => true, 'chg'=>false, 'message' => 'No se realizaron cambios. El usuario ya cuenta con los privilegios de representante de proyecto.']);
                                }else{
                                    echo json_encode(['success' => true, 'chg'=>false, 'message' => 'No se realizaron cambios. El usuario no cuenta con privilegios.']);
                                }                            }
                        }else{
                            echo json_encode(['success' => false, 'message' => 'Error al consultar la confirmación del rol actual.']);   
                        }
                    }else{
                        echo json_encode(['success' => false, 'message' => 'Id de usuario o de proyecto inválido.']);
                    }
                }else {
                    echo json_encode(['success' => false, 'message' => 'Rol actual del usuario inválido.']);
                }
            }
            
            if(isset($_GET['addTeamMember']) && $_GET['addTeamMember'] == 'true'){
                $crud = new Crud();
                $mysqli = $crud->getMysqliConnection();
                $user = filter_var($_POST['userId'], FILTER_VALIDATE_INT);
                $project = filter_var($_POST['proyecto'], FILTER_VALIDATE_INT);
                
                if($user !== false && $project !== false){

                    $checkQuery = "SELECT id_usuario FROM tbl_integrantes WHERE id_usuario = ? AND id_proyecto = ?";
                    $checkStmt = $mysqli->prepare($checkQuery);

                    if ($checkStmt) {
                        $checkStmt->bind_param('ii', $user, $project);
                        $checkStmt->execute();
                        $result = $checkStmt->get_result();
                        
                        if ($result->num_rows > 0) {
                            echo json_encode(['success' => true, 'message' => 'El usuario ya es miembro del proyecto.']);
                        } 
                        else {
                            $query = "INSERT INTO tbl_integrantes (id_usuario, id_proyecto, responsable) VALUES (?, ?, 0)";
                            $stmt = $mysqli->prepare($query);
                            
                            if ($stmt) {
                                $stmt->bind_param('ii', $user, $project);
                                $stmt->execute();
                                
                                if ($stmt->affected_rows > 0) {
                                    echo json_encode(['success' => true, 'message' => 'Integrante de equipo añadido.']);
                                } else {
                                    echo json_encode(['success' => false, 'message' => 'No se realizaron cambios.']);
                                }
                            } else {
                                echo json_encode(['success' => false, 'message' => 'Error en la preparación de la consulta.'. $mysqli->error]);
                            }
                        }
                    }
                }else {
                    echo json_encode(['success' => false, 'message' => 'Id de usuario o de proyecto inválido.']);
                }
            }
            
            if(isset($_GET['createAndAddTeamMember']) && $_GET['createAndAddTeamMember'] == 'true'){
                if($_SESSION['rol']==='ADM'){
                    $crud = new Crud();
                    $mysqli = $crud->getMysqliConnection();
                    $project = filter_var($_POST['proyecto'], FILTER_VALIDATE_INT);
                    
                    if($project !== false){
                        $name = Crud::antiNaughty((string)$_POST['name']);
                        $depto = Crud::antiNaughty((string)$_POST['depto']);
                        $mail = Crud::antiNaughty((string)$_POST['mail']);
                        $password = password_hash('Cobach123!', PASSWORD_DEFAULT);
                        $partes = explode(' ', $name);
                        $nickName = isset($partes[1]) ? $partes[0] . ' ' . substr($partes[1], 0, 1) . '.' : $partes[0];

                        $createUserQuery = "INSERT INTO tbl_usuarios (rolUsuario, nombre, correo, contrasena, departamento, nickname)
                        VALUES (?, ?, ?, ?, ?, ?)";
                        $params = ['EST', $name, $mail, $password, $depto, $nickName];
                        $types = "ssssss";
                        Crud::executeNonResultQuery2($createUserQuery, $params, $types, './php/support.php');
                        $success = Crud::executeResultQuery('SELECT id_usuario FROM tbl_usuarios WHERE nombre = ? AND correo = ? AND departamento = ?',
                            [$name, $mail, $depto],'sss');
                        if (count($success) > 0) {
                            $query = "INSERT INTO tbl_integrantes (id_usuario, id_proyecto, responsable) VALUES (?, ?, 0)";
                            $stmt = $mysqli->prepare($query);
                            $user = $success[0]['id_usuario'];
                            if ($stmt) {
                                $stmt->bind_param('ii', $user, $project);
                                $stmt->execute();
                                
                                if ($stmt->affected_rows > 0) {
                                    echo json_encode(['success' => true, 'message' => 'Integrante de equipo añadido.']);
                                } else {
                                    echo json_encode(['success' => false, 'message' => 'No se realizaron cambios.']);
                                }
                            } else {
                                echo json_encode(['success' => false, 'message' => 'Error en la preparación de la consulta.'. $mysqli->error]);
                            }
                        }else{
                            echo json_encode(['success' => false, 'message' => 'No se pudo crear la cuenta de usuario.']);
                        }
                    }else {
                        echo json_encode(['success' => false, 'message' => 'Id de usuario o de proyecto inválido.']);
                    }
                }else{
                    echo json_encode(['success' => false, 'message' => 'No cuentas con los permisos necesarios para crear cuentas de usuario.']);
                }
            }
            
            if(isset($_GET['findExistingUser']) && $_GET['findExistingUser'] == 'true'){
                $crud = new Crud();
                $mysqli = $crud->getMysqliConnection();
                $name = Crud::antiNaughty($_POST['name']);
                $mail = Crud::antiNaughty($_POST['mail']);
                
                if (filter_var($mail, FILTER_VALIDATE_EMAIL)) {
                    $query = "SELECT id_usuario, nombre, correo, departamento FROM tbl_usuarios WHERE correo = ?";
                    $stmt = $mysqli->prepare($query);
                    
                    if ($stmt) {
                        $stmt->bind_param("s", $mail);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            $userInfo = $result->fetch_assoc();
                            echo json_encode(['success' => true, 'exists'=>true, 'message' => 'Correo de usuario existente.', 'userInfo'=>$userInfo]);
                        } else {
                            $nameParts = explode(" ", $name);
                            $likeConditions = [];
                            foreach ($nameParts as $part) {
                                $part = $mysqli->real_escape_string($part);
                                $likeConditions[] = "nombre LIKE '%$part%'";
                            }

                            $likeQuery = implode(" AND ", $likeConditions);
                            $q2 = "SELECT id_usuario, nombre, correo, departamento FROM tbl_usuarios WHERE $likeQuery";
                            
                            $result2 = $mysqli->query($q2);

                            if ($result2 && $result2->num_rows > 0) {
                                $userInfo = $result2->fetch_assoc();
                                echo json_encode(['success' => true, 'exists' => true, 'message' => 'Se encontraron coincidencias parciales en el nombre.', 'userInfo'=>$userInfo]);
                            } else {
                                echo json_encode(['success' => true, 'exists' => false, 'message' => 'No se encontraron coincidencias.']);
                            }
                        
                        }
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Error en la preparación de la consulta.']);
                    }
                }else {
                    echo json_encode(['success' => false, 'message' => 'El correo electrónico proporcionado es inválido.']);
                }
            }
            
            if(isset($_GET['AccountFieldUpdate']) && $_GET['AccountFieldUpdate'] == 'true'){
                $crud = new Crud();
                $mysqli = $crud->getMysqliConnection();
                $newValue = Crud::antiNaughty($_POST['newValue']);
                $column = Crud::antiNaughty($_POST['column']);
                $user = filter_var($_POST['ticketRem'], FILTER_VALIDATE_INT);
                $idTicket = filter_var($_POST['ticketId'], FILTER_VALIDATE_INT);
                
                if(($column === 'departamento' || $column === 'correo') && $user !== false && $idTicket !== false){
                    $query = "UPDATE tbl_usuarios SET $column = ? WHERE id_usuario = ?";
                    $stmt = $mysqli->prepare($query);
                    
                    if ($stmt) {
                        $stmt->bind_param('si', $newValue, $user);
                        $stmt->execute();
                        
                        if ($stmt->affected_rows > 0) {
                            $q2 = "UPDATE tbl_solicitudes_soporte SET estado = ? WHERE id_solicitud = ?";
                            $stmt2 = $mysqli->prepare($q2);
                            
                            if ($stmt2) {
                                $newState = 'Cerrado';
                                $stmt2->bind_param('si', $newState, $idTicket);
                                $stmt2->execute();
                                
                                // Aquí usamos $stmt2->affected_rows para validar si el segundo UPDATE se ejecutó correctamente
                                if ($stmt2->affected_rows > 0) {
                                    echo json_encode(['success' => true, 'message' => 'Datos de la cuenta y estado del ticket actualizados correctamente.']);
                                } else {
                                    echo json_encode(['success' => true, 'message' => 'Datos actualizados. Error al actualizar el estado del ticket.']);
                                }
                            } else {
                                echo json_encode(['success' => true, 'message' => 'Datos actualizados. Error 2 al actualizar el estado del ticket.']);
                            }
                        } else {
                            echo json_encode(['success' => false, 'message' => 'No se realizaron cambios en los datos del usuario.']);
                        }
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Error en la preparación de la consulta para actualizar el usuario.']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error de datos: Columna desconocida o id de creador de ticket inválido.']);
                }
            }
            
            if(isset($_GET['systemErrorReport']) && $_GET['systemErrorReport'] == 'true'){
                $crud = new Crud();
                $mysqli = $crud->getMysqliConnection();
                $response = Crud::antiNaughty($_POST['response']);
                $user = filter_var($_POST['ticketRem'], FILTER_VALIDATE_INT);
                $idTicket = filter_var($_POST['ticketId'], FILTER_VALIDATE_INT);
                
                if($user !== false && $idTicket !== false){
                    $query = "UPDATE tbl_solicitudes_soporte SET estado = ?, response = ? WHERE id_solicitud = ?";
                    $stmt = $mysqli->prepare($query);
                    
                    if ($stmt) {
                        $newState = 'Cerrado';
                        $stmt->bind_param('ssi', $newState, $response, $idTicket);
                        $stmt->execute();
                        
                        if ($stmt->affected_rows > 0) {
                            echo json_encode(['success' => true, 'message' => 'Ticket actualizado correctamente.']);
                        } else {
                            echo json_encode(['success' => true, 'message' => 'Error al ejecutar la consulta. No se realizaron cambios.']);
                        }
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta.']);
                    }        
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error de datos: Id de usuario o de ticket inválido.']);
                }
            }
            
            
            if (isset($_GET['getTicket']) && $_GET['getTicket'] == 'true') {
                $crud = new Crud();
                $mysqli = $crud->getMysqliConnection();
                $idTicket = filter_var($_POST['ticketId'], FILTER_VALIDATE_INT);
            
                $query = "SELECT id_usuario, tipoSolicitud, mensaje FROM tbl_solicitudes_soporte WHERE id_solicitud = ?";
                $stmt = $mysqli->prepare($query);
            
                if ($stmt) {
                    $stmt->bind_param('i', $idTicket);
                    $stmt->execute();
                    $result = $stmt->get_result();
            
                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc(); 
                        $mensaje = json_decode($row['mensaje'], true);
                        $tipo = htmlspecialchars($row['tipoSolicitud']);
                        $user = htmlspecialchars($row['id_usuario']);
                        $q2 = "SELECT nombre FROM tbl_usuarios WHERE id_usuario = ?";
                        $userName = Crud::executeResultQuery($q2, [$user], 'i');
                        $nombre = $userName[0]['nombre'];
                        if($tipo > 0 && $tipo < 4){
                            switch ($tipo){
                                case 1:
                                    $titulo = htmlspecialchars($mensaje['titulo']);
                                    $descripcion = htmlspecialchars($mensaje['descripcion']);
                                    $imgContent = '';
                                    if (isset($mensaje['imagen'])) {
                                        $imgPath = htmlspecialchars($mensaje['imagen']);
                                        $imgContent = "<div><label>Imagen capturada:</label><br><img src='$imgPath' alt='Imagen del ticket' class='ticketImage'></div>";
                                    }
                                    $html = "
                                        <div t1p0='systemErrorReport' class='systemErrorReport'>
                                        <h2>Ticket de Soporte</h2>
                                        <div id='ticketCreator' tcid='$user' class='ticketCreator'>
                                        <span>Solicitado por:  </span><i>$nombre</i></div>
                                        <div class='s1'>
                                            <label>Error del sistema encontrado:</label>
                                            <input disabled class='input' value='$titulo'>
                                        </div>
                                        <div class='s2'>
                                            <label>Descripción del error:</label>
                                            <input disabled class='input' value='$descripcion'>
                                        </div>
                                        $imgContent
                                        <div class='btnOptions'>
                                            <button class='generalBtnStyle btn-green' id='solveAndClose'>Cerrar ticket</button>
                                            <button class='generalBtnStyle btn-red' id='cancelAndClose'>Cancelar</button>
                                        </div>
                                        </div>
                                        ";  
                                    break;
                                    case 2: 
                                        $changeType = htmlspecialchars($mensaje['Cambio']);
                                        $idProject = filter_var($mensaje['Proyecto'], FILTER_VALIDATE_INT);
                                        if($idProject !== false){
                                            $q3 = "SELECT nombre FROM tbl_proyectos WHERE id_proyecto = ?";
                                            $projectInf = Crud::executeResultQuery($q3, [$idProject], 'i');
                                            $pName = $projectInf[0]['nombre'];
                                            if($changeType === 'addMember') {
                                                $newUserN = htmlspecialchars($mensaje['nombre']);
                                                $correo = htmlspecialchars($mensaje['correo']);
                                                $depto = htmlspecialchars($mensaje['depto']);
                                                $html = "
                                                    <div t1p0='projectInfoUpdate' class='projectInfoUpdate' tcp2='addMember' pid='$idProject'>
                                                        <h2>Ticket de Soporte</h2>
                                                        <div id='ticketCreator' tcid='$user' class='ticketCreator'>
                                                            <span>Proyecto: </span><i>$pName</i><br>
                                                            <span>Solicitado por: </span><i>$nombre</i></div>
                                                        <span>Asunto: </span>
                                                        <h3>Añadir integrante de equipo</h3>

                                                        <br><span>Información del nuevo integrante: </span>
                                                        <br><br>
                                                        <div class='s1'>
                                                            <label>Nombre:</label>
                                                            <input disabled id='newUserName' class='input' value='$newUserN'>
                                                        </div>
                                                        <div class='s2'>
                                                            <label>Departamento:</label>
                                                            <input disabled id='newUserDepto' class='input' value='$depto'>
                                                        </div>
                                                        <div class='s1'>
                                                            <label>Correo:</label>
                                                            <input disabled id='newUserMail' class='input' value='$correo'>
                                                        </div>
                                                        <br>
                                                        <div class='btnOptions'>
                                                            <button class='generalBtnStyle btn-green' id='solveAndClose'>Agregar usuario</button>
                                                            <button class='generalBtnStyle btn-red' id='cancelAndClose'>Cancelar</button>
                                                        </div>
                                                    </div>
                                                ";
                                            }
                                            else if($changeType === 'removeMember') {
                                                $userAlter = htmlspecialchars($mensaje['userId']);
                                                $description = htmlspecialchars($mensaje['ticketDescription']);
                                                $userInfo = Crud::executeResultQuery('SELECT nombre FROM tbl_usuarios WHERE id_usuario = ?',[$userAlter], 'i');
                                                if(count($userInfo) > 0){
                                                    $targetUserName = $userInfo[0]['nombre'];
                                                    $html = "
                                                        <div t1p0='projectInfoUpdate' class='projectInfoUpdate' tcp2='removeMember' pid='$idProject'>
                                                            <h2>Ticket de Soporte</h2>
                                                            <div id='ticketCreator' tcid='$user' class='ticketCreator'>
                                                                <span>Proyecto: </span><i>$pName</i><br>
                                                                <span>Solicitado por: </span><i>$nombre</i></div>
                                                            <span>Asunto: </span>
                                                            <h3>Eliminar integrante del proyecto</h3>

                                                            <br><span>Información del ticket: </span>
                                                            <br><br>
                                                            <div class='s1'>
                                                                <label>Integrante por expulsar:</label>
                                                                <input disabled id='alterThisUser' class='input' uid='$userAlter' value='$targetUserName'>
                                                            </div>
                                                            <div class='s2'>
                                                                <label>Razón o motivo para realizar esta acción:</label>
                                                                <textarea disabled id='currentRol' class='input textarea'>$description</textarea>
                                                            </div>
                                                            <div class='btnOptions'>
                                                                <button class='generalBtnStyle btn-green' id='solveAndClose'>Eliminar integrante</button>
                                                                <button class='generalBtnStyle btn-red' id='cancelAndClose'>Cancelar</button>
                                                            </div>
                                                        </div>
                                                    ";
                                                }
                                            }
                                            else if($changeType === 'changePermitions') {
                                                $userAlter = htmlspecialchars($mensaje['usuario']);
                                                $rolActual = htmlspecialchars($mensaje['currentPermtion']);
                                                $userInfo = Crud::executeResultQuery('SELECT nombre FROM tbl_usuarios WHERE id_usuario = ?',[$userAlter], 'i');
                                                if(count($userInfo) > 0){
                                                    $targetUserName = $userInfo[0]['nombre'];
                                                    $currentRol = $rolActual === 'member' ? 'Integrante sin privilegios' : 'Integrante con privilegios de lider de proyecto';
                                                    $html = "
                                                        <div t1p0='projectInfoUpdate' class='projectInfoUpdate' tcp2='changePermitions' pid='$idProject'>
                                                            <h2>Ticket de Soporte</h2>
                                                            <div id='ticketCreator' tcid='$user' class='ticketCreator'>
                                                                <span>Proyecto: </span><i>$pName</i><br>
                                                                <span>Solicitado por: </span><i>$nombre</i></div>
                                                            <span>Asunto: </span>
                                                            <h3>Cambiar permisos de integrante</h3>
    
                                                            <br><span>Información del ticket: </span>
                                                            <br><br>
                                                            <div class='s1'>
                                                                <label>Nombre de integrante:</label>
                                                                <input disabled id='alterThisUser' class='input' uid='$userAlter' value='$targetUserName'>
                                                            </div>
                                                            <div class='s2'>
                                                                <label>Rol actual dentro del proyecto:</label>
                                                                <input disabled id='currentRol' curRol='$rolActual' class='input' value='$currentRol'>
                                                            </div>
                                                            <div class='btnOptions'>
                                                                <button class='generalBtnStyle btn-green' id='solveAndClose'>Cambiar permisos</button>
                                                                <button class='generalBtnStyle btn-red' id='cancelAndClose'>Cancelar</button>
                                                            </div>
                                                        </div>
                                                    ";

                                                }else{
                                                    echo json_encode(['success' => false, 'message' => 'No se encontró la información del usuario.']);
                                                }
                                            }
                                            else if($changeType === 'projectDataCorrection') {
                                                $title = htmlspecialchars($mensaje['ticketTitle']);
                                                $description = htmlspecialchars($mensaje['ticketDescription']);
                                                $html = "
                                                    <div t1p0='projectInfoUpdate' class='projectInfoUpdate' tcp2='projectDataCorrection' pid='$idProject'>
                                                        <h2>Ticket de Soporte</h2>
                                                        <div id='ticketCreator' tcid='$user' class='ticketCreator'>
                                                            <span>Proyecto: </span><i>$pName</i><br>
                                                            <span>Solicitado por: </span><i>$nombre</i>
                                                        </div>
                                                        <br><span>Información del ticket: </span>
                                                        <br><br>
                                                        <div class='s1'>
                                                            <label>Título:</label>
                                                            <input disabled id='ticketTitle' class='input' value='$title'>
                                                        </div>
                                                        <div class='s2'>
                                                            <label>Descripción de los cambios a realizar:</label>
                                                            <textarea disabled id='description' class='input textarea'>$description</textarea>
                                                        </div>
                                                        <div class='btnOptions'>
                                                            <button class='generalBtnStyle btn-green' id='solveAndClose'>Editar de proyecto</button>
                                                            <button class='generalBtnStyle btn-red' id='cancelAndClose'>Cancelar</button>
                                                        </div>
                                                    </div>
                                                ";
                                            }
                                        }
                                    break;
                                case 3:
                                    $campo = htmlspecialchars($mensaje['Campo']);
                                    $newValue = htmlspecialchars($mensaje['newValue']);
                                    $q2 = "SELECT nombre,$campo FROM tbl_usuarios WHERE id_usuario = ?";
                                    $oldValue = Crud::executeResultQuery($q2, [$user], 'i');
                                    if($oldValue && count($oldValue)>0){
                                        $value = $oldValue[0][$campo];
                                        $userName = $oldValue[0]['nombre'];
                                        $html = "
                                        <div t1p0='AccountFieldUpdate' class='AccountFieldUpdate'>
                                        <h2>Ticket de Soporte</h2>
                                        <div id='ticketCreator' tcid='$user' class='ticketCreator'>
                                        <span>Solicitado por:  </span><i>$userName</i></div>
                                        <div class='s1'>
                                            <label>Valor actual registrado:</label>
                                            <input disabled class='input' value='$value'>
                                        </div>
                                        <div class='s2'>
                                            <label>Valor del nuevo dato:</label>
                                            <input id='newValue' class='input' field='$campo' value='$newValue' oninput='resetField(this)'>
                                        </div>
                                        <div class='btnOptions'>
                                            <button class='generalBtnStyle btn-green' id='solveAndClose'>Actualizar datos</button>
                                            <button class='generalBtnStyle btn-red' id='cancelAndClose'>Cancelar</button>
                                        </div>
                                        </div>
                                        ";
                                    }else{
                                        echo json_encode(['success' => false, 'message' => 'Error al recuperar los datos del usuario.']);
                                        exit();
                                    }
                                    break;
                            }
                            if($html !== ''){
                                echo json_encode(['success' => true, 'message' => 'Ticket recuperado correctamente.', 'html' => $html]);
                            }else{
                                echo json_encode(['success' => false, 'message' => 'Error al preparar la interfaz de resolución.']);
                            }
                        }else{
                            echo json_encode(['success' => false, 'message' => 'Tipo de solicitud no válido.']);
                        }
                    } else {
                        echo json_encode(['success' => false, 'message' => 'No se pudo recuperar el ticket.']);
                    }
                    $stmt->close();
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error en la preparación de la consulta.']);
                }
                $mysqli->close();
            }
            

        } else if ($_SESSION['rol'] === 'EST') {
            $crud = new Crud();
            $mysqli = $crud->getMysqliConnection();
            $id_usuario = $_SESSION['id'];
        
            if (isset($_GET['newTicket']) && $_GET['newTicket'] === 'true') {
                $type = $_POST['ticketType'];
                if ($type) {
                    $submit = false;
                    $query = "";
            
                    if ($type === 't-1') {
                        $ticketTitle = Crud::antiNaughty((string)$_POST['ticketTitle']);
                        $ticketDescription = Crud::antiNaughty((string)$_POST['ticketDescription']);
            
                        $mensaje = json_encode([
                            'titulo' => "{$ticketTitle}",
                            'descripcion' => "{$ticketDescription}"
                        ]);
            
                        $uploadDir = "../assets/suppTicketsImages/";
            
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }
            
                        if (isset($_POST['ticketImageBase64'])) {
                            $imgData = $_POST['ticketImageBase64'];
            
                            if (preg_match('/^data:image\/(\w+);base64,/', $imgData, $type)) {
                                $imgData = substr($imgData, strpos($imgData, ',') + 1);
                                $type = strtolower($type[1]);
            
                                if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                                    echo json_encode(['success' => false, 'message' => 'Formato de imagen no soportado.']);
                                    exit;
                                }
            
                                $imgData = base64_decode($imgData);
                                if ($imgData === false) {
                                    echo json_encode(['success' => false, 'message' => 'Error al decodificar la imagen.']);
                                    exit;
                                }
            
                                $uniqueFileName = uniqid('ticket_', true) . '.' . $type;
                                $filePath = $uploadDir . $uniqueFileName;
            
                                if (file_put_contents($filePath, $imgData) === false) {
                                    echo json_encode(['success' => false, 'message' => 'Error al guardar la imagen.']);
                                    exit;
                                }
            
                                $mensaje = json_encode([
                                    'titulo' => "{$ticketTitle}",
                                    'descripcion' => "{$ticketDescription}",
                                    'imagen' => '../assets/suppTicketsImages/' . $uniqueFileName
                                ]);
                            } else {
                                echo json_encode(['success' => false, 'message' => 'Formato de imagen inválido.']);
                                exit;
                            }
                        }
                        
                        // Prepara y ejecuta la consulta
                        $query = "INSERT INTO tbl_solicitudes_soporte (id_usuario, tipoSolicitud, mensaje, estado) VALUES (?, 1, ?, 'Abierto')";
                        $stmt = $mysqli->prepare($query);
                        
                        if (!$stmt) {
                            echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta: ' . $mysqli->error]);
                            exit;
                        }
                        
                        $stmt->bind_param('is', $id_usuario, $mensaje);
                        
                        if (!$stmt->execute()) {
                            echo json_encode(['success' => false, 'message' => 'Error en la ejecución: ' . $stmt->error]);
                            exit;
                        }
                        
                        echo json_encode(['success' => true, 'message' => 'Ticket enviado exitosamente.']);
                        $stmt->close();
                        
                    } 

                    else if($type === 't-2'){
                        $query = "SELECT proyectos.id_proyecto FROM tbl_proyectos proyectos JOIN tbl_integrantes integrantes ON proyectos.id_proyecto = integrantes.id_proyecto WHERE integrantes.id_usuario = ? AND integrantes.responsable = 1 AND proyectos.estado = 1;";
                        $params = [$id_usuario];
                        $types = "i";
                        $userProjects = Crud::executeResultQuery($query, $params, $types);
                        $proyectoId = filter_var($_POST['projectSelect'], FILTER_VALIDATE_INT);
                        if($proyectoId !== false && is_array($userProjects)){   
                            $proyectoIds = array_column($userProjects, 'id_proyecto');
                            if (in_array($proyectoId, $proyectoIds)) {     
                                $correctionType = Crud::antiNaughty((string)$_POST['correctionType']);
                                $mensaje = null;
                                if($correctionType === 'addMember'){
                                    $nombre = Crud::antiNaughty((string)$_POST['name']);
                                    $correo = Crud::antiNaughty((string)$_POST['email']);
                                    $depto = Crud::antiNaughty((string)$_POST['department']);
                                    
                                    $mensaje = json_encode([
                                        'Cambio' => "addMember",
                                        'Proyecto' => "$proyectoId",
                                        'nombre' => "$nombre",
                                        'correo' => "$correo",
                                        'depto' => "$depto"
                                    ]);
                                }
                                if($correctionType === 'removeMember'){
                                    $userDel = Crud::antiNaughty((string)$_POST['delMemberSelect']);
                                    $userDel = filter_var($userDel, FILTER_VALIDATE_INT);
                                    $ticketDescription = Crud::antiNaughty((string)$_POST['ticketDescription']);
                                    
                                    if($userDel != false){
                                        $mensaje = json_encode([
                                            'Cambio' => "removeMember",
                                            'Proyecto' => "$proyectoId",
                                            'userId' => "$userDel",
                                            'ticketDescription' => "$ticketDescription"
                                        ]);
                                    }
                                }
                                if($correctionType === 'changePermitions'){
                                    $crRp = Crud::antiNaughty((string)$_POST['crRp']);
                                    $memberId = Crud::antiNaughty((string)$_POST['permitionMemberSelect']);                                    
                                    
                                    $query = "SELECT id_usuario FROM tbl_integrantes WHERE id_proyecto = ?";
                                    $params = [$proyectoId];
                                    $types = "i";
                                    $usersListing = Crud::executeResultQuery($query, $params, $types);
                                    $userIds = array_column($usersListing, 'id_usuario');

                                    if (in_array($memberId, $userIds)) {   
                                        if($crRp === '0' || $crRp === '1'){
                                            $cr = $crRp === '0' ? 'member' : 'rep';
                                            $mensaje = json_encode([
                                                'Cambio' => "changePermitions",
                                                'Proyecto' => "$proyectoId",
                                                'usuario' => "$memberId",
                                                'currentPermtion' => "$cr"
                                            ]);
                                        }else{
                                            echo json_encode(['success' => false, 'message' => 'Permiso de usuario actual inválido. Recarga la pagina e intenta de nuevo.']);
                                            exit;
                                        }
                                    }else{
                                        echo json_encode(['success' => false, 'message' => 'No se pudo encontrar el id de usuario. Recarga la pagina e intenta de nuevo.']);
                                        exit;
                                    }
                                }
                                if($correctionType === 'projectDataCorrection'){
                                    $ticketTitle = Crud::antiNaughty((string)$_POST['ticketTitle']);
                                    $ticketDescription = Crud::antiNaughty((string)$_POST['ticketDescription']);
                                    
                                    $mensaje = json_encode([
                                        'Cambio' => "projectDataCorrection",
                                        'Proyecto' => "$proyectoId",
                                        'ticketTitle' => "$ticketTitle",
                                        'ticketDescription' => "$ticketDescription"
                                    ]);
                                }

                                if($mensaje != null){
                                    // Prepara y ejecuta la consulta
                                    $query = "INSERT INTO tbl_solicitudes_soporte (id_usuario, tipoSolicitud, mensaje, estado) VALUES (?, 2, ?, 'Abierto')";
                                    $stmt = $mysqli->prepare($query);
                        
                                    if (!$stmt) {
                                        echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta: ' . $mysqli->error]);
                                        exit;
                                    }
                        
                                    $stmt->bind_param('is', $id_usuario, $mensaje);
                        
                                    if (!$stmt->execute()) {
                                        echo json_encode(['success' => false, 'message' => 'Error en la ejecución: ' . $stmt->error]);
                                        exit;
                                    }

                                    echo json_encode(['success' => true, 'message' => 'Ticket de Corrección o cambios en un proyecto levantado.']);
                                    $stmt->close();
                                }else{
                                    echo json_encode(['success' => false, 'message' => 'Tipo de corrección o cambio a realizar inválido.']);
                                }
                            }else{
                                echo json_encode(['success' => false, 'message' => 'Id de proyecto alterado.']);
                            }
                        }else{
                            echo json_encode(['success' => false, 'message' => 'Id de proyecto inválido.']);
                        }
                    }
                    
                    else if($type === 't-3'){
                        $onField = Crud::antiNaughty((string)$_POST['correctionType']);
                        $newValue = Crud::antiNaughty((string)$_POST['newValue']);
                        $field = $onField === 'deptoUpdate' ? 'departamento' : 'correo';

                        $mensaje = json_encode([
                            'Campo' => "{$field}",
                            'newValue' => "{$newValue}"
                        ]);

                        // Prepara y ejecuta la consulta
                        $query = "INSERT INTO tbl_solicitudes_soporte (id_usuario, tipoSolicitud, mensaje, estado) VALUES (?, 3, ?, 'Abierto')";
                        $stmt = $mysqli->prepare($query);
            
                        if (!$stmt) {
                            echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta: ' . $mysqli->error]);
                            exit;
                        }
            
                        $stmt->bind_param('is', $id_usuario, $mensaje);
            
                        if (!$stmt->execute()) {
                            echo json_encode(['success' => false, 'message' => 'Error en la ejecución: ' . $stmt->error]);
                            exit;
                        }
            
                        echo json_encode(['success' => true, 'message' => 'Ticket enviado exitosamente.']);
                        $stmt->close();
                    }
                    
                    else {
                        echo json_encode(['success' => false, 'message' => 'Tipo de ticket no reconocido.']);
                    }
                }
            }

            if (isset($_GET['getProjectList']) && $_GET['getProjectList'] === 'true'){    
                $query = "SELECT proyectos.id_proyecto, proyectos.nombre FROM tbl_proyectos proyectos JOIN tbl_integrantes integrantes ON proyectos.id_proyecto = integrantes.id_proyecto WHERE integrantes.id_usuario = ? AND integrantes.responsable = 1 AND proyectos.estado = 1;";
    
                $stmt = $mysqli->prepare($query);
        
                if ($stmt) {
                    $stmt->bind_param('i', $id_usuario);
                    $stmt->execute();
                    
                    $result = $stmt->get_result();
        
                    if ($result->num_rows > 0) {
                        $projectsList = [];
                        while ($row = $result->fetch_assoc()) {
                            $projectsList[] = $row; // Lo convierto en array para poder usar map
                        }
                        // $_SESSION['projectSelected'] = $projectsList[0]['id_proyecto'];
                        echo json_encode(['success' => true, 'data' => $projectsList]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'No se encontraron proyectos a cargo del usuario actual']);
                    }
                $stmt->close();
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta']);
                }
                $mysqli->close();
            }


            if (isset($_GET['getMemberList']) && $_GET['getMemberList'] === 'true'){  
                if (isset($_GET['pid'])) {
                    $pid = filter_var($_GET['pid'], FILTER_VALIDATE_INT);
                    if($pid != false){      
                        $query = "SELECT proyectos.id_proyecto FROM tbl_proyectos proyectos JOIN tbl_integrantes integrantes ON proyectos.id_proyecto = integrantes.id_proyecto WHERE integrantes.id_usuario = ? AND integrantes.responsable = 1 AND proyectos.estado = 1;";
                        $params = [$id_usuario];
                        $types = "i";
                        $userProjects = Crud::executeResultQuery($query, $params, $types);
                        $proyectoIds = array_column($userProjects, 'id_proyecto');
                        if(is_array($proyectoIds) && in_array($pid, $proyectoIds)){  
                            $query = "SELECT integrantes.id_usuario, usuario.nombre, integrantes.responsable FROM tbl_integrantes integrantes JOIN tbl_usuarios usuario ON integrantes.id_usuario = usuario.id_usuario WHERE integrantes.id_proyecto = ?;";
                
                            $stmt = $mysqli->prepare($query);
                    
                            if ($stmt) {
                                $stmt->bind_param('i', $pid);
                                $stmt->execute();
                                
                                $result = $stmt->get_result();
                    
                                if ($result->num_rows > 0) {
                                    $usersList = [];
                                    while ($row = $result->fetch_assoc()) {
                                        if ($row['id_usuario'] != $_SESSION['id']) {
                                            $usersList[] = $row;
                                        }
                                    }
                                    echo json_encode(['success' => true, 'data' => $usersList]);
                                } else {
                                    echo json_encode(['success' => false, 'message' => 'No se encontraron usuarios registrados en este proyecto']);
                                }
                            $stmt->close();
                            } else {
                                echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta']);
                            }
                            $mysqli->close();

                        }else{
                            echo json_encode(['success' => false, 'message' => 'Id de proyecto alterado.']);
                        }
                    }
                }else{
                    echo json_encode(['success' => false, 'message' => 'Parametros de solicitud incorrectos o insuficientes.']);
                }
            }
        }
        
         else {
            echo json_encode(['success' => false, 'message' => 'No tienes permisos para enviar este ticket.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Rol de usuario no detectado.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método de solicitud no permitido.']);
}
?>
