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
   
    if (isset($_POST['delete']) && $_POST['delete'] == 'true' && isset($_POST['id']) && isset($_POST['rep'])) {
        $id = $_POST['id'];
        $rep = $_POST['rep'];
        $project = $_SESSION['projectSelected'];
        $query = "DELETE FROM tbl_actividades WHERE id_proyecto = ? AND id_actividad = ? AND id_usuario = ?";
        $params = [$project, $id, $rep];
        $types = "iii";
        $destination = "../php/activityManagement.php?id=$project";
        Crud::executeNonResultQuery2($query, $params, $types, $destination);
    }



    if (isset($_GET['actId']) && isset($_GET['moveToColumn'])) {
        $crud = new Crud();
        $mysqli = $crud->getMysqliConnection();
        $cardId = filter_var($_GET['actId'], FILTER_VALIDATE_INT);
        $columnId = filter_var($_GET['moveToColumn'], FILTER_VALIDATE_INT);
    
        if ($cardId !== false && $columnId !== false) {
            $cardIdEscaped = $mysqli->real_escape_string($cardId);
            $columnIdEscaped = $mysqli->real_escape_string($columnId);
    
            $sql = "UPDATE tbl_actividades SET estadoActual = '$columnIdEscaped' WHERE id_actividad = '$cardIdEscaped' AND id_proyecto = '". $_SESSION['projectSelected'] ."'";
    
            if ($mysqli->query($sql) === TRUE) {
                if ($mysqli->affected_rows > 0) {
                    echo json_encode(['success' => true, 'message' => 'Actividad actualizada.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'No se encontró la actividad o no se realizaron cambios.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al actualizar la actividad: ' . $mysqli->error]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Los datos proporcionados no son válidos.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
    }
    
    echo"<script>window.location.href = `$destination`;</script>";
}else{
    echo"<script>window.location.href = `$destination`;</script>";
}