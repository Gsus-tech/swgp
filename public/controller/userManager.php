<?php
session_start();
if($_SESSION['rol']==='ADM' && $_SERVER["REQUEST_METHOD"] == "POST"){
    $destination = "userManagement.php";
    require("generalCRUD.php");
    
    if(isset($_GET['addUser']) && $_GET['addUser'] == 'true'){
        if($_POST['Fpassword'] === $_POST['FpasswordCon']){
            $userName = crud::antiNaughty((string)$_POST['Fname']);
            $depto = crud::antiNaughty((string)$_POST['Fdpto']);
            $mail = crud::antiNaughty($_POST['Fmail']);
            $password = md5($_POST['Fpassword']);
            $userType = '';
            $errorMsg = '';
            $e0 = $_POST['comboBoxUserType'];
            // $nickname = 
            $query = "INSERT INTO tbl_usuarios (rolUsuario,nombre,correo,contrasena,departamento,nickname) 
            VALUES('$e0','$userName','$mail','$password','$depto','$userName')";
            
            crud::executeNonResultQuery($query, $destination);
        }
    }

    if(isset($_GET['delete']) && $_GET['delete'] == 'true' && isset($_GET['deleteUser'])){
        $id = $_GET['deleteUser'];
        if (is_numeric($id)) {
            $dependency = checkForDependencies($id);
            $query = "DELETE FROM tbl_usuarios WHERE id_usuario='$id';";
            if($dependency==true){
                $resp = breakUserDependencies($id);
                crud::executeNonResultQuery($query, $destination);
            }
            else{
                crud::executeNonResultQuery($query, $destination);
            }
            echo "<script>window.location.href = '../php/userManagement.php';</script>";
        }
    }

    if(isset($_GET['updateUser']) && $_GET['updateUser'] == 'true'){
        $idToUpdate = $_POST['EditThisID'];
        $userName = (string)$_POST['Ename'];
        $depto = (string)$_POST['eFdpto'];
        $mail = $_POST['Email'];
        $userType = $_POST['comboBoxUserType'];
    
        $query = "UPDATE tbl_usuarios SET rolUsuario='$userType',nombre='$userName',correo='$mail',departamento='$depto' WHERE id_usuario=$idToUpdate";
    
        crud::executeNonResultQuery($query, $destination);
    } 

}else{
    if(isset($_GET['deleteAccounts'])){
        $ids = $_GET['deleteAccounts']; 
        $idsArray = explode(',', $ids);
        $idsArray = array_filter($idsArray, 'is_numeric');

        if (!empty($idsArray)) {
            $idsList = implode(',', array_map('intval', $idsArray));
            $query = "DELETE FROM tbl_usuarios WHERE id_usuario IN ($idsList)";
            $destination = "userManagement.php";
            require("generalCRUD.php");
            crud::executeNonResultQuery($query, $destination);
        }
    }else{
        echo "<script>window.location.href = '../php/dashboard.php';</script>";
    }
exit();
}


function checkForDependencies($id){
    $d = crud::executeResultQuery("SELECT id_actividad FROM tbl_actividades WHERE id_usuario = '$id';");
    if(count($d)>=1){
        return true;
    }
    else{
        return false;
    }
}

function breakUserDependencies($id){
    $dependecies = crud::executeResultQuery("SELECT id_actividad, id_proyecto FROM tbl_actividades WHERE id_usuario = '$id';");
    for($i=0;$i<count($dependecies);$i++){
        $act = $dependecies[$i][0];
        $project = $dependecies[$i][1];
        $f = crud::executeResultQuery("SELECT id_usuario FROM tbl_integrantes WHERE id_proyecto = '$project' AND responsable = 1;");
        $n = count($f);
        if($n>=1 && $f[0][0]!=$id || count($f)>=1 && $f[$n][0]!=$id){
            if($f[0][0]!=$id){
                $user = $f[0][0];
            }else{
                $user = $f[count($f)][0];
            }
        }else{
            $user = $_SESSION['id'];
        }
        $query = "UPDATE tbl_actividades SET id_usuario='$user' WHERE id_proyecto = '$project' AND id_actividad='$act' AND id_usuario = '$id'";
        return crud::executeNonResultQuery2($query);
        // echo "<script>console.log('Proyecto: $project - Actividad: $act - Usuario: $user');</script>";
    }
}

?>