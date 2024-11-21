<?php
session_start();
require_once '../controller/generalCRUD.php';
use Controller\GeneralCrud\Crud;

// Verificar que el usuario esté autenticado
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['rol']) && isset($_SESSION['nombre'])) {
    $destination = "../php/accountSettings.php";
    if(isset($_GET['verify']) && $_GET['verify'] == 'true'){
        $crud = new Crud();
        $mysqli = $crud->getMysqliConnection();
        $id_usuario = $_SESSION['id']; 
        $password = Crud::antiNaughty($_POST['password']);
    
        $query = "SELECT contrasena FROM tbl_usuarios WHERE id_usuario = ?";
        $stmt = $mysqli->prepare($query);
    
        if ($stmt) {
            $stmt->bind_param('i', $id_usuario);
            $stmt->execute();
            
            $result = $stmt->get_result();
    
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                if(password_verify($password, $row['contrasena'])){
                    echo json_encode(['success' => true, 'message' => 'Contraseña verificada.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Contraseña incorrecta.'.$row['contrasena']]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'No pudimos encontrar tu cuenta.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Error en la consulta de base de datos.']);
        }
    }
    

    if(isset($_GET['getUserInfo']) && $_GET['getUserInfo'] == 'true'){
        $crud = new Crud();
        $mysqli = $crud->getMysqliConnection();
        $id_usuario = $_SESSION['id']; 
    
        // Consulta para obtener los datos del usuario
        $query = "SELECT nombre, correo, departamento, nickname FROM tbl_usuarios WHERE id_usuario = ?";
        $stmt = $mysqli->prepare($query);
    
        if ($stmt) {
            $stmt->bind_param('i', $id_usuario);
            $stmt->execute();
            
            $result = $stmt->get_result();
    
            if ($result->num_rows > 0) {
                $userData = $result->fetch_assoc();
                echo json_encode(['success' => true, 'data' => $userData]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No se encontró el usuario.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Error en la consulta de base de datos.']);
        }
    }
   
    if(isset($_GET['setTheme']) && $_GET['setTheme'] == 'true'){
        $crud = new Crud();
        $mysqli = $crud->getMysqliConnection();
        $id_usuario = $_SESSION['id'];
        $theme = Crud::antiNaughty($_POST['theme']);
    
        if($theme === 'darkMode' || $theme === 'systemMode' || $theme === 'lightMode'){
            $tema = 'Sistema';
            if($theme === 'lightMode'){
                $tema = 'Claro';
            }else if($theme === 'darkMode'){
                $tema = 'Oscuro';
            }
            $query = "UPDATE tbl_preferencias_usuario SET tema = ? WHERE id_usuario = ?";
            $stmt = $mysqli->prepare($query);
            
            if ($stmt) {
                $stmt->bind_param('si', $tema, $id_usuario);
                $stmt->execute();
                
                if ($stmt->affected_rows > 0) {
                    echo json_encode(['success' => true, 'message' => 'Tema actualizado correctamente.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'No se encontró el usuario.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Error en la consulta de base de datos.']);
            }
        }else{
            echo json_encode(['success' => false, 'message' => 'Valor de tema seleccionado inválido.']);
        }
    }

    if(isset($_GET['setFontSize']) && $_GET['setFontSize'] == 'true'){
        $crud = new Crud();
        $mysqli = $crud->getMysqliConnection();
        $id_usuario = $_SESSION['id'];
        $fontSize = Crud::antiNaughty($_POST['fontSize']);
    
        if($fontSize === 'largeSize' || $fontSize === 'normalSize'){
            $fuente = 'Normal';
            if($fontSize === 'largeSize'){
                $fuente = 'Grande';
            }
            $query = "UPDATE tbl_preferencias_usuario SET tLetra = ? WHERE id_usuario = ?";
            $stmt = $mysqli->prepare($query);
            
            if ($stmt) {
                $stmt->bind_param('si', $fuente, $id_usuario);
                $stmt->execute();
                
                if ($stmt->affected_rows > 0) {
                    echo json_encode(['success' => true, 'message' => 'Tamaño de fuente actualizado correctamente.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'No se encontró el usuario.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Error en la consulta de base de datos.']);
            }
        }else{
            echo json_encode(['success' => false, 'message' => 'Tamaño de fuente seleccionado inválido.']);
        }
    }
    
    if(isset($_GET['resetPreferenceValues']) && $_GET['resetPreferenceValues'] == 'true'){
        $crud = new Crud();
        $mysqli = $crud->getMysqliConnection();
        $id_usuario = $_SESSION['id'];
        $fuente = 'Normal';
        $tema = 'Sistema';
        $query = "UPDATE tbl_preferencias_usuario SET tLetra = ?, tema = ? WHERE id_usuario = ?";
        $stmt = $mysqli->prepare($query);
        
        if ($stmt) {
            $stmt->bind_param('ssi', $fuente, $tema, $id_usuario);
            $stmt->execute();
            
            if ($stmt->affected_rows > 0) {
                echo json_encode(['success' => true, 'message' => 'Preferencias de usuario restablecidas correctamente.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'No se encontró el usuario.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Error en la consulta de base de datos.']);
        }
    }

    if (isset($_GET['updateNotifications']) && $_GET['updateNotifications'] == 'true') {
        $crud = new Crud();
        $mysqli = $crud->getMysqliConnection();
        $id_usuario = $_SESSION['id'];
        $type = Crud::antiNaughty($_POST['type']);
        $value = $_POST['value'];
    
        // Validar el tipo
        $validTypes = ['byMail', 'generales', 'personales'];
        if ($type === 'por correo') {
            $type = 'byMail';
        }
        if (!in_array($type, $validTypes)) {
            echo json_encode(['success' => false, 'message' => 'Tipo de notificación inválido.']);
            exit();
        }
    
        // Verificar y crear registro si no existe
        $existingRow = Crud::executeResultQuery("SELECT id_usuario FROM tbl_notificaciones WHERE id_usuario = ?", [$id_usuario], 'i', $destination);
        if (!$existingRow || count($existingRow) === 0) {
            Crud::executeNonResultQuery2("INSERT INTO tbl_notificaciones (id_usuario, generales, personales, byMail) VALUES (".$id_usuario.", 1, 0, 1)");
        }
        
        // Convertir el valor a booleano
        $newState = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        // Actualizar la tabla
        $query = "UPDATE tbl_notificaciones SET $type = ? WHERE id_usuario = ?";
        $stmt = $mysqli->prepare($query);
        if ($stmt) {
            $stmt->bind_param('ii', $newState, $id_usuario);
            $stmt->execute();
    
            if ($stmt->affected_rows > 0) {
                echo json_encode(['success' => true, 'message' => 'Notificaciones actualizadas.']);
            } else {
                echo json_encode(['success' => true, 'message' => 'No se realizaron cambios en las notificaciones. El valor ya estaba asignado.']);
            }
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Error en la consulta de base de datos.']);
        }
        
        $mysqli->close();
    }
    
    
    if(isset($_GET['updateData']) && $_GET['updateData'] == 'true'){
        $crud = new Crud();
        $mysqli = $crud->getMysqliConnection();
        $id_usuario = $_SESSION['id'];
        $name = Crud::antiNaughty($_POST['name']);
        $nickName = Crud::antiNaughty($_POST['nickName']);
        
        $query = "UPDATE tbl_usuarios SET nombre = ?, nickname = ? WHERE id_usuario = ?";
        $stmt = $mysqli->prepare($query);
    
        if ($stmt) {
            $stmt->bind_param('ssi', $name, $nickName, $id_usuario);
            $stmt->execute();
    
            if ($stmt->affected_rows > 0) {
                echo json_encode(['success' => true, 'message' => 'Datos actualizados correctamente.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'No se realizaron cambios.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Error en la preparación de la consulta.']);
        }
    }
    
    if(isset($_GET['notificationToggle']) && $_GET['notificationToggle'] == 'true'){
        $id_usuario = $_SESSION['id'];
        $query = "SELECT notificaciones FROM `tbl_preferencias_usuario` WHERE id_usuario = ?";
        $params = [$id_usuario];
        $currentSetting = Crud::executeResultQuery($query, $params, 'i');
        $newSetting = $currentSetting[0]['notificaciones'] === 1 ? 0 : 1;

        $crud = new Crud();
        $mysqli = $crud->getMysqliConnection();
        
        $query = "UPDATE tbl_preferencias_usuario SET notificaciones = ? WHERE id_usuario = ?";
        $stmt = $mysqli->prepare($query);
    
        if ($stmt) {
            $stmt->bind_param('ii', $newSetting, $id_usuario);
            $stmt->execute();
    
            if ($stmt->affected_rows > 0) {
                echo json_encode(['success' => true, 'message' => 'Ajustes de notificaciones actualizados.', 'newState' => $newSetting]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No se realizaron cambios.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Error en la preparación de la consulta.']);
        }
    }
    
    if(isset($_GET['updatePassword']) && $_GET['updatePassword'] == 'true'){
        $crud = new Crud();
        $mysqli = $crud->getMysqliConnection();
        $id_usuario = $_SESSION['id'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        $query = "UPDATE tbl_usuarios SET contrasena = ? WHERE id_usuario = ?";
        $stmt = $mysqli->prepare($query);
    
        if ($stmt) {
            $stmt->bind_param('si', $password, $id_usuario);
            $stmt->execute();
    
            if ($stmt->affected_rows > 0) {
                echo json_encode(['success' => true, 'message' => 'Datos actualizados correctamente.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'No se realizaron cambios.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Error en la preparación de la consulta.']);
        }
    }

    if(isset($_GET['getNotificationPreferences']) && $_GET['getNotificationPreferences'] == 'true'){
        $crud = new Crud();
        $mysqli = $crud->getMysqliConnection();
        $id_usuario = $_SESSION['id'];
        
        if($_SESSION['notificaciones']===0){
            echo json_encode(['success' => false, 'message' => 'Las notificaciones están desactivadas.']);
            exit();
        }

        $query = "SELECT * FROM `tbl_notificaciones` WHERE id_usuario = ?";
        $stmt = $mysqli->prepare($query);
    
        if ($stmt) {
            $stmt->bind_param('i', $id_usuario);
            $stmt->execute();
            
            $result = $stmt->get_result();
    
            if ($result->num_rows > 0) {
                $notificaciones = $result->fetch_assoc();
                echo json_encode(['success' => true, 'result' => $notificaciones]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No se pudo recuperar las preferencias de notificaciones de la base de datos.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Error en la preparación de la consulta.']);
        }
    }

    // Usar solo al implementar la actualizacion.
    // Cambia el nombre de la funcion por el parametro del boton que utilizaras.
    // Para realizar los cambios en el sistema online, recuerda actualizar la tabla notificaciones, crear la tabla logs y agregar los triggers.
    function initializeNotificationsForUsers() {
        include "db_connection.php"; // Asegúrate de incluir tu archivo de conexión
    
        $mysqli = new mysqli($sname, $userN, $pass, $db_name);
    
        if ($mysqli->connect_error) {
            die('Error de Conexión (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
        }
    
        try {
            // Consulta para obtener todos los usuarios
            $query = "SELECT id_usuario FROM tbl_usuarios";
            $result = $mysqli->query($query);
    
            if ($result) {
                // Preparar la consulta de inserción
                $insertQuery = "INSERT INTO tbl_notificaciones (id_usuario, generales, personales, byMail) VALUES (?, 0, 1, 0)";
                $stmt = $mysqli->prepare($insertQuery);
    
                if (!$stmt) {
                    throw new Exception("Error al preparar la consulta: " . $mysqli->error);
                }
    
                // Iterar sobre los usuarios y verificar si ya tienen un registro en tbl_notificaciones
                while ($row = $result->fetch_assoc()) {
                    $id_usuario = $row['id_usuario'];
    
                    // Verificar si el registro ya existe
                    $checkQuery = "SELECT COUNT(*) AS count FROM tbl_notificaciones WHERE id_usuario = ?";
                    $checkStmt = $mysqli->prepare($checkQuery);
                    $checkStmt->bind_param('i', $id_usuario);
                    $checkStmt->execute();
                    $checkResult = $checkStmt->get_result();
                    $checkRow = $checkResult->fetch_assoc();
    
                    if ($checkRow['count'] == 0) {
                        // Insertar solo si no existe
                        $stmt->bind_param('i', $id_usuario);
                        $stmt->execute();
                    }
    
                    $checkStmt->close();
                }
    
                echo "Notificaciones inicializadas correctamente.";
                $stmt->close();
            } else {
                throw new Exception("Error al obtener usuarios: " . $mysqli->error);
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        } finally {
            $mysqli->close();
        }
    }

} else {
    echo"<script>window.location.href = `../php/accountSettings.php`;</script>";
}
?>
