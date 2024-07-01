<?php
require_once 'controller/generalCRUD.php';
use Controller\GeneralCrud\Crud;

if (isset($_GET['login']) && $_GET['login']=='true') {
    session_start();
    include "controller/db_connection.php";
    if (isset($_POST['userMail']) && isset($_POST['password'])) {
        $userMail = trim($_POST['userMail']);
        $userMail = stripslashes($userMail);
        $userMail = htmlspecialchars($userMail);
        $password = trim($_POST['password']);
        $password = stripslashes($password);
        $password = htmlspecialchars($password);
    
        $sql = "SELECT * FROM tbl_usuarios WHERE correo='$userMail'";
        try {
            $result = mysqli_query($conn, $sql);
        } catch (mysqli_sql_exception $e) {
            echo "Error: " . $e->getMessage();
            exit();
        }
        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            if ($row['contrasena'] === md5($password)) {
                $_SESSION['id'] = $row['id_usuario'];
                $_SESSION['rol'] = $row['rolUsuario'];
                $_SESSION['nombre'] = $row['nombre'];
                $_SESSION['correo'] = $row['correo'];
                $_SESSION['departamento'] = $row['departamento'];
                $_SESSION['nickname'] = $row['nickname'];
                $_SESSION['projectSelected'] = 0;
                if ($_SESSION['rol'] == 'EST') {
                    $user=$_SESSION['id'];
                    $accountProjects=array();
                    $query = "SELECT responsable FROM tbl_integrantes WHERE id_usuario = '$user' AND responsable = 1;";
                    $accountProjects = Crud::executeResultQuery($query);
                    $_SESSION['responsable'] = Crud::isInArray($accountProjects, 1);
                    $_SESSION['responsable+2'] = Crud::isInArrayOver1Time($accountProjects, 1);
                } else {
                    $_SESSION['responsable'] = false;
                    $_SESSION['responsable+2'] = false;
                }
                header("Location: php/dashboard.php");
                exit();
            } else {
                header("Location: index.php?");
                exit();
            }
        }
    } else {
        header("Location: index.php?");
        exit();
    }
} elseif (isset($_GET['logout']) && $_GET['logout']=='true') {
    session_start();
    
    session_unset();
    session_destroy();
    
    header("Location: index.php");
}
