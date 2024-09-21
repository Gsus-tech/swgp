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
    
    
} else {
     echo"<script>window.location.href = `../php/actionsManagement.php`;</script>";
}
?>
