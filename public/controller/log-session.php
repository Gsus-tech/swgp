<?php
require_once 'generalCRUD.php';
use Controller\GeneralCrud\Crud;

if (isset($_GET['login']) && $_GET['login']=='true') {
    session_start();
    include "db_connection.php";
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
            if(password_verify($password, $row['contrasena'])){
                date_default_timezone_set('America/Mexico_City');
                $_SESSION['id'] = $row['id_usuario'];
                $_SESSION['rol'] = $row['rolUsuario'];
                $_SESSION['nombre'] = $row['nombre'];
                $_SESSION['correo'] = $row['correo'];
                $_SESSION['departamento'] = $row['departamento'];
                $_SESSION['nickname'] = $row['nickname'];
                $_SESSION['projectSelected'] = 0;
                if ($_SESSION['rol'] == 'EST') {

                    $q1 = "SELECT i.id_proyecto
                    FROM tbl_integrantes AS i
                    JOIN tbl_proyectos AS p ON i.id_proyecto = p.id_proyecto
                    WHERE i.id_usuario = ? AND p.estado = 1;";
                    $pts = Controller\GeneralCrud\Crud::executeResultQuery($q1, [$_SESSION['id']], 'i');
                    $_SESSION['projectSelected'] = $pts[0]['id_proyecto'];
                    $user=$_SESSION['id'];
                    $accountProjects=array();
                    $query = "SELECT i.responsable
                        FROM tbl_integrantes AS i
                        JOIN tbl_proyectos AS p ON i.id_proyecto = p.id_proyecto
                        WHERE i.id_usuario = ? AND i.responsable = 1 AND p.estado = 1;";
                    $params = [$user];
                    $types = "i";
                    $accountProjects = Crud::executeResultQuery($query, $params, $types);
                    $_SESSION['responsable'] = Crud::isInArray($accountProjects, 1);
                    $_SESSION['responsable+2'] = Crud::isInArrayOver1Time($accountProjects, 1);

                    $queryForProjectCount = "SELECT responsable FROM tbl_integrantes WHERE id_usuario = ?";
                    $memberIn=array();
                    $memberIn = Crud::executeResultQuery($queryForProjectCount, $params, $types);
                    $_SESSION['varios-proyectos'] = count($memberIn) > 1 ? true : false;
                } else {
                    $_SESSION['responsable'] = false;
                    $_SESSION['responsable+2'] = false;
                    $_SESSION['varios-proyectos'] = false;
                }

                // Guardar las preferencias
                $q3 = "SELECT notificaciones,tema,tLetra FROM `tbl_preferencias_usuario` WHERE id_usuario = ?";
                $par3 = [$_SESSION['id']];
                $preferences = Crud::executeResultQuery($q3, $par3, 'i');
                if(count($preferences) > 0){
                    $_SESSION['notificaciones'] =$preferences[0]['notificaciones'];  
                    if($preferences[0]['tema'] !== 'Sistema'){
                        $_SESSION['tema'] = $preferences[0]['tema'] === 'Claro' ? 'lightMode' : 'darkMode';
                    }
                    if($preferences[0]['tLetra'] === 'Grande'){
                        $_SESSION['fontStyle'] = 'bigFont';
                    }
                }

                // Guardar los datos de la sesion
                $sessionId = session_id();
                $_SESSION['sessionId'] = $sessionId;
                $sessionQuery = "UPDATE tbl_usuarios SET id_sesion = ?, last_activity = NOW() WHERE id_usuario = ?";
                Crud::executeNonResultQuery2($sessionQuery, [$sessionId, $_SESSION['id']], 'si','../php/dashboard.php');

                header("Location: ../php/dashboard.php");
                exit();
            } else {
                header("Location: ../index.php?");
                exit();
            }

        }else {
            header("Location: ../index.php?");
            exit();
        }
    } else {
        header("Location: ../index.php");
        exit();
    }
} elseif (isset($_GET['logout']) && $_GET['logout']=='true') {
    session_start();
    
    $sessionQuery = "UPDATE tbl_usuarios SET id_sesion = NULL, last_activity = NULL WHERE id_usuario = ?";
                Crud::executeNonResultQuery2($sessionQuery, [$_SESSION['id']], 'i','../php/../index.php');
                
    session_unset();
    session_destroy();
    
    header("Location: ../index.php");

}
