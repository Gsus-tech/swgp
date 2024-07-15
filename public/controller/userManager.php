<?php
session_start();
require_once '../controller/generalCRUD.php';
use Controller\GeneralCrud\Crud;

if ($_SESSION['rol']==='ADM' && $_SERVER["REQUEST_METHOD"] == "POST") {
    $destination = "userManagement.php";
    
    if (isset($_GET['addUser']) && $_GET['addUser'] == 'true') {
        if ($_POST['Fpassword'] === $_POST['FpasswordCon']) {
            $userName = Crud::antiNaughty((string)$_POST['Fname']);
            $depto = Crud::antiNaughty((string)$_POST['Fdpto']);
            $mail = Crud::antiNaughty($_POST['Fmail']);
            $password = md5($_POST['Fpassword']);
            $userType = $_POST['comboBoxUserType'];
            
            $query = "INSERT INTO tbl_usuarios (rolUsuario, nombre, correo, contrasena, departamento, nickname) 
                      VALUES (?, ?, ?, ?, ?, ?)";
            $params = [$userType, $userName, $mail, $password, $depto, $userName];
            $types = "ssssss";
            
            Crud::executeNonResultQuery($query, $params, $types, $destination);
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
            echo "<script>window.location.href = '../php/userManagement.php';</script>";
        }
    }    

    if (isset($_GET['updateUser']) && $_GET['updateUser'] == 'true') {
        $idToUpdate = $_POST['EditThisID'];
        $userName = (string)$_POST['Ename'];
        $depto = (string)$_POST['eFdpto'];
        $mail = $_POST['Email'];
        $userType = $_POST['comboBoxUserType'];
    
        $query = "UPDATE tbl_usuarios SET rolUsuario=?, nombre=?, correo=?, departamento=? WHERE id_usuario=?";
        $params = [$userType, $userName, $mail, $depto, $idToUpdate];
        $types = "ssssi";
        Crud::executeNonResultQuery($query, $params, $types, $destination);
    }
} else {
    if (isset($_GET['deleteAccounts'])) {
        $ids = $_GET['deleteAccounts'];
        $idsArray = explode(',', $ids);
        $idsArray = array_filter($idsArray, 'is_numeric');
    
        if (!empty($idsArray)) {
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
        Crud::executeNonResultQuery($updateQuery, $updateParams, $updateTypes, null);
    }
}

