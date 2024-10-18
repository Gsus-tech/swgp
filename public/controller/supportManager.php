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
                        
                    } 

                    else if($type === 't-2'){
                        $query = "SELECT proyectos.id_proyecto FROM tbl_proyectos proyectos JOIN tbl_integrantes integrantes ON proyectos.id_proyecto = integrantes.id_proyecto WHERE integrantes.id_usuario = ? AND integrantes.responsable = 1 AND proyectos.estado = 1;";
                        $params = [$id_usuario];
                        $types = "i";
                        $userProjects = Crud::executeResultQuery($query, $params, $types);
                        $proyectoId = filter_var($_POST['projectSelect'], FILTER_VALIDATE_INT);
                        if($proyectoId !== false && is_array($userProjects)){   
                            $proyectoIds = array_column($userProjects, 'id_proyecto');
                            if (in_array($proyectoId, $proyectoIds)) {     
                                $correctionType = Crud::antiNaughty((string)$_POST['correctionType']);
                                $mensaje = null;
                                if($correctionType === 'addMember'){
                                    $nombre = Crud::antiNaughty((string)$_POST['name']);
                                    $correo = Crud::antiNaughty((string)$_POST['email']);
                                    $depto = Crud::antiNaughty((string)$_POST['department']);
                                    
                                    $mensaje = json_encode([
                                        'Cambio' => "addMember",
                                        'nombre' => "$nombre",
                                        'correo' => "$correo",
                                        'depto' => "$depto"
                                    ]);
                                }
                                if($correctionType === 'removeMember'){
                                    // $mensaje = "Eliminar integrante";
                                }
                                if($correctionType === 'changePermitions'){
                                    // $mensaje = "Cambio de permisos de usuario";
                                }
                                if($correctionType === 'projectDataCorrection'){
                                    $ticketTitle = Crud::antiNaughty((string)$_POST['ticketTitle']);
                                    $ticketDescription = Crud::antiNaughty((string)$_POST['ticketDescription']);
                                    
                                    $mensaje = json_encode([
                                        'Cambio' => "projectDataCorrection",
                                        'ticketTitle' => "$ticketTitle",
                                        'ticketDescription' => "$ticketDescription"
                                    ]);
                                }

                                if($mensaje != null){
                                    // Prepara y ejecuta la consulta
                                    $query = "INSERT INTO tbl_solicitudes_soporte (id_usuario, tipoSolicitud, mensaje, estado) VALUES (?, 2, ?, 'Abierto')";
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

                                    echo json_encode(['success' => true, 'message' => 'Ticket de Corrección o cambios en un proyecto levantado.'.$mensaje]);
                                    $stmt->close();
                                }else{
                                    echo json_encode(['success' => false, 'message' => 'Tipo de corrección o cambio a realizar inválido.']);
                                }
                            }else{
                                echo json_encode(['success' => false, 'message' => 'Id de proyecto alterado.']);
                            }
                        }else{
                            echo json_encode(['success' => false, 'message' => 'Id de proyecto inválido.']);
                        }
                    }
                    
                    else if($type === 't-3'){
                        $onField = Crud::antiNaughty((string)$_POST['correctionType']);
                        $newValue = Crud::antiNaughty((string)$_POST['newValue']);
                        $field = $onField === 'deptoUpdate' ? 'Departamento' : 'Correo';

                        $mensaje = json_encode([
                            'Campo' => "{$field}",
                            'newValue' => "{$newValue}"
                        ]);

                        // Prepara y ejecuta la consulta
                        $query = "INSERT INTO tbl_solicitudes_soporte (id_usuario, tipoSolicitud, mensaje, estado) VALUES (?, 3, ?, 'Abierto')";
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
                    }
                    
                    else {
                        echo json_encode(['success' => false, 'message' => 'Tipo de ticket no reconocido.']);
                    }
                }
            }

            if (isset($_GET['getProjectList']) && $_GET['getProjectList'] === 'true'){
                $crud = new Crud();
                $mysqli = $crud->getMysqliConnection();
                $id_usuario = $_SESSION['id'];
    
                $query = "SELECT proyectos.id_proyecto, proyectos.nombre FROM tbl_proyectos proyectos JOIN tbl_integrantes integrantes ON proyectos.id_proyecto = integrantes.id_proyecto WHERE integrantes.id_usuario = ? AND integrantes.responsable = 1 AND proyectos.estado = 1;";
    
                $stmt = $mysqli->prepare($query);
        
                if ($stmt) {
                    $stmt->bind_param('i', $id_usuario);
                    $stmt->execute();
                    
                    $result = $stmt->get_result();
        
                    if ($result->num_rows > 0) {
                        $projectsList = [];
                        while ($row = $result->fetch_assoc()) {
                            $projectsList[] = $row; // Lo convierto en array para poder usar map
                        }
                        // $_SESSION['projectSelected'] = $projectsList[0]['id_proyecto'];
                        echo json_encode(['success' => true, 'data' => $projectsList]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'No se encontraron proyectos a cargo del usuario actual']);
                    }
                $stmt->close();
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta']);
                }
                $mysqli->close();
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
