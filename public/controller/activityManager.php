<?php
session_start();
require_once '../controller/generalCRUD.php';
use Controller\GeneralCrud\Crud;

$destination = "../php/activityManagement.php";
if ($_SERVER["REQUEST_METHOD"] == "POST" && ($_SESSION['rol']==='ADM' || $_SESSION['rol']==='SAD' || $_SESSION['responsable'] === true)) {

    if (isset($_GET['addNew']) && $_GET['addNew'] == 'true') {   
        $name = Crud::antiNaughty((string)$_POST['Fname']);
        $description = Crud::antiNaughty((string)$_POST['Fdescription']);
        $responsable = (int)$_POST['responsableActividad'];
        $projectID = $_SESSION['projectSelected'];
        $idObjetivo = (int)$_POST['objetivoEnlazado'];
        //Validar fecha ->
        $fechaTermino = $_POST['Fdate'];
        $format = 'Y-m-d';
        $fechaValida = DateTime::createFromFormat($format, $fechaTermino);

        if($_SESSION['rol']==='ADM' || $_SESSION['rol']==='SAD'){
            $destination = "../php/activityManagement.php?id=$projectID";
        }else{
            $destination = "../php/activityManagement.php";
        }					

        if (!$fechaValida && !$fechaValida->format($format) === $fechaTermino) {
            $destination .= "?error=" . urlencode('Error en el registro de la fecha. Formato de datos incorrecto.');
            $fechaTermino = new Date();
        }

        $sql = "INSERT INTO `tbl_actividades` (`id_proyecto`, `id_usuario`, `nombre_actividad`, `descripción`, `estadoActual`, `participantes`, `fecha_estimada`, `id_objetivo`)  VALUES (?, ?, ?, ?, 1, null, ?, ?);";
        $params = [$projectID, $responsable, $name, $description, $fechaTermino, $idObjetivo];
        Crud::executeNonResultQuery($sql, $params, 'iisssi', $destination);
    }

    if (isset($_GET['editId']) && filter_var($_GET['editId'], FILTER_VALIDATE_INT) !== false && $_GET['editActivity'] === 'true') {
        $name = Crud::antiNaughty((string)$_POST['Fname']);
        $description = Crud::antiNaughty((string)$_POST['Fdescription']);
        $responsable = (int)$_POST['userRespList'];
        $projectID = $_SESSION['projectSelected'];
        $idObjetivo = (int)$_POST['objetivoList'];
        $activityId = (int)$_GET['editId']; 
        //Validar fecha ->
        $fechaTermino = $_POST['Fdate'];
        $format = 'Y-m-d';
        $fechaValida = DateTime::createFromFormat($format, $fechaTermino);

        if($_SESSION['rol']==='ADM' || $_SESSION['rol']==='SAD'){
            $destination = "../php/activityManagement.php?id=$projectID";
        }else{
            $destination = "../php/activityManagement.php";
        }					

        if (!$fechaValida || $fechaValida->format($format) !== $fechaTermino) {
            $destination .= "?error=" . urlencode('Error en el registro de la fecha. Formato de datos incorrecto.');
            $fechaTermino = (new DateTime())->format('Y-m-d'); // Usar la fecha actual si la validación falla
        }

        $sql = "UPDATE `tbl_actividades` 
            SET `nombre_actividad` = ?, `descripción` = ?, `id_usuario` = ?, `fecha_estimada` = ?, `id_objetivo` = ? 
            WHERE `id_actividad` = ? AND `id_proyecto` = ?";
        $params = [$name, $description, $responsable, $fechaTermino, $idObjetivo, $activityId, $projectID];
        Crud::executeNonResultQuery($sql, $params, 'ssisiii', $destination);
        
        // if(isset($_GET['changeResponsible']) && $_GET['changeResponsible'] === 'true'){
        //     $responsable = filter_var($_POST['userRespList'], FILTER_VALIDATE_INT);
        //     $sql = "UPDATE `tbl_actividades` 
        //     SET `id_usuario` = ?
        //     WHERE `id_actividad` = ? AND `id_proyecto` = ?";
        //     $params = [$responsable, $activityId, $projectID];
        //     Crud::executeNonResultQuery($sql, $params, 'ssisiii', $destination);
        // }
    }
   
    if (isset($_POST['delete']) && $_POST['delete'] == 'true' && isset($_POST['id']) && isset($_POST['rep'])) {
        $project = $_SESSION['projectSelected'];
        $destiny = isset($_POST['dsh']) ? 'dashboard.php' : 'activityManagement.php';
        $urlParams = $_SESSION['rol'] == 'EST' ? "" : "?id=" . $project;
        $destination = "../php/" . $destiny . $urlParams;

        $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
        $rep = filter_var($_POST['rep'], FILTER_VALIDATE_INT);
        
        if ($id === false || $rep === false || $project === false) {
            $errorMsg = urlencode("Parámetros inválidos proporcionados.");
            header("Location: $destination?error=$errorMsg");
            exit();
        }

        $query = "DELETE FROM tbl_actividades WHERE id_proyecto = ? AND id_actividad = ? AND id_usuario = ?";
        $params = [$project, $id, $rep];
        $types = "iii";
        Crud::executeNonResultQuery($query, $params, $types, $destination);
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
    } 

    if (isset($_GET['getMembers']) && $_GET['getMembers'] === 'true') {
        $crud = new Crud();
        $mysqli = $crud->getMysqliConnection();
    
        $projectSelected = $_SESSION['projectSelected'];
    
        if (filter_var($projectSelected, FILTER_VALIDATE_INT) !== false) {
            $query = "SELECT tbl_integrantes.id_usuario, tbl_usuarios.nombre FROM tbl_integrantes 
            JOIN tbl_usuarios ON tbl_integrantes.id_usuario = tbl_usuarios.id_usuario 
            WHERE tbl_integrantes.id_proyecto = ?";
            $stmt = $mysqli->prepare($query);
    
            if ($stmt) {
                $stmt->bind_param('i', $projectSelected);
                $stmt->execute();
                
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $members = $result->fetch_all(MYSQLI_ASSOC);
                    echo json_encode(['success' => true, 'members' => $members]);
                } else {
                    // En caso que no hayan objetivos registrados...
                    echo json_encode(['success' => false, 'message' => 'No se encontraron integrantes']);
                }
                $stmt->close();
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'ID de proyecto no válido']);
        }
        $mysqli->close();
    } 

    if (isset($_GET['getObjectives']) && $_GET['getObjectives'] === 'true') {
        $crud = new Crud();
        $mysqli = $crud->getMysqliConnection();
    
        $projectSelected = $_SESSION['projectSelected'];
        $tipo = 'especifico';
    
        if (filter_var($projectSelected, FILTER_VALIDATE_INT) !== false) {
            $query = "SELECT id_objetivo, contenido FROM tbl_objetivos WHERE id_proyecto = ? AND tipo = ?";
            $stmt = $mysqli->prepare($query);
    
            if ($stmt) {
                $stmt->bind_param('is', $projectSelected, $tipo);
                $stmt->execute();
                
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $objectives = $result->fetch_all(MYSQLI_ASSOC);
                    echo json_encode(['success' => true, 'objectives' => $objectives]);
                } else {
                    // En caso que no hayan objetivos registrados...
                    echo json_encode(['success' => false, 'message' => 'No se encontraron objetivos']);
                }
                $stmt->close();
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'ID de proyecto no válido']);
        }
        $mysqli->close();
    } 
    
    if (isset($_GET['getActivityInfo']) && $_GET['getActivityInfo'] === 'true' && isset($_GET['activityId'])) {
        $crud = new Crud();
        $mysqli = $crud->getMysqliConnection();
        $activityId = filter_var($_GET['activityId'], FILTER_VALIDATE_INT);
    
        if ($activityId !== false) {
            $query = "SELECT actividades.nombre_actividad, actividades.descripción, actividades.fecha_estimada, actividades.id_usuario, actividades.id_objetivo, objetivos.contenido AS objetivo_descripcion
          FROM tbl_actividades actividades 
          JOIN tbl_objetivos objetivos ON actividades.id_objetivo = objetivos.id_objetivo 
          WHERE actividades.id_actividad = ?";

            $stmt = $mysqli->prepare($query);
    
            if ($stmt) {
                // Solo enlazamos el ID de la actividad como parámetro
                $stmt->bind_param('i', $activityId);
                $stmt->execute();
                
                $result = $stmt->get_result();
    
                if ($result->num_rows > 0) {
                    $activityData = $result->fetch_assoc();
    
                    // Enviar los resultados de la consulta al archivo JS
                    echo json_encode(['success' => true, 'data' => $activityData]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'No se encontró la actividad']);
                }
                $stmt->close();
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta']);
            }
            $mysqli->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'ID de actividad no válido']);
        }
    }

    if (isset($_GET['getProjectDates']) && $_GET['getProjectDates'] === 'true') {
        $crud = new Crud();
        $mysqli = $crud->getMysqliConnection();
    
        $projectId = $_SESSION['projectSelected'];  // Obtener el ID del proyecto desde la sesión
    
        $query = "SELECT fecha_inicio, fecha_cierre FROM tbl_proyectos WHERE id_proyecto = ?";
    
        $stmt = $mysqli->prepare($query);
    
        if ($stmt) {
            $stmt->bind_param('i', $projectId);  // Enlazar el ID del proyecto como parámetro
            $stmt->execute();
            
            $result = $stmt->get_result();
    
            if ($result->num_rows > 0) {
                $projectDates = $result->fetch_assoc();
                echo json_encode(['success' => true, 'data' => $projectDates]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No se encontró el proyecto']);
            }
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta']);
        }
    
        $mysqli->close();
    }


    if (isset($_GET['getRepId']) && $_GET['getRepId'] === 'true') {
        $crud = new Crud();
        $mysqli = $crud->getMysqliConnection();  // Usar la conexión de $crud
        $id_actividad = filter_var($_POST['id_actividad'], FILTER_VALIDATE_INT); 
        $id_proyecto = $_SESSION['projectSelected'];  // Obtener el proyecto de la sesión
    
        if ($id_actividad !== false) {
            $query = "SELECT id_usuario FROM tbl_actividades WHERE id_proyecto = ? AND id_actividad = ?";
            if ($stmt = $mysqli->prepare($query)) {
                $stmt->bind_param('ii', $id_proyecto, $id_actividad);
                $stmt->execute();
                $stmt->bind_result($id_usuario);
                if ($stmt->fetch()) {
                    echo json_encode(['success' => true, 'id_usuario' => $id_usuario]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'No se encontró el usuario para esta actividad.']);
                }
                $stmt->close();
            } else {
                echo json_encode(['success' => false, 'message' => 'Error en la consulta SQL.']);
            }
            $mysqli->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Faltan parámetros en la solicitud.']);
        }
    }
    
}else{
    echo"<script>window.location.href = `$destination`;</script>";
}