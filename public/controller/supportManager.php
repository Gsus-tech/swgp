<?php
session_start();
require_once '../controller/generalCRUD.php';
use Controller\GeneralCrud\Crud;

$destination = "../php/support.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar el rol del usuario
    if (isset($_SESSION['rol'])) {
        if ($_SESSION['rol'] === 'ADM' || $_SESSION['rol'] === 'SAD') {
            
            echo json_encode(['success' => true, 'message' => 'AJAX REQUEST realizado correctamente.']);
        } else if ($_SESSION['rol'] === 'EST') {
            $crud = new Crud();
            $mysqli = $crud->getMysqliConnection();
            $id_usuario = $_SESSION['id'];
        
            if (isset($_GET['newTicket']) && $_GET['newTicket'] === 'true') {
                $type = $_POST['ticketType'];
                if ($type) {
                    $submit = false;
                    $query = "";
            
                    if ($type === 't-1') {
                        $ticketTitle = Crud::antiNaughty((string)$_POST['ticketTitle']);
                        $ticketDescription = Crud::antiNaughty((string)$_POST['ticketDescription']);
            
                        $mensaje = json_encode([
                            'titulo' => "{$ticketTitle}",
                            'descripcion' => "{$ticketDescription}"
                        ]);
            
                        $uploadDir = "../assets/suppTicketsImages/";
            
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }
            
                        if (isset($_POST['ticketImageBase64'])) {
                            $imgData = $_POST['ticketImageBase64'];
            
                            if (preg_match('/^data:image\/(\w+);base64,/', $imgData, $type)) {
                                $imgData = substr($imgData, strpos($imgData, ',') + 1);
                                $type = strtolower($type[1]);
            
                                if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                                    echo json_encode(['success' => false, 'message' => 'Formato de imagen no soportado.']);
                                    exit;
                                }
            
                                $imgData = base64_decode($imgData);
                                if ($imgData === false) {
                                    echo json_encode(['success' => false, 'message' => 'Error al decodificar la imagen.']);
                                    exit;
                                }
            
                                $uniqueFileName = uniqid('ticket_', true) . '.' . $type;
                                $filePath = $uploadDir . $uniqueFileName;
            
                                if (file_put_contents($filePath, $imgData) === false) {
                                    echo json_encode(['success' => false, 'message' => 'Error al guardar la imagen.']);
                                    exit;
                                }
            
                                $mensaje = json_encode([
                                    'titulo' => "{$ticketTitle}",
                                    'descripcion' => "{$ticketDescription}",
                                    'imagen' => '../assets/suppTicketsImages/' . $uniqueFileName
                                ]);
                            } else {
                                echo json_encode(['success' => false, 'message' => 'Formato de imagen inválido.']);
                                exit;
                            }
                        }
            
                        // Prepara y ejecuta la consulta
                        $query = "INSERT INTO tbl_solicitudes_soporte (id_usuario, tipoSolicitud, mensaje, estado) VALUES (?, 1, ?, 'Abierto')";
                        $stmt = $mysqli->prepare($query);
            
                        if (!$stmt) {
                            echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta: ' . $mysqli->error]);
                            exit;
                        }
            
                        $stmt->bind_param('is', $id_usuario, $mensaje);
            
                        if (!$stmt->execute()) {
                            echo json_encode(['success' => false, 'message' => 'Error en la ejecución: ' . $stmt->error]);
                            exit;
                        }
            
                        echo json_encode(['success' => true, 'message' => 'Ticket enviado exitosamente.']);
                        $stmt->close();
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Tipo de ticket no reconocido.']);
                    }
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'NewTicket es false.']);
            }
            
        }
        
         else {
            echo json_encode(['success' => false, 'message' => 'No tienes permisos para enviar este ticket.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Rol de usuario no detectado.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método de solicitud no permitido.']);
}
?>
