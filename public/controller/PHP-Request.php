<?php
session_start();
require_once '../controller/generalCRUD.php';
use Controller\GeneralCrud\Crud;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_GET['change-selectedProject'])){
        $selectedProject = $_POST['selectedProject'] ?? null;

        if ($selectedProject) {
            $_SESSION['projectSelected'] = $selectedProject;

            // Respuesta en formato JSON
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    if (isset($_GET['validateSession']) && $_GET['validateSession'] === 'true') {
        date_default_timezone_set('America/Mexico_City');
        if (!isset($_SESSION['id']) || !isset($_SESSION['sessionId'])) {
            echo json_encode(['success' => false, 'message' => 'no se encontró una sesión abierta']);
            exit();
        }
    
        $userId = $_SESSION['id'];
        $sessionId = $_SESSION['sessionId'];
    
        $query = "SELECT id_sesion, last_activity FROM tbl_usuarios WHERE id_usuario = ?";
        $lastSessionId = Crud::executeResultQuery($query, [$userId], 'i');
    
        if (count($lastSessionId) === 0) {
            echo json_encode(['success' => false, 'message' => 'No se encontró la cuenta de usuario.']);
            exit();
        }
    
        $isValidSessionId = ($lastSessionId[0]['id_sesion'] === $sessionId);
        $lastActivity = $lastSessionId[0]['last_activity'];

        $lastActivityTimestamp = strtotime($lastActivity);
        $currentTimestamp = time();
        $timeDifference = ($currentTimestamp - $lastActivityTimestamp) / 60;
    
        $isValidLastActivity = ($timeDifference <= 30);

        if ($isValidSessionId && $isValidLastActivity) {
            $sessionQuery = "UPDATE tbl_usuarios SET last_activity = NOW() WHERE id_usuario = ?";
            Crud::executeNonResultQuery2($sessionQuery, [$_SESSION['id']], 'i','../php/dashboard.php');
            echo json_encode(['success' => true, 'value' => true]);
        } else {
            echo json_encode([
                'success' => true,'value' => false,
                'message' => $isValidSessionId ? 'Sesión expirada por inactividad.' : 'ID de sesión no válido.'
            ]);
        }
    }
    
}