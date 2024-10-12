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
        
            if(isset($_GET['newTicket']) && $_GET['newTicket']==='true'){
                $type = $_POST['ticketType'];
                if($type){
                    $submit = false;
                    $query = "";
                    
                    if($type === 't-1'){
                        $ticketTitle = Crud::antiNaughty((string)$_POST['ticketTitle']);
                        $ticketDescription = Crud::antiNaughty((string)$_POST['ticketDescription']);
        
                        $mensaje = json_encode([
                            'titulo' => "<h2>{$ticketTitle}</h2>",
                            'descripcion' => "<p>{$ticketDescription}</p>"
                        ]);
                        
                        // Nos falta agregar las imagenes.
                        // Estaba pensando en convertirlas a base64 desde js y mandarlas asi por medio de data.
                        // Al igual que en los reportes, vamos a guardar la ruta en la bd y guardamos la imagen en un folder en la carpeta raiz.

                        $query = "INSERT INTO tbl_solicitudes_soporte (id_usuario, tipoSolicitud, mensaje, estado) VALUES (?, ?, ?, 'Abierto')";
                        $stmt = $mysqli->prepare($query);
                        if ($stmt) {
                            $stmt->bind_param('iis', $id_usuario, $type, $mensaje);
                            
                            if($stmt->execute()){
                                echo json_encode(['success' => true, 'message' => 'Ticket enviado exitosamente.']);
                            } else {
                                echo json_encode(['success' => false, 'message' => 'Error al enviar el ticket.']);
                            }
                            
                            $stmt->close(); 
                        }
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Tipo de ticket no reconocido.']);
                    }
                }
            }else{
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
