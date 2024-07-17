<?php
session_start();
require_once '../controller/generalCRUD.php';
use Controller\GeneralCrud\Crud;

if ($_SESSION['rol']==='ADM' && $_SERVER["REQUEST_METHOD"] == "POST") {
    $destination = "projectsManagement.php";

    
    if (isset($_GET['editProject']) && $_GET['editProject'] == 'true' && isset($_GET['id'])) {
        $id = $_GET['id'];
        $nombre = (string)$_POST['Fname'];
        $description = (string)$_POST['eFdpto'];
        $meta = $_POST['Email'];
        $fechaIni = $_POST['thisDate_inicio'];
        $fechaFin = $_POST['thisDate_cierre'];
    
        $query = "UPDATE tbl_proyectos SET nombre=?, descripción=?, meta=?, fecha_inicio=?, fecha_cierre=? WHERE id_proyecto=?";
        $params = [$nombre, $description, $meta, $fechaIni, $fechaFin, $id];
        $types = "sssssi";
        Crud::executeNonResultQuery($query, $params, $types, $destination);
    
    }

}