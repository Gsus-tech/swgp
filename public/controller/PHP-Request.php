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
    
    if(isset($_GET['checkForNotifications']) && $_GET['checkForNotifications'] === 'true'){
        if($_SESSION['rol']==='EST'){
            $userId = $_SESSION['id'];
            // Obtener proyectos asociados al usuario
            $queryProjects = "SELECT id_proyecto FROM tbl_integrantes WHERE id_usuario = ?";
            $userProjects = Crud::executeResultQuery($queryProjects, [$userId], 'i', $_SERVER['HTTP_REFERER']);
            $projectIds = array_column($userProjects, 'id_proyecto');
        
            if (empty($projectIds)) {
                // No hay proyectos asociados
                echo json_encode(['success' => false, 'message' => 'No se encontraron proyectos asociados']);
                exit();
            }
        
            // Filtrar registros en tbl_logs para los proyectos asociados
            $placeholders = implode(',', array_fill(0, count($projectIds), '?'));
            $queryLogs = "SELECT usuario, accion, proyecto, fecha, seleccionados, notifyUser, logfor, viewed FROM tbl_logs 
                WHERE (proyecto IN ($placeholders) AND logfor != 'sistema') OR notifyUser = ?";
            $params = array_merge($projectIds, [$userId]);
            $logs = Crud::executeResultQuery($queryLogs, $params, str_repeat('i', count($projectIds)) . 'i');
            
            if (count($logs) === 0) {
                // No se encontraron
                echo json_encode(['success' => false, 'none' => true, 'message' => 'Sin notificaciones']);
                exit();
            }

            // Procesar los registros para identificar notificaciones relevantes
            $notifications = [];
            foreach ($logs as $log) {
                // Notificaciones directas (notifyUser)
                if ($log['logfor'] === 'usuario' && (int)$log['notifyUser'] === (int)$userId) {
                    $notifications[] = [
                        'usuario' => $log['usuario'],
                        'accion' => $log['accion'],
                        'proyecto' => $log['proyecto'],
                        'fecha' => $log['fecha'],
                        'viewed' => $log['viewed']
                    ];
                    continue; // Pasar a la siguiente notificación
                }
            
                // Notificaciones específicas (seleccionados)
                if ($log['logfor'] === 'especificos' && $log['seleccionados'] !== null) {
                    $decodedData = htmlspecialchars_decode($log['seleccionados']);
                    $jsonArray = json_decode($decodedData, true);
            
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        error_log("Error al decodificar JSON en log ID {$log['id']}: " . json_last_error_msg());
                        continue; // Omitir registros con JSON inválido
                    }
            
                    foreach ($jsonArray as $entry) {
                        if (isset($entry['id']) && (int)$entry['id'] === (int)$userId) {
                            $notifications[] = [
                                'usuario' => $log['usuario'],
                                'accion' => $log['accion'],
                                'proyecto' => $log['proyecto'],
                                'fecha' => $log['fecha'],
                                'viewed' => $entry['viewed']
                            ];
                            break; // Salir del bucle, ya que encontramos al usuario actual
                        }
                    }
                }
            }
            
            
            echo json_encode(['success' => true, 'notificaciones' => $notifications, 'message' => "Notificaciones recuperadas."]);
        }
        else{
            echo json_encode(['success' => false, 'none' => true, 'message' => 'Trabajando en las notificaciones administrativas']);
        }
    }
}