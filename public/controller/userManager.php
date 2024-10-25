<?php
session_start();
require_once '../controller/generalCRUD.php';
use Controller\GeneralCrud\Crud;
if (($_SESSION['rol']==='ADM' || $_SESSION['rol']==='SAD') && $_SERVER["REQUEST_METHOD"] == "POST" && isset($_GET['updateUser'])) {
    if ($_GET['updateUser'] == 'true') {
        $idToUpdate = isset($_POST['EditThisID']) ? (int)$_POST['EditThisID'] : null;
        $userName = Crud::antiNaughty((string)$_POST['Ename']);
        $depto = Crud::antiNaughty((string)$_POST['eFdpto']);
        $mail = filter_var($_POST['Email'], FILTER_SANITIZE_EMAIL);
        if($idToUpdate != null && !containsSpecialCharacters($userName) && !containsSpecialCharacters($depto)){
            if($_SESSION['rol']=='ADM'){
                $userType = htmlspecialchars($_POST['comboBoxUserType'], ENT_QUOTES, 'UTF-8');
                $query = "UPDATE tbl_usuarios SET rolUsuario=?, nombre=?, correo=?, departamento=? WHERE id_usuario=?";
                $params = [$userType, $userName, $mail, $depto, $idToUpdate];
                $types = "ssssi";
            }else{
                $query = "UPDATE tbl_usuarios SET nombre=?, correo=?, departamento=? WHERE id_usuario=?";
                $params = [$userName, $mail, $depto, $idToUpdate];
                $types = "sssi";
            }
            $destination = "userManagement.php";
            Crud::executeNonResultQuery($query, $params, $types, $destination);
        }else{
            error_log("Intento fallido de actualización: El formato de alguno de los datos no era correcto.", 0);
            header('Location: ../php/userManagement.php?error='. urlencode('Intento fallido de actualización. ID de usuario inválido o no proporcionado.'));
            exit();
        }
    }
}else if ($_SESSION['rol']==='ADM' && $_SERVER["REQUEST_METHOD"] == "POST") {
    
    $destination = "userManagement.php";
    
    if (isset($_GET['addUser']) && $_GET['addUser'] == 'true') {
        if ($_POST['Fpassword'] === $_POST['FpasswordCon']) {
            $userName = Crud::antiNaughty((string)$_POST['Uname']);
            $depto = Crud::antiNaughty((string)$_POST['Fdpto']);
            $mail = filter_var($_POST['Fmail'], FILTER_SANITIZE_EMAIL);
            $password = password_hash($_POST['Fpassword'], PASSWORD_DEFAULT);
            $userType = htmlspecialchars($_POST['comboBoxUserType'], ENT_QUOTES, 'UTF-8');

            //Obtener el nickName
            $partes = explode(' ', $userName);
            $nickName = isset($partes[1]) ? $partes[0] . ' ' . substr($partes[1], 0, 1) . '.' : $partes[0];

            
            if (!containsSpecialCharacters($userName) && !containsSpecialCharacters($depto)) {
                $query = "INSERT INTO tbl_usuarios (rolUsuario, nombre, correo, contrasena, departamento, nickname)
                        VALUES (?, ?, ?, ?, ?, ?)";
                $params = [$userType, $userName, $mail, $password, $depto, $nickName];
                $types = "ssssss";
                
                Crud::executeNonResultQuery($query, $params, $types, $destination);
            }
            else {
                error_log("Intento fallido de creación de cuenta. El formato de alguno de los datos no era correcto.", 0);
                header('Location: ../php/userManagement.php?error='. urlencode('Intento fallido de creación de cuenta. Formato de datos no válido.'));
                exit();
            }
        }else {
            error_log("Intento fallido de creación de cuenta. Las contraseñas no coinciden.", 0);
            header('Location: ../php/userManagement.php?error='. urlencode('Intento fallido de creación de cuenta. Las contraseñas no coincidieron.'));
            exit();
        }
    }

    if (isset($_GET['delete']) && $_GET['delete'] == 'true' && isset($_GET['deleteUser'])) {
        $id = $_GET['deleteUser'];
        if (is_numeric($id)) {
            $dependency = checkForDependencies($id);
            $query = "DELETE FROM tbl_usuarios WHERE id_usuario = ?";
            $params = [$id];
            $types = "i";
            
            if ($dependency == true) {
                $resp = breakUserDependencies($id);
            }
            Crud::executeNonResultQuery($query, $params, $types, $destination);
        } else {
            error_log("Intento fallido de eliminación de cuenta. El id proporcionado es inválido.", 0);
            header('Location: ../php/userManagement.php?error='. urlencode('Intento fallido de eliminación de cuenta. El id proporcionado es inválido.'));
            exit();
        }
    }    

    
} else {
    if (isset($_GET['deleteAccounts'])) {
        $ids = $_GET['deleteAccounts'];
        $idsArray = explode(',', $ids);
        $idsArray = array_filter($idsArray, 'is_numeric');
    
        if (!empty($idsArray)) {

            foreach ($idsArray as $id) {
                $dependency = checkForDependencies($id);
                if ($dependency == true) {
                    $resp = breakUserDependencies($id);
                }
            }

            $placeholders = implode(',', array_fill(0, count($idsArray), '?'));
            $query = "DELETE FROM tbl_usuarios WHERE id_usuario IN ($placeholders)";
            $params = $idsArray;
            $types = str_repeat('i', count($idsArray));
            
            $destination = "userManagement.php";
            Crud::executeNonResultQuery($query, $params, $types, $destination);
        }
    } else {
        echo "<script>window.location.href = '../php/dashboard.php';</script>";
    }
    
    exit();
}


function checkForDependencies($id) {
    $query = "SELECT id_actividad FROM tbl_actividades WHERE id_usuario = ?";
    $params = [$id];
    $types = "i";
    $d = Crud::executeResultQuery($query, $params, $types);
    
    return count($d) >= 1;
}


function breakUserDependencies($id) {
    $query = "SELECT id_actividad, id_proyecto FROM tbl_actividades WHERE id_usuario = ?";
    $params = [$id];
    $types = "i";
    $dependencies = Crud::executeResultQuery($query, $params, $types);
    
    foreach ($dependencies as $dependency) {
        $act = $dependency['id_actividad'];
        $project = $dependency['id_proyecto'];
        
        $query = "SELECT id_usuario FROM tbl_integrantes WHERE id_proyecto = ? AND responsable = 1";
        $params = [$project];
        $types = "i";
        $f = Crud::executeResultQuery($query, $params, $types);
        
        $n = count($f);
        $user = $_SESSION['id'];
        
        if ($n >= 1 && $f[0]['id_usuario'] != $id) {
            $user = $f[0]['id_usuario'];
        } elseif ($n >= 1 && $f[$n - 1]['id_usuario'] != $id) {
            $user = $f[$n - 1]['id_usuario'];
        }
        
        $updateQuery = "UPDATE tbl_actividades SET id_usuario = ? WHERE id_proyecto = ? AND id_actividad = ? AND id_usuario = ?";
        $updateParams = [$user, $project, $act, $id];
        $updateTypes = "iiii";
        Crud::executeNonResultQuery2($updateQuery, $updateParams, $updateTypes, "../php/userManagement.php");
    }
}

function containsSpecialCharacters($string) {
    $pattern = '/[^a-zA-Z0-9 áéíóúÁÉÍÓÚ]/';
    if (preg_match($pattern, $string)) {
        return true;
    } else {
        return false;
    }
}