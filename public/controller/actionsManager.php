<?php
session_start();
require_once '../controller/generalCRUD.php';
use Controller\GeneralCrud\Crud;

// Verificar que el usuario esté autenticado
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['rol']) && isset($_SESSION['nombre'])) {

    if(isset($_GET['ajaxUpdate']) && $_GET['ajaxUpdate'] == 'true'){
        $crud = new Crud();
        $mysqli = $crud->getMysqliConnection();
        $activityId = filter_var($_GET['activityId'], FILTER_VALIDATE_INT);
    
        if ($activityId !== false) {
            $query = "SELECT estadoActual, (SELECT COUNT(*) FROM tbl_avances WHERE id_actividad = ?) as numeroReportes FROM tbl_actividades WHERE id_actividad = ?";

            $stmt = $mysqli->prepare($query);
    
            if ($stmt) {
                // Solo enlazamos el ID de la actividad como parámetro
                $stmt->bind_param('ii', $activityId, $activityId);
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
        }
    }


    if(isset($_GET['saveNewReport']) && $_GET['saveNewReport'] == 'true'){
        if (isset($_POST['contenido']) && isset($_POST['id_actividad'])) {
            $id_usuario = $_SESSION['id']; 
            $id_proyecto = $_SESSION['projectSelected'];
            $id_actividad = filter_var($_POST['id_actividad'], FILTER_VALIDATE_INT); 
            $nombre = trim($_POST['nombreReporte']);
            $contenidoJSON = $_POST['contenido']; 
        
            if($id_actividad === false){
                $_SESSION['error_message'] = "ID de actividad inválido.";
                header("Location: ../php/actionsManagement.php");
                exit();
            }

            if (strlen($nombre) > 255) {
                $nombre = substr($nombre, 0, 255);
            }
        
            $query = "INSERT INTO tbl_avances (contenido, nombre, id_usuario, id_proyecto, id_actividad) VALUES (?, ?, ?, ?, ?)";
            $params = [$contenidoJSON, $nombre, $id_usuario, $id_proyecto, $id_actividad];
            $types = "ssiii";
        
            Crud::executeNonResultQuery2($query, $params, $types, '../php/actionsManagement.php');
            $_SESSION['currentActivityEdition'] = $id_actividad;
            echo "<script>window.location.href = `../php/actionsManagement.php?Data-success=true`;</script>";            
        }        
    }

    if (isset($_GET['getReportInfo']) && $_GET['getReportInfo'] == 'true') {
        $crud = new Crud();
        $mysqli = $crud->getMysqliConnection();
        $activityId = filter_var($_GET['actId'], FILTER_VALIDATE_INT);
        $id_usuario = $_SESSION['id']; 
        $id_proyecto = $_SESSION['projectSelected'];
    
        if ($activityId !== false) {
            $query = "SELECT id_avance, nombre, contenido, DATE(fecha_creacion) AS fecha_creacion FROM tbl_avances WHERE id_usuario = ? AND id_proyecto = ? AND id_actividad = ?";
            $stmt = $mysqli->prepare($query);
    
            if ($stmt) {
                // Enlazamos el ID de la actividad, usuario y proyecto como parámetros
                $stmt->bind_param('iii', $id_usuario, $id_proyecto, $activityId);
                $stmt->execute();
                
                $result = $stmt->get_result();
                $reportData = [];
    
                setlocale(LC_TIME, 'es_ES.UTF-8');

                while ($row = $result->fetch_assoc()) {
                    $row['fecha_creacion'] = strftime('%e de %B de %Y', strtotime($row['fecha_creacion']));
                    $reportData[] = $row;
                }
    
                if (count($reportData) > 0) {
                    // Enviar todos los reportes al archivo JS
                    echo json_encode(['success' => true, 'data' => $reportData]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'No se encontraron reportes']);
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

    if (isset($_GET['getReportData']) && $_GET['getReportData'] == 'true') {
        $id_avance = filter_var($_GET['id_avance'], FILTER_VALIDATE_INT);

    if ($id_avance) {
        $crud = new Crud();
        $mysqli = $crud->getMysqliConnection();

        // Query para obtener los datos del reporte por id_avance
        $query = "SELECT nombre, contenido FROM tbl_avances WHERE id_avance = ?";
        $stmt = $mysqli->prepare($query);

        if ($stmt) {
            $stmt->bind_param('i', $id_avance);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $reportData = $result->fetch_assoc();

                // Decodificar el contenido JSON almacenado en la base de datos
                $contenido = json_decode($reportData['contenido'], true);

                echo json_encode(['success' => true, 'data' => [
                    'nombre' => $reportData['nombre'],
                    'contenido' => $contenido
                ]]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No se encontró el reporte']);
            }

            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Error en la consulta']);
        }

        $mysqli->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'ID de avance inválido']);
        }
    }    
    
} else {
     echo"<script>window.location.href = `../php/actionsManagement.php`;</script>";
}
?>