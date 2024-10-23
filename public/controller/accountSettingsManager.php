<?php
session_start();
require_once '../controller/generalCRUD.php';
use Controller\GeneralCrud\Crud;

// Verificar que el usuario esté autenticado
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['rol']) && isset($_SESSION['nombre'])) {

    if(isset($_GET['verify']) && $_GET['verify'] == 'true'){
        $crud = new Crud();
        $mysqli = $crud->getMysqliConnection();
        $id_usuario = $_SESSION['id']; 
        $password = Crud::antiNaughty($_POST['password']);
    
        // Consulta para obtener los datos del usuario
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
    
    if(isset($_GET['updateData']) && $_GET['updateData'] == 'true'){
        $crud = new Crud();
        $mysqli = $crud->getMysqliConnection();
        $id_usuario = $_SESSION['id'];
        $name = Crud::antiNaughty($_POST['name']);
        $nickName = Crud::antiNaughty($_POST['nickName']);
        
        $query = "UPDATE tbl_usuarios SET nombre = ?, nickname = ? WHERE id_usuario = ?";
        $stmt = $mysqli->prepare($query);
    
        if ($stmt) {
            // Asegúrate de usar las variables correctas $name y $nickName
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
    

} else {
    echo"<script>window.location.href = `../php/actionsManagement.php`;</script>";
}
?>
