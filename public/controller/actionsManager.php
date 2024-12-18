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

    if(isset($_GET['getActState']) && $_GET['getActState'] == 'true'){
        $crud = new Crud();
        $mysqli = $crud->getMysqliConnection();
        $activityId = filter_var($_GET['activityId'], FILTER_VALIDATE_INT);
        $id_usuario = $_SESSION['id'];

        if ($activityId !== false) {
            $query = "SELECT estadoActual, revision, revision_date FROM tbl_actividades WHERE id_actividad = ? AND id_usuario = ?";

            $stmt = $mysqli->prepare($query);
    
            if ($stmt) {
                // Solo enlazamos el ID de la actividad como parámetro
                $stmt->bind_param('ii', $activityId, $id_usuario);
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

            //Guardar imagenes en servidor
            $uploadDir = "../assets/report-images/project-" . $id_proyecto . "/";
            $imageNamesList = array();
            $imageCounter = 0;

            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            foreach ($_POST as $key => $base64Image) {
                if (strpos($key, 'imagen_') === 0) {  
                    if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                        $imageType = strtolower($type[1]); 
                        
                        // Validar el tipo de archivo
                        if (in_array($imageType, ['jpeg', 'jpg', 'png', 'webp'])) {
                            $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
                            $decodedImage = base64_decode($base64Image);
        
                            if ($decodedImage !== false) {
                                $imageName = 'act' . $id_actividad . '-' . uniqid() . '.' . $imageType;
                                
                                // Guardar la imagen en la carpeta especificada
                                $imagePath = $uploadDir . $imageName;
                                file_put_contents($imagePath, $decodedImage);

                                $imageNamesList[$imageCounter] = $imagePath;
                                $imageCounter++;
                            } else {
                                echo "Error: Error al decodificar la imagen.";
                            }
                        } else {
                            echo "Error: Tipo de archivo no permitido: $imageType";
                        }
                    } else {
                        echo "Error: Formato de imagen no válido.";
                    }
                }
            }
            $setImage = 0;
            $decodedJSON = json_decode($contenidoJSON, true);
            if (is_array($decodedJSON)) {
               foreach ($decodedJSON as &$item) {
                    if (isset($item['type']) && $item['type'] === 'img') {
                        $item['value'] = $imageNamesList[$setImage];
                        $setImage++;
                    }
                }
                $contenidoJSON = json_encode($decodedJSON);
            } else {
                echo "Error: No se pudo decodificar el contenido JSON.";
            }
            
        
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
    
    if (isset($_GET['saveNewReport2']) && $_GET['saveNewReport2'] == 'true') {
        $id_usuario = $_SESSION['id'];
        $projectId = $_SESSION['projectSelected'];
        $id_actividad = filter_var($_POST['id_actividad'], FILTER_VALIDATE_INT);
        $reportName = $_POST['reportName'];
        $reportHTML = $_POST['reportHTML'];
        $imageNamesList = [];
        $uploadDir = "../assets/report-images/project-" . $projectId . "/";
        
        // Crear la carpeta si no existe
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
    
        preg_match_all('/<img([^>]+)src=["\']data:image\/(jpeg|jpg|png|webp);base64,([^"\']+)["\']([^>]*)>/i', $reportHTML, $matches, PREG_SET_ORDER);
        
    foreach ($matches as $match) {
        $imageType = strtolower($match[2]);
        $base64Image = $match[3]; 
        $attributesBeforeSrc = trim($match[1]); // Atributos antes del src
        $attributesAfterSrc = trim($match[4]);  // Atributos después del src

        // Validar el tipo de archivo
        if (in_array($imageType, ['jpeg', 'jpg', 'png', 'webp'])) {
            $decodedImage = base64_decode($base64Image);
        
            if ($decodedImage !== false) {
                // Generar un nombre único para la imagen
                $imageName = 'act' . $id_actividad . '-' . uniqid() . '.' . $imageType;
                $imagePath = $uploadDir . $imageName;

                // Guardar la imagen en el servidor
                file_put_contents($imagePath, $decodedImage);

                // Crear el nuevo tag de imagen con la ruta del servidor
                $newImageTag = '<img ' . $attributesBeforeSrc . ' src="' . $imagePath . '"' . $attributesAfterSrc . '>';
                
                // Reemplazar la imagen base64 en el HTML con el nuevo tag
                $pattern = '/<img([^>]+)src=["\']data:image\/(jpeg|jpg|png|webp);base64,([^"\']+)["\']([^>]*)>/i';
                $replacement = $newImageTag;
                $reportHTML = preg_replace($pattern, $replacement, $reportHTML, 1);
            } else {
                echo json_encode(['success' => false, 'message' => "Error al decodificar la imagen."]);
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'message' => "Tipo de archivo no permitido: $imageType"]);
            exit;
        }
    }
    
        // Conexión a la base de datos
        $crud = new Crud();
        $mysqli = $crud->getMysqliConnection();
        // Insertar el reporte en la base de datos
        $query = "INSERT INTO tbl_avances (contenido, nombre, id_usuario, id_proyecto, id_actividad) VALUES (?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($query);
        if (!$stmt) {
            echo "<script>window.location.href = `../php/actionsManagement.php?Data-success=false`;</script>";
        }
    
        $stmt->bind_param("ssiii", $reportHTML, $reportName, $id_usuario, $projectId, $id_actividad);
        $flag = false;
        if ($stmt->execute()) {
            echo "<script>localStorage.setItem('openActDetails', 'true');localStorage.setItem('actId', '$id_actividad');window.location.href = `../php/actionsManagement.php?Data-success=true`;</script>";               
        } else {
            echo "<script>localStorage.setItem('openActDetails', 'true');localStorage.setItem('actId', '$id_actividad');window.location.href = `../php/actionsManagement.php?Data-success=false`;</script>";            
        }
    
        // Cerrar la conexión y liberar recursos
        $stmt->close();
        $mysqli->close();
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
                    // $contenido = json_decode($reportData['contenido'], true);
                    $contenido = $reportData['contenido'];

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

    if (isset($_GET['deleteReport']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $crud = new Crud();
        $mysqli = $crud->getMysqliConnection();
        
        $avId = isset($_POST['avId']) ? (int)$_POST['avId'] : null;
    
        if ($avId !== null) {
            // Obtener el contenido del reporte
            $query = "SELECT contenido FROM tbl_avances WHERE id_avance = ?";
            $stmt = $mysqli->prepare($query);
    
            if ($stmt) {
                $stmt->bind_param('i', $avId);
                $stmt->execute();
                $result = $stmt->get_result();
    
                if ($result->num_rows > 0) {
                    $reportData = $result->fetch_assoc();
                    $contenidoHTML = $reportData['contenido']; // Contenido del reporte en HTML
    
                    // Buscar todas las imágenes en el HTML del contenido
                    preg_match_all('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $contenidoHTML, $matches);
    
                    // Eliminar cada imagen encontrada en el servidor
                    foreach ($matches[1] as $rutaImagen) {
                        $rutaImagen = str_replace('..', '', $rutaImagen); // Asegurarse de no permitir caminos fuera del directorio raíz
                        $imagePath = $_SERVER['DOCUMENT_ROOT'] . $rutaImagen;
    
                        if (file_exists($imagePath)) {
                            unlink($imagePath);
                        }
                    }
    
                    // Eliminar el reporte después de eliminar las imágenes
                    $deleteQuery = "DELETE FROM tbl_avances WHERE id_avance = ?";
                    $stmtDelete = $mysqli->prepare($deleteQuery);
                    if ($stmtDelete) {
                        $stmtDelete->bind_param('i', $avId);
    
                        if ($stmtDelete->execute()) {
                            if ($stmtDelete->affected_rows > 0) {
                                echo json_encode([
                                    'success' => true,
                                    'message' => "Reporte con ID $avId y sus imágenes asociadas han sido eliminados correctamente."
                                ]);
                            } else {
                                echo json_encode([
                                    'success' => false,
                                    'message' => "No se encontró el reporte con ID $avId o no se eliminaron registros."
                                ]);
                            }
                        } else {
                            echo json_encode(['success' => false, 'message' => 'Error al eliminar el reporte: ' . $stmtDelete->error]);
                        }
    
                        $stmtDelete->close();
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'No se encontró el reporte']);
                }
    
                $stmt->close();
            } else {
                echo json_encode(['success' => false, 'message' => 'Error en la consulta para obtener los datos del reporte']);
            }
    
            $mysqli->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Faltan datos necesarios para eliminar el reporte']);
        }
    
        exit();
    }
    

    if (isset($_GET['submitActivityRevision']) && $_GET['submitActivityRevision'] === 'true') {
        $id_actividad = filter_var($_POST['actId'], FILTER_VALIDATE_INT);
        
        if ($id_actividad) {
            
            $crud = new Crud();
            $mysqli = $crud->getMysqliConnection();
            
            // Query para actualizar el campo de revisión
            $query = "UPDATE tbl_actividades SET revision = ?, revision_date = NOW() WHERE id_actividad = ? AND id_usuario = ? AND id_proyecto = ?";
            $stmt = $mysqli->prepare($query);
            
            if ($stmt) {
                $revision = 1; // Poner el valor de revisión a 1
                $id_usuario = $_SESSION['id'];
                $id_proyecto = $_SESSION['projectSelected'];
    
                // Vinculacion de parámetros a la consulta preparada
                $stmt->bind_param('iiii', $revision, $id_actividad, $id_usuario, $id_proyecto);
                $stmt->execute();
    
                // Verificar si se actualizó alguna fila
                if ($stmt->affected_rows > 0) {
                    echo json_encode(['success' => true, 'message' => 'La actividad ha sido marcada como revisada.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'No se pudo actualizar la actividad o no se encontró.']);
                }
    
                $stmt->close();
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta SQL.']);
            }
    
            $mysqli->close();
           
        } else {
            echo json_encode(['success' => false, 'message' => 'ID de actividad no válido.']);
        }
    }
    
    function saveImage($base64Image, $uploadDir, $id_actividad) {
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
            $imageType = strtolower($type[1]);
    
            if (!in_array($imageType, ['jpeg', 'jpg', 'png', 'webp'])) {
                return ["success" => false, "message" => "Tipo de archivo no permitido: $imageType"];
            }
    
            $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
            $decodedImage = base64_decode($base64Image);
    
            if ($decodedImage === false) {
                return ["success" => false, "message" => "Error al decodificar la imagen."];
            }
    
            $imageName = 'act' . $id_actividad . '-' . uniqid() . '.' . $imageType;
            $imagePath = $uploadDir . $imageName;
    
            if (file_put_contents($imagePath, $decodedImage)) {
                return ["success" => true, "path" => $imagePath];
            } else {
                return ["success" => false, "message" => "No se pudo guardar la imagen en el servidor."];
            }
        } else {
            return ["success" => false, "message" => "Formato de imagen no válido."];
        }
    }

    function responseJson($success, $message) {
        echo json_encode(["success" => $success, "message" => $message]);
        exit;
    }
    
} else {
     echo"<script>window.location.href = `../php/actionsManagement.php`;</script>";
}
?>
