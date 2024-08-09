<?php
session_start();
require_once '../controller/generalCRUD.php';
use Controller\GeneralCrud\Crud;

$destination = "../php/activityManagement.php";
if ($_SERVER["REQUEST_METHOD"] == "POST" && ($_SESSION['rol']==='ADM' || $_SESSION['rol']==='SAD' || $_SESSION['responsable'] === true)) {

    if (isset($_GET['addNew']) && $_GET['addNew'] == 'true') {
       
        $name = Crud::antiNaughty((string)$_POST['Fname']);
        $description = Crud::antiNaughty((string)$_POST['Fdescription']);
        $dateChckBox = $_POST['noDateSelected'];
        
        echo "<script>console.log('value: $dateChckBox');</script>";
        if($dateChckBox == 1){
            $fechaTermino = '00-00-0000';
        }else{
            $mes = $_POST['mes_meta'];
            $dia = $_POST['dia_meta'];
            $año = $_POST['anio_meta'];
            
            $fechaTermino= "$año-$mes-$dia";
        }
        $responsable = $_POST['responsableActividad'];
        $projectID = $_SESSION['projectSelected'];
        $idObjetivo = $_POST['objetivoEnlazado'];
        
        if($_SESSION['rol']==='ADM' || $_SESSION['rol']==='SAD'){
            $destination = "../php/activityManagement.php?id=$projectID";
        }else{
            $destination = "../php/activityManagement.php";
        }					
        $sql = "INSERT INTO `tbl_actividades` (`id_proyecto`, `id_usuario`, `nombre_actividad`, `descripción`, `estadoActual`, `participantes`, `fecha_estimada`, `id_objetivo`)  VALUES (?, ?, ?, ?, 1, null, ?, ?);";
        $params = [$projectID, $responsable, $name, $description, $fechaTermino, $idObjetivo];
        Crud::executeNonResultQuery($sql, $params, 'iisssi', $destination);
    
    }
   
}