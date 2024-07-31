<?php
session_start();
require_once '../controller/generalCRUD.php';
use Controller\GeneralCrud\Crud;

$destination = "../php/projectsManagement.php";
if ($_SESSION['rol']==='ADM' && $_SERVER["REQUEST_METHOD"] == "POST") {

    
    if (isset($_GET['editProject']) && $_GET['editProject'] == 'true' && isset($_GET['id'])) {
        $id =  $_GET['id'];
        $destination = $destination ."?editProject=". $id;
        //Actualizar tbl_integrantes
        if($_POST['membersTableFlagAdd'] === 'true'){
            echo "<script>console.log('QUERY: AddMember');</script>";
            $addedMembers = json_decode($_POST['addedMembers'], true);
            foreach ($addedMembers as $member) {
                $newMember = $member['usuarioId'];
                $rolGiven = $member['rol'];
                $query = "INSERT INTO tbl_integrantes (id_usuario, id_proyecto, responsable) VALUES (?, ?, ?)";
                $params = [$newMember, $id, $rolGiven];
                $types = "ssi";
                Crud::executeNonResultQuery2($query, $params, $types, $destination);
            }
        }
        if ($_POST['membersTableFlagDel'] === 'true') {
            echo "<script>console.log('QUERY: RemoveMember');</script>";
            $removedMembers = json_decode($_POST['removedMembers'], true);
        
            foreach ($removedMembers as $member) {
                $delMember = $member['idUsuario'];
                
                // Obtener actividades que tienen este usuario
                $query = "SELECT id_actividad FROM tbl_actividades WHERE id_proyecto = ? AND id_usuario = ?";
                $params = [$id, $delMember];
                $types = "ii";
                $actividades = Crud::executeResultQuery($query, $params, $types);
                
                // Obtener responsable del proyecto
                $query = "SELECT id_usuario FROM tbl_integrantes WHERE id_proyecto = ? AND responsable = 1";
                $repProyecto = Crud::executeResultQuery($query, [$id], "i");
                $nuevoResponsableId = $repProyecto[0]['id_usuario'] == $delMember ? $repProyecto[1]['id_usuario'] : $repProyecto[0]['id_usuario'];
        
                if (count($actividades) > 0) {
                    foreach ($actividades as $actividad) {
                        $actividadId = $actividad['id_actividad'];
                        
                        $query = "UPDATE tbl_actividades SET id_usuario = ? WHERE id_proyecto = ? AND id_usuario = ? AND id_actividad = ?";
                        $params = [$nuevoResponsableId, $id, $delMember, $actividadId];
                        $types = "iiii";
                        Crud::executeNonResultQuery2($query, $params, $types, $destination);                    
                    }
                }
        
                // Eliminar integrante del proyecto
                $query = "DELETE FROM tbl_integrantes WHERE id_usuario = ? AND id_proyecto = ?";
                $params = [$delMember, $id];
                $types = "ii";
                Crud::executeNonResultQuery2($query, $params, $types, $destination);
            }
        }
        
        //Actualizar tbl_objetivos
        if($_POST['objGTableFlagAdd'] === 'true'){      // agregar - general
            echo "<script>console.log('QUERY: AddObjectiveG');</script>";
            $addedObjectives = json_decode($_POST['addedObjG'], true);
            foreach ($addedObjectives as $objective) {
                $newObjective = $objective['newId'];
                $newContent = $objective['content'];
                $tipo = 'general';
                $query = "INSERT INTO tbl_objetivos (id_objetivo, id_proyecto, tipo, contenido) VALUES (?, ?, ?, ?)";
                $params = [$newObjective, $id, $tipo, $newContent];
                $types = "iiss";
                Crud::executeNonResultQuery2($query, $params, $types, $destination);
            }
        }
        if($_POST['objETableFlagAdd'] === 'true'){      // agregar - especifico
            echo "<script>console.log('QUERY: AddObjectiveE');</script>";
            $addedObjectives = json_decode($_POST['addedObjE'], true);
            foreach ($addedObjectives as $objective) {
                $newObjective = $objective['newId'];
                $newContent = $objective['content'];
                $tipo = 'especifico';
                $query = "INSERT INTO tbl_objetivos (id_objetivo, id_proyecto, tipo, contenido) VALUES (?, ?, ?, ?)";
                $params = [$newObjective, $id, $tipo, $newContent];
                $types = "iiss";
                Crud::executeNonResultQuery2($query, $params, $types, $destination);
            }
        }
        if($_POST['objGTableFlagUpd'] === 'true') {     // actualizar - general
            $updatedObjectives = json_decode($_POST['updatedObjG'], true);
            echo "<script>console.log('QUERY: UpdateObjectiveG');</script>";
            foreach ($updatedObjectives as $objective) {
                $objId = $objective['idObjetivo'];
                $newContent = Crud::antiNaughty($objective['nuevaDescripcion']);
                $tipo = 'general';

                $query = "UPDATE tbl_objetivos SET contenido = ? WHERE id_objetivo = ? AND id_proyecto = ? AND tipo = ?";
                $params = [$newContent, $objId, $id, $tipo];
                $types = "siis";
                
                Crud::executeNonResultQuery2($query, $params, $types, $destination);
            }
        }        
        if($_POST['objETableFlagUpd'] === 'true') {     // actualizar - especifico
            echo "<script>console.log('QUERY: UpdateObjectiveE');</script>";
            $updatedObjectives = json_decode($_POST['updatedObjE'], true);
            foreach ($updatedObjectives as $objective) {
                $objId = $objective['idObjetivo'];
                $newContent = Crud::antiNaughty($objective['nuevaDescripcion']);
                $tipo = 'especifico';
                $query = "UPDATE tbl_objetivos SET contenido = ? WHERE id_objetivo = ? AND id_proyecto = ? AND tipo = ?";
                $params = [$newContent, $objId, $id, $tipo];
                $types = "siis";
                Crud::executeNonResultQuery2($query, $params, $types, $destination);
            }
        }  
        if($_POST['objGTableFlagDel'] === 'true'){      // eliminar - general
            echo "<script>console.log('QUERY: RemoveObjectiveG');</script>";
            $removedObj = json_decode($_POST['removedObjG'], true);
            foreach ($removedObj as $objective) {
                $tipo = 'general';
                $objId = $objective['objId'];
                $query="DELETE FROM tbl_objetivos WHERE id_objetivo = ? AND id_proyecto = ? AND tipo = ?";
                $params = [$objId, $id, $tipo];
                $types = "iis";
                Crud::executeNonResultQuery2($query, $params, $types, $destination);
            }
        }
        if($_POST['objETableFlagDel'] === 'true'){      // eliminar - especifico
            echo "<script>console.log('QUERY: RemoveObjectiveE');</script>";
            $removedObj = json_decode($_POST['removedObjE'], true);
            foreach ($removedObj as $objective) {
                $tipo = 'especifico';
                $objId = $objective['objId'];
                $query="DELETE FROM tbl_objetivos WHERE id_objetivo = ? AND id_proyecto = ? AND tipo = ?";
                $params = [$objId, $id, $tipo];
                $types = "iis";
                Crud::executeNonResultQuery2($query, $params, $types, $destination);
            }
        }


        //Actualizar tbl_proyectos
        echo "<script>console.log('QUERY: GeneralChanges');</script>";
        $nombre = Crud::antiNaughty((string)$_POST['Fname']);
        $depto = Crud::antiNaughty((string)$_POST['eFdptoText']);
        $description = Crud::antiNaughty((string)$_POST['Fdescription']);
        $meta = Crud::antiNaughty((string)$_POST['Fmeta']);
        $fechaIni = $_POST['thisDate_inicio'];
        $fechaFin = $_POST['thisDate_cierre'];
    
        $query = "UPDATE tbl_proyectos SET nombre=?, descripción=?, meta=?, fecha_inicio=?, fecha_cierre=?, departamentoAsignado=? WHERE id_proyecto=?";
        $params = [$nombre, $description, $meta, $fechaIni, $fechaFin, $depto, $id];
        $types = "ssssssi";
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
            Crud::executeNonResultQuery2($query, $params, 'ssssss', $destination); 

            $newProjectId = Crud::getLastInserted('id_proyecto', 'tbl_proyectos');
            $flag = $newProjectId != null ? true : false;
            if($flag){
                header("Location: ../php/projectsManagement.php?editProject=$newProjectId");
            }

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
