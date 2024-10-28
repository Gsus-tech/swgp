<?php
session_start();
require_once '../controller/generalCRUD.php';
use Controller\GeneralCrud\Crud;

$destination = "../php/projectsManagement.php";
if (($_SESSION['rol']==='ADM' || $_SESSION['rol']==='SAD') && $_SERVER["REQUEST_METHOD"] == "POST") {
    function deleteDirectory($dir) {
        if (!is_dir($dir)) {
            return;
        }
    
        // Obtener todos los archivos y subdirectorios
        $files = array_diff(scandir($dir), ['.', '..']);
    
        foreach ($files as $file) {
            $filePath = "$dir/$file";
            
            // Si es una carpeta, llamamos a la función recursivamente
            if (is_dir($filePath)) {
                deleteDirectory($filePath);
            } else {
                // Si es un archivo, lo eliminamos
                unlink($filePath);
            }
        }
    
        // Finalmente, eliminar la carpeta vacía
        rmdir($dir);
    }
    
    if (isset($_GET['editProject']) && $_GET['editProject'] == 'true' && isset($_GET['id'])) {
        $id =  (int)$_GET['id'];
        $destination = $destination ."?editProject=". $id;
            
        //Actualizar tbl_proyectos
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
        $dp = Crud::antiNaughty((string)$_POST['dropDownDepto']);;
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
            $er = json_encode("Fechas inválidas.");
            header("Location: $destination?error=$er");
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
    }


    if (isset($_GET['addMember'])) {
        $crud = new Crud();
        $mysqli = $crud->getMysqliConnection();
        
        $projectId = filter_var($_POST['projectId'], FILTER_VALIDATE_INT);
        $usuarioId = filter_var($_POST['usuarioId'], FILTER_VALIDATE_INT);
        $rol = filter_var($_POST['tipoMiembro'], FILTER_VALIDATE_INT);

        if ($projectId !== false && $usuarioId !== false && $rol !== false) {
            // Preparar la consulta SQL
            $sql = "INSERT INTO tbl_integrantes (id_usuario, id_proyecto, responsable) VALUES (?, ?, ?)";
            $stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param('iii', $usuarioId, $projectId, $rol);

                // Ejecutar la consulta
                if ($stmt->execute()) {
                    if ($stmt->affected_rows > 0) {
                        echo json_encode([
                            'success' => true,
                            'message' => 'Miembro agregado correctamente.'
                        ]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'No se pudo agregar el miembro.']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al agregar el miembro: ' . $stmt->error]);
                }
                $stmt->close();
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta: ' . $mysqli->error]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Los datos proporcionados no son válidos.']);
        }

        // Cerrar la conexión
        $mysqli->close();
        exit();
    }

    if (isset($_GET['deleteMember'])) {
        ob_clean();

        $idUsuario = isset($_POST['idUsuario']) ? (int)$_POST['idUsuario'] : null;
        $projectId = isset($_POST['projectId']) ? (int)$_POST['projectId'] : null;

        if ($idUsuario !== null && $projectId !== null) {
            $crud = new Crud();
            $mysqli = $crud->getMysqliConnection();
            
            // Primero obtenemos las actividades que tienen este usuario asignado
            $sql = "SELECT id_actividad FROM tbl_actividades WHERE id_proyecto = ? AND id_usuario = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param('ii', $projectId, $idUsuario);
            $stmt->execute();
            $result = $stmt->get_result();
            $actividades = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            // Obtener el responsable del proyecto
            $sql = "SELECT id_usuario FROM tbl_integrantes WHERE id_proyecto = ? AND responsable = 1";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param('i', $projectId);
            $stmt->execute();
            $result = $stmt->get_result();
            $responsableProyecto = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            if (!$responsableProyecto || (count($responsableProyecto)===1 && $responsableProyecto[0]['id_usuario']===$idUsuario)) {
                echo json_encode([
                    'success' => false,
                    'message' => "No se encontró un responsable del proyecto para transferir actividades."
                ]);
                $mysqli->close();
                exit();
            }

            $nuevoResponsableId = ($responsableProyecto[0]['id_usuario'] == $idUsuario) 
                ? $responsableProyecto[1]['id_usuario'] 
                : $responsableProyecto[0]['id_usuario'];

            // Transferir las actividades a un nuevo responsable si las hay
            if (count($actividades) > 0) {
                $sql = "UPDATE tbl_actividades SET id_usuario = ? WHERE id_proyecto = ? AND id_usuario = ? AND id_actividad = ?";
                $stmt = $mysqli->prepare($sql);
                foreach ($actividades as $actividad) {
                    $actividadId = $actividad['id_actividad'];
                    $stmt->bind_param('iiii', $nuevoResponsableId, $projectId, $idUsuario, $actividadId);
                    $stmt->execute();
                }
                $stmt->close();
            }

            // Finalmente, eliminar el integrante del proyecto
            $sql = "DELETE FROM tbl_integrantes WHERE id_usuario = ? AND id_proyecto = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param('ii', $idUsuario, $projectId);

            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    echo json_encode([
                        'success' => true,
                        'message' => "Miembro con ID $idUsuario eliminado del proyecto con ID $projectId.",
                        'data' => [
                            'usuarioId' => $idUsuario,
                            'projectId' => $projectId
                        ]
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => "No se encontró el miembro en el proyecto o no se realizaron cambios."
                    ]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al ejecutar la eliminación: ' . $stmt->error]);
            }

            $stmt->close();
            $mysqli->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Faltan datos necesarios para eliminar el miembro.']);
        }

        exit();
    }

    if (isset($_GET['addObjectiveGeneral']) || isset($_GET['addObjectiveEspecifico'])) {
        $crud = new Crud();
        $mysqli = $crud->getMysqliConnection();
        $idProyecto = filter_var($_POST['idProyecto'], FILTER_VALIDATE_INT); 
        $type = $_POST['tipo']; 
        $contenido = $_POST['contenido'];
        $tipo = '';
        if(isset($_GET['addObjectiveGeneral']) && $type === 'general'){ $tipo = 'general'; }
        if(isset($_GET['addObjectiveEspecifico']) && $type === 'especifico'){ $tipo = 'especifico'; }
        
        if ($idProyecto !== false && !empty($contenido) && !empty($tipo)) {
            // Preparar la consulta SQL para insertar un nuevo objetivo
            $sql = "INSERT INTO tbl_objetivos (id_proyecto, tipo, contenido) VALUES (?, ?, ?)";
            $stmt = $mysqli->prepare($sql);
        
            if ($stmt) {
                // Enlazar los parámetros de la consulta (i - entero, s - string)
                $stmt->bind_param('iss', $idProyecto, $tipo, $contenido);
        
                // Ejecutar la consulta
                if ($stmt->execute()) {
                    if ($stmt->affected_rows > 0) {
                        // Obtener el ID del nuevo objetivo insertado
                        $newId = $mysqli->insert_id;
        
                        // Respuesta JSON de éxito
                        echo json_encode([
                            'success' => true,
                            'message' => 'Objetivo ingresado correctamente.',
                            'data' => [
                                'newId' => $newId,  // Enviar el ID del nuevo objetivo
                                'nombre' => 'Objetivo guardado',
                                'descripcion' => $contenido  // La descripción que se guardó
                            ]
                        ]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'No se pudo guardar el objetivo.']);
                    }
                } else {
                    // Error al ejecutar la consulta
                    echo json_encode(['success' => false, 'message' => 'Error al guardar el objetivo: ' . $stmt->error]);
                }
        
                // Cerrar la consulta
                $stmt->close();
            } else {
                // Error al preparar la consulta
                echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta: ' . $mysqli->error]);
            }
        } else {
            // Datos inválidos o faltantes
            echo json_encode(['success' => false, 'message' => 'Los datos proporcionados no son válidos.']);
        }
        
        // Cerrar la conexión
        $mysqli->close();
        exit();  
    } 

    if (isset($_GET['deleteObjective'])) {
        $crud = new Crud();
        $mysqli = $crud->getMysqliConnection();
        $projectId = filter_var($_POST['projectId'], FILTER_VALIDATE_INT);
        $objId = filter_var($_POST['objId'], FILTER_VALIDATE_INT);
        $type = $_POST['type'];
    
        if ($projectId !== false && $objId !== false && !empty($type)) {
            $sql = "DELETE FROM tbl_objetivos WHERE id_proyecto = ? AND id_objetivo = ? AND tipo = ?";
            $stmt = $mysqli->prepare($sql);
    
            if ($stmt) {
                $stmt->bind_param('iis', $projectId, $objId, $type);
    
                if ($stmt->execute()) {
                    if ($stmt->affected_rows > 0) {
                        echo json_encode([
                            'success' => true,
                            'message' => 'Objetivo eliminado correctamente.',
                            'data' => ['objId' => $objId]
                        ]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'No se encontró el objetivo o no se eliminaron registros.']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al eliminar el objetivo: ' . $stmt->error]);
                }

                $stmt->close();
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta: ' . $mysqli->error]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Los datos proporcionados no son válidos.']);
        }
    
        // Cerrar la conexión
        $mysqli->close();
        exit();
    }

    if (isset($_GET['editObjetctive'])) {
        $crud = new Crud();
        $mysqli = $crud->getMysqliConnection();
        
        $projectId = filter_var($_POST['idProyecto'], FILTER_VALIDATE_INT);
        $objectiveId = filter_var($_POST['idObjetivo'], FILTER_VALIDATE_INT);
        $newDescription = $_POST['nuevaDescripcion'];
        $type = $_POST['tipo'];

        if ($projectId !== false && $objectiveId !== false && !empty($newDescription) && !empty($type)) {
            $sql = "UPDATE tbl_objetivos SET contenido = ? WHERE id_proyecto = ? AND id_objetivo = ? AND tipo = ?";
            $stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param('siis', $newDescription, $projectId, $objectiveId, $type);

                if ($stmt->execute()) {
                    if ($stmt->affected_rows > 0) {
                        echo json_encode([
                            'success' => true,
                            'message' => 'Objetivo actualizado correctamente.'
                        ]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'No se encontró el objetivo o no se realizaron cambios.']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al actualizar el objetivo: ' . $stmt->error]);
                }

                $stmt->close();
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta: ' . $mysqli->error]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Los datos proporcionados no son válidos.']);
        }
        $mysqli->close();
        exit();
    }
    
    if (isset($_GET['cierreProyecto'])) {
        $crud = new Crud();
        $mysqli = $crud->getMysqliConnection();
        $proyectoId = filter_var($_GET['cierreProyecto'], FILTER_VALIDATE_INT);
    
        if ($proyectoId !== false) {
            $sql = "UPDATE tbl_proyectos SET estado = ? WHERE id_proyecto = ?";
            if(isset($_GET['success']) && $_GET['success'] === 'true'){
                $estado = 2;
            }else{
                $estado = 0;    
            }
            $stmt = $mysqli->prepare($sql);
    
            if ($stmt) {
                $stmt->bind_param('ii', $estado, $proyectoId);
    
                // Ejecutar la consulta
                if ($stmt->execute()) {
                    if ($stmt->affected_rows > 0) {
                        echo json_encode(['success' => true, 'message' => 'Proyecto finalizado.']);
                        $_SESSION['projectSelected']=0;
                    } else {
                        echo json_encode(['success' => false, 'message' => 'No se encontró el proyecto o no se realizaron cambios.']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al finalizar el proyecto: ' . $stmt->error]);
                }
                $stmt->close();
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta: ' . $mysqli->error]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Los datos proporcionados no son válidos.']);
        }
    }
    
    if (isset($_GET['reactivate'])) {
        $crud = new Crud();
        $mysqli = $crud->getMysqliConnection();
        $proyectoId = filter_var($_GET['reactivate'], FILTER_VALIDATE_INT);
    
        if ($proyectoId !== false) {
            $sql = "UPDATE tbl_proyectos SET estado = ? WHERE id_proyecto = ?";
            $estado = 1;
            $stmt = $mysqli->prepare($sql);
    
            if ($stmt) {
                $stmt->bind_param('ii', $estado, $proyectoId);
    
                // Ejecutar la consulta
                if ($stmt->execute()) {
                    if ($stmt->affected_rows > 0) {
                        echo json_encode(['success' => true, 'message' => 'Proyecto reactivado.']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'No se encontró el proyecto o no se realizaron cambios.']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al reactivar el proyecto: ' . $stmt->error]);
                }
                $stmt->close();
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta: ' . $mysqli->error]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Los datos proporcionados no son válidos.']);
        }
    }

    if (isset($_GET['deleteProjectPermanently'])) {
        $crud = new Crud();
        $mysqli = $crud->getMysqliConnection();
        $proyectoId = filter_var($_GET['deleteProjectPermanently'], FILTER_VALIDATE_INT);
        
        if ($proyectoId !== false) {
            // Primero eliminamos el proyecto de la base de datos
            $sql = "DELETE FROM tbl_proyectos WHERE id_proyecto = ?";
            $stmt = $mysqli->prepare($sql);
        
            if ($stmt) {
                $stmt->bind_param('i', $proyectoId);

                // Ejecutar la consulta para eliminar el proyecto
                if ($stmt->execute()) {
                    if ($stmt->affected_rows > 0) {
                        // Si el proyecto se eliminó correctamente, procedemos a eliminar la carpeta de archivos
                        $projectFolder = "../assets/report-images/project-" . $proyectoId;
                        
                        if (is_dir($projectFolder)) {
                            // Llamar a la función recursiva para eliminar la carpeta y su contenido
                            deleteDirectory($projectFolder);
                            echo json_encode(['success' => true, 'message' => 'Proyecto eliminado permanentemente de la BD y la carpeta de archivos ha sido eliminada.']);
                        } else {
                            echo json_encode(['success' => true, 'message' => 'Proyecto eliminado permanentemente, pero no se encontró la carpeta de archivos asociada.']);
                        }
                    } else {
                        echo json_encode(['success' => false, 'message' => 'No se encontró el proyecto o no se realizaron cambios.']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al eliminar el proyecto de la BD: ' . $stmt->error]);
                }
                $stmt->close();
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta: ' . $mysqli->error]);
            }

            $mysqli->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Los datos proporcionados no son válidos.']);
        }
    }

    if (isset($_GET['getFullReportData']) && $_GET['getFullReportData'] === 'true') {
        $crud = new Crud();
        $mysqli = $crud->getMysqliConnection();
    
        $projectSelected = $_SESSION['projectSelected'];
    
        if (filter_var($projectSelected, FILTER_VALIDATE_INT) !== false) {
            $query = "SELECT tbl_actividades.nombre_actividad, tbl_avances.id_actividad, tbl_avances.nombre as reporte_nombre, tbl_avances.contenido 
                      FROM tbl_avances INNER JOIN tbl_actividades ON tbl_actividades.id_actividad = tbl_avances.id_actividad 
                      WHERE tbl_avances.id_proyecto = ? ORDER BY tbl_avances.id_actividad ASC";
            $stmt = $mysqli->prepare($query);
    
            if ($stmt) {
                $stmt->bind_param('i', $projectSelected);
                $stmt->execute();
                
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $activityName = $row['nombre_actividad'];
                        $reportContent = [
                            'reporte_nombre' => $row['reporte_nombre'],
                            'contenido' => $row['contenido']
                        ];
                        
                        if (!isset($structuredReports[$activityName])) {
                            $structuredReports[$activityName] = [];
                        }
                        // Add the report under the correct activity
                        $structuredReports[$activityName][] = $reportContent;
                    }
                    echo json_encode(['success' => true, 'data' => $structuredReports]);
                } else {
                    // En caso que no hayan objetivos registrados...
                    echo json_encode(['success' => false, 'message' => 'No se encontraron reportes asociados']);
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

    else {
        echo json_encode(['success' => false, 'message' => 'Método de solicitud no permitido.']);
    }
    
}else{
    header("Location: $destination");
}


