<?php
session_start();
require_once '../controller/generalCRUD.php';
use Controller\GeneralCrud\Crud;

$destination = "../php/projectsManagement.php";
if ($_SESSION['rol']==='ADM' && $_SERVER["REQUEST_METHOD"] == "POST") {

    
    if (isset($_GET['editProject']) && $_GET['editProject'] == 'true' && isset($_GET['id'])) {
        $id =  $_GET['id'];
        //Actualizar tbl_integrantes
        if($_POST['membersTableFlagAdd'] === 'true'){
            $addedMembers = json_decode($_POST['addedMembers'], true);
            foreach ($addedMembers as $member) {
                $newMember = $member['usuarioId'];
                $rolGiven = $member['rol'];
                $query = "INSERT INTO tbl_integrantes (id_usuario, id_proyecto, responsable) VALUES (?, ?, ?)";
                $params = [$newMember, $id, $rolGiven];
                $types = "ssi";
                Crud::executeNonResultQuery2($query, $params, $types);
            }
        }
        if($_POST['membersTableFlagDel'] === 'true'){
            $removedMembers = json_decode($_POST['removedMembers'], true);
            foreach ($removedMembers as $member) {
                //Aun tenemos que lidear con las dependencias de las actividades.
                $query="DELETE FROM tbl_integrantes WHERE id_usuario = ? AND id_proyecto= ? ";
                $delMember = $member['idUsuario'];
                $params = [$delMember, $id];
                $types = "ii";
                Crud::executeNonResultQuery2($query, $params, $types);
            }
            echo"<script>console.log('Removing users');</script>";
        }


        //Actualizar tbl_proyectos
        $nombre = Crud::antiNaughty((string)$_POST['Fname']);
        $depto = Crud::antiNaughty((string)$_POST['eFdptoText']);
        $description = Crud::antiNaughty((string)$_POST['Fdescription']);
        $meta = Crud::antiNaughty((string)$_POST['Fmeta']);
        $fechaIni = $_POST['thisDate_inicio'];
        $fechaFin = $_POST['thisDate_cierre'];
    
        $query = "UPDATE tbl_proyectos SET nombre=?, descripción=?, meta=?, fecha_inicio=?, fecha_cierre=?, departamentoAsignado=? WHERE id_proyecto=?";
        $params = [$nombre, $description, $meta, $fechaIni, $fechaFin, $depto, $id];
        $types = "ssssssi";
        $destination = "$destination?editProject=$id";
        Crud::executeNonResultQuery($query, $params, $types, $destination);
        
    }


    if (isset($_GET['addProject']) && $_GET['addProject'] == 'true') {
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
            $query = "INSERT INTO tbl_proyectos (nombre, descripción, meta, departamentoAsignado, fecha_inicio, fecha_cierre) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $params = [$name, $description, $metas, $depto, $fechaInicio, $fechaCierre];
            Crud::executeNonResultQuery($query, $params, 'ssssss', $destination); 

            exit;
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }else{
        header("Location: $destination");
    }
}else{
    header("Location: $destination");
}
