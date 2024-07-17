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

    $name = Crud::antiNaughty((string)$_POST['Pname']);
    $dp = $_POST['dropDownDepto'];
    $Deptos = Crud::getFiltersOptions('tbl_usuarios', 'departamento');
    $depto = '';

    if ($dp == 'other') {
        $depto = Crud::antiNaughty((string)$_POST['newDepto']);
    } else {
        foreach ($Deptos as $i => $deptoArray) {
            foreach ($deptoArray as $key => $value) {
                if ($i == $dp) {
                    $depto = $value;
                }
            }
        }
    }

    $description = Crud::antiNaughty((string)$_POST['Fdescription']);
    $metas = Crud::antiNaughty((string)$_POST['Fmeta']);
    $mes1 = (int)$_POST['mes_inicio'];
    $dia1 = (int)$_POST['dia_inicio'];
    $año1 = (int)$_POST['anio_inicio'];
    $mes2 = (int)$_POST['mes_cierre'];
    $dia2 = (int)$_POST['dia_cierre'];
    $año2 = (int)$_POST['anio_cierre'];

    $fechaInicio = sprintf('%04d-%02d-%02d', $año1, $mes1, $dia1);
    $fechaCierre = sprintf('%04d-%02d-%02d', $año2, $mes2, $dia2);

    if (!checkdate($mes1, $dia1, $año1) || !checkdate($mes2, $dia2, $año2)) {
        echo "Invalid date provided.";
        exit;
    }

    try {
        $destino = "../php/projectsManagement.php";
        $query = "INSERT INTO tbl_proyectos (nombre, descripción, meta, departamentoAsignado, fecha_inicio, fecha_cierre) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $params = [$name, $description, $metas, $depto, $fechaInicio, $fechaCierre];
        Crud::executeNonResultQuery($query, $params, 'ssssss', $destino); 

        exit;
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

