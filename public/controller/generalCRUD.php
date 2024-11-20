<?php

namespace Controller\GeneralCrud;

class Crud
{
    private $mysqli;

    public function __construct() {
        // Aquí estableces la conexión a la base de datos
        include "db_connection.php";
        $this->mysqli = new \mysqli($sname, $userN, $pass, $db_name);

        if ($this->mysqli->connect_error) {
            die('Error de Conexión (' . $this->mysqli->connect_errno . ') ' . $this->mysqli->connect_error);
        }
    }

    public static function selectData($fields, $table, $id, $order)
    {
        include "db_connection.php";
        $myQuery = "SELECT $fields FROM $table ORDER BY $id $order;";
        $data=array();
        try {
            if ($stmt = $conn->prepare($myQuery)) {
                $stmt->execute();
                $result = $stmt->get_result();
                $data = $result->fetch_all(MYSQLI_ASSOC);
                $stmt->close();
            }
        } catch (mysqli_sql_exception $e) {
            echo "Error al obtener los datos: " . $e->getMessage();
        } finally {
            $conn->close();
        }
        return $data;
    }

    public static function deleteRow($table, $condition, $id, $route)
    {
        include "db_connection.php";
        $queryDel = "DELETE FROM $table WHERE $condition='$id';";
        try {
            $deleteRow =  mysqli_query($conn, $queryDel);
            if ($deleteRow === false) {
                throw new Exception(mysqli_error($conn));
            }
            header("Location: ../php/$route");
        } catch (mysqli_sql_exception $e) {
            echo "Error al eliminar los datos: " . $e->getMessage();
        } finally {
            $conn->close();
        }
    }

    public static function findRow($fields, $table, $idField, $id)//updated
    {
        include "db_connection.php";
        $queryFind = "SELECT $fields FROM $table WHERE $idField = ?";
        $data=array();
        try {
            if ($stmt = $conn->prepare($queryFind)) {
                $stmt->bind_param("s", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $data = $result->fetch_all(MYSQLI_ASSOC);
                $stmt->close();
            } else {
                throw new Exception($conn->error);
            }
        } catch (Exception $e) {
            $m = $e->getMessage();
            echo "<script>console.log('Error al obtener los datos: $m ')</script>";
        } finally {
            $conn->close();
        }
        return $data;
    }
    
    public static function findRow2Condition($fields, $table, $condition1, $value1, $condition2, $value2)//updated
    {
        include "db_connection.php";
        $queryFind = "SELECT $fields FROM $table WHERE $condition1 = ? AND $condition2 = ?";
        $data = array();
        try {
            if ($stmt = $conn->prepare($queryFind)) {
                $stmt->bind_param("ss", $value1, $value2);
                $stmt->execute();
                $result = $stmt->get_result();
                $data = $result->fetch_all(MYSQLI_ASSOC);
                $stmt->close();
            } else {
                throw new Exception($conn->error);
            }
        } catch (Exception $e) {
            echo "Error al obtener los datos: " . $e->getMessage();
        } finally {
            $conn->close();
        }
        return $data;
    }


    public static function findRows($fields, $table, $idField, $id)//updated
    {
        include "db_connection.php";
        $queryFind = "SELECT $fields FROM $table WHERE $idField = ?";
        $data = array();
        try {
            if ($stmt = $conn->prepare($queryFind)) {
                $stmt->bind_param("s", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $data = $result->fetch_all(MYSQLI_ASSOC);
                $stmt->close();
            } else {
                throw new Exception($conn->error);
            }
        } catch (Exception $e) {
            echo "Error al obtener los datos: " . $e->getMessage();
        } finally {
            $conn->close();
        }
        return $data;
    }

    public static function findRows2Condition($fields, $table, $condition1, $value1, $condition2, $value2)//updated
    {
        include "db_connection.php";
        $queryFind = "SELECT $fields FROM $table WHERE $condition1 = ? AND $condition2 = ?";
        $data = array();
    
        try {
            if ($stmt = $conn->prepare($queryFind)) {
                $stmt->bind_param("ss", $value1, $value2);
                $stmt->execute();
                $result = $stmt->get_result();
                $data = $result->fetch_all(MYSQLI_ASSOC);
                $stmt->close();
            } else {
                throw new Exception($conn->error);
            }
        } catch (Exception $e) {
            echo "Error al obtener los datos: " . $e->getMessage();
        } finally {
            $conn->close();
        }
        return $data;
    }

    public static function findRowsOrderBy($fields, $table, $idField, $id, $by, $order) //unused - updated
    {
        include "db_connection.php";
        $data = array();
    
        // Validar los parámetros
        $order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC'; 
        $by = preg_replace("/[^a-zA-Z0-9_]+/", "", $by); 
    
        // Consulta
        $queryFind = "SELECT $fields FROM $table WHERE $idField = ? ORDER BY `$by` $order";
    
        try {
            $stmt = $conn->prepare($queryFind);
            $stmt->bind_param("s", $id); 
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        } catch (mysqli_sql_exception $e) {
            echo "Error al obtener los datos: " . $e->getMessage();
        } finally {
            $conn->close();
        }
    
        return $data;
    }
    

    public static function getLastInserted($idField, $table) //unused - updated
    {
        include "db_connection.php";
        $last_inserted_id = null;

        // Consulta
        $query = "SELECT MAX($idField) AS last_id FROM $table";

        try {
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result) {
                $row = $result->fetch_assoc();
                $last_inserted_id = $row['last_id'];
            }
            
            $stmt->close();
        } catch (mysqli_sql_exception $e) {
            echo "Error al obtener el último ID insertado: " . $e->getMessage();
        } finally {
            $conn->close();
        }

        return $last_inserted_id;
    }


    public static function executeNonResultQuery($query, $params, $types, $destinationPage)//updated 
    {
        include "db_connection.php";
        try {
            $stmt = $conn->prepare($query);
            if ($params && $types) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            echo "<script>window.location.href = '../php/$destinationPage';</script>";
            exit();

            $stmt->close();
        } catch (mysqli_sql_exception $e) {
            $error = $e->getCode();
            echo "<script>window.location.href = '../php/$destinationPage?error=$error';</script>";
            exit();
        } finally {
            $conn->close();
        }
    }

    public static function executeNonResultQuery2($query, $params = [], $types = "", $destinationPage) // updated
    {
        include "db_connection.php";
        try {
            $stmt = $conn->prepare($query);
            if ($params && $types) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $stmt->close();
        } catch (mysqli_sql_exception $e) {
            $error = $e->getCode();
            echo "<script>console.log('Error: $error');</script>";
            header("Location: $destinationPage");

        } finally {
            $conn->close();
        }
    }


    public static function executeResultQuery($query, $params = [], $types = "")//updated
    {
        include "db_connection.php";
        $data = array();
        try {
            $stmt = $conn->prepare($query);
            if ($params && $types) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        } catch (mysqli_sql_exception $e) {
            $error = $e->getCode();
            echo "<script>console.log('Error: $error');</script>";
        } finally {
            $conn->close();
        }
        return $data;
    }


    public static function selectUserSearchData($fields, $table, $id, $order, $search) //updated
    {
        include "db_connection.php";
        $search = trim($search);
        $search = stripslashes($search);
        $search = htmlspecialchars($search);
        $search = mysqli_real_escape_string($conn, $search);
        $data = array();
    
        $searchLower = strtolower(trim($search));
    
        // Determinar el tipo de búsqueda (filtrado por los tipos de usuario en BD y en Display)
        if (in_array($searchLower, ['super-usuario', 'superusuario', 'super'])) {
            $searchQuery = " AND (id_usuario LIKE ? OR nombre LIKE ? OR rolUsuario LIKE 'ADM' OR correo LIKE ? OR departamento LIKE ?)";
            $params = ["%$search%", "%$search%", "%$search%", "%$search%"];
            $types = "ssss";
        } elseif (in_array($searchLower, ['estándar', 'estandar'])) {
            $searchQuery = " AND (id_usuario LIKE ? OR nombre LIKE ? OR rolUsuario LIKE 'EST' OR correo LIKE ? OR departamento LIKE ?)";
            $params = ["%$search%", "%$search%", "%$search%", "%$search%"];
            $types = "ssss";
        } elseif (in_array($searchLower, ['administrador', 'admin'])) {
            $searchQuery = " AND (id_usuario LIKE ? OR nombre LIKE ? OR rolUsuario LIKE 'SAD' OR correo LIKE ? OR departamento LIKE ?)";
            $params = ["%$search%", "%$search%", "%$search%", "%$search%"];
            $types = "ssss";
        } else {
            $searchQuery = " AND (id_usuario LIKE ? OR nombre LIKE ? OR rolUsuario LIKE ? OR correo LIKE ? OR departamento LIKE ?)";
            $params = ["%$search%", "%$search%", "%$search%", "%$search%", "%$search%"];
            $types = "sssss";
        }
    
        // Consulta completa 
        $myQuery = "SELECT $fields FROM $table WHERE 1=1 $searchQuery ORDER BY $id $order";
        
        try {
            $stmt = $conn->prepare($myQuery);
            if ($params && $types) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        } catch (mysqli_sql_exception $e) {
            echo "Error al obtener los datos: " . $e->getMessage();
        } finally {
            $conn->close();
        }
    
        return $data;
    }
    

    public static function selectProjectSearchData($fields, $table, $id, $order, $search)//updated
    {
        include "db_connection.php";
        $data = array();
        $search = "%{$search}%";
    
        // Consulta
        $searchQuery = " AND (id_proyecto LIKE ? OR departamentoAsignado LIKE ? OR nombre LIKE ? OR fecha_inicio LIKE ? OR fecha_cierre LIKE ?)";
    
        $myQuery = "SELECT $fields FROM $table WHERE estado=1 AND 1=1 $searchQuery ORDER BY $id $order;";
    
        try {
            $stmt = $conn->prepare($myQuery);
            $stmt->bind_param("sssss", $search, $search, $search, $search, $search);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        } catch (mysqli_sql_exception $e) {
            echo "Error al obtener los datos: " . $e->getMessage();
        } finally {
            $conn->close();
        }
    
        return $data;
    }
    

    public static function getFiltersOptions($tabla, $field)//updated
    {
        include "db_connection.php";
        $data = array();

        // Consulta
        $myQuery = "SELECT DISTINCT $field FROM $tabla";

        try {
            $stmt = $conn->prepare($myQuery);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        } catch (mysqli_sql_exception $e) {
            echo "Error al obtener los datos: " . $e->getMessage();
        } finally {
            $conn->close();
        }

        return $data;
    }

    public static function antiNaughty($string) //updated
    {
        include "db_connection.php";
        $returnString = mysqli_real_escape_string($conn, $string);  // Escapar caracteres peligrosos para SQL
        $returnString = htmlspecialchars($returnString, ENT_QUOTES, 'UTF-8');  // Convertir caracteres especiales a entidades HTML para evitar XSS
        return $returnString;
    }

    public static function insertNewActivity($query, $params, $types, $destinationPage) //unused - updated
    {
        include "db_connection.php";
        $query2 = 'ALTER TABLE tbl_actividades AUTO_INCREMENT = 1;';
        try {
            // Preparar la primera consulta
            $stmt = $conn->prepare($query);
            if ($params && $types) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
    
            // Verificar si la inserción fue exitosa
            if ($stmt->affected_rows > 0) {
                $stmt->close();
    
                // Ejecutar la segunda consulta para reiniciar AUTO_INCREMENT
                if ($conn->query($query2) === TRUE) {
                    echo "<script>window.location.href = '../php/$destinationPage';</script>";
                    exit();
                } else {
                    throw new mysqli_sql_exception("Error en la consulta de reinicio de AUTO_INCREMENT: " . $conn->error);
                }
            }
        } catch (mysqli_sql_exception $e) {
            $error = $e->getCode();
            echo "<script>window.location.href = '../php/$destinationPage?error=$error';</script>";
            exit();
        } finally {
            $conn->close();
        }
    }
    



     //Funciones tecnicas
     public static function isInArray($data, $condition)
     {
         $exist= false;
         for ($i=0; $i<count($data); $i++) {
             foreach ($data[$i] as $key => $value) {
                 if ($value == $condition) {
                     $exist= true;
                 }
             }
         }
         return $exist;
     }
 
     public static function isInArrayOver1Time($data, $condition)
     {
         $exist= false;
         $exist2= false;
         for ($i=0; $i<count($data); $i++) {
             foreach ($data[$i] as $key => $value) {
                 if ($value == $condition && $exist== true) {
                     $exist2= true;
                 }
                 if ($value == $condition) {
                     $exist= true;
                 }
             }
         }
         if ($exist==true && $exist2==true) {
             return true;
         } else {
             return false;
         }
     }

    public static function containsMaliciousPattern($input) {
        $maliciousPatterns = [
            '/<script\b[^>]*>(.*?)<\/script>/is',
            '/\bSELECT\b|\bDELETE\b|\bINSERT\b|\bUPDATE\b|\bDROP\b/i',
            '/--|#|\/\*/', 
            '/\' OR \'1\'=\'1\'/', 
            '/\'1\'=\'1\'/', 
            '/OR 1=1/',
            '/\' OR \'a\'=\'a\'/',
            '/\' OR \'\'=\'\'/'
        ];

        foreach ($maliciousPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }
        return false;
    }


    public static function logAction($usuario, $tipo, $logfor, $accion, $proyecto = null, $notifyUser = null, $seleccionados = null, $destinationPage) {
        include "db_connection.php";
        $query = "INSERT INTO `tbl_logs` (`usuario`, `tipo`, `logfor`, `accion`, `proyecto`, `notifyUser`, `seleccionados`, `fecha`, `viewed`)
                  VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), 0)";
        
        try {
            // Preparar la consulta
            $stmt = $conn->prepare($query);
    
            // Validar y asignar parámetros
            $usuario = filter_var($usuario, FILTER_VALIDATE_INT);
            $tipo = in_array($tipo, ['general', 'personal']) ? $tipo : 'personal';
            $logfor = in_array($logfor, ['sistema', 'usuario', 'todos', 'especificos']) ? $logfor : 'sistema';
            $accion = htmlspecialchars($accion, ENT_QUOTES, 'UTF-8');
            $proyecto = $proyecto !== null ? filter_var($proyecto, FILTER_VALIDATE_INT) : null;
            $notifyUser = $notifyUser !== null ? filter_var($notifyUser, FILTER_VALIDATE_INT) : null;
            $seleccionados = $seleccionados !== null ? htmlspecialchars($seleccionados, ENT_QUOTES, 'UTF-8') : null;
    
            // Vincular parámetros
            $stmt->bind_param('issssis', $usuario, $tipo, $logfor, $accion, $proyecto, $notifyUser, $seleccionados);
    
            $stmt->execute();
    
            // Verificar si la inserción fue exitosa
            if ($stmt->affected_rows > 0) {
                $stmt->close();
            } else {
                throw new mysqli_sql_exception("No se pudo registrar el log. Affected rows: " . $stmt->affected_rows);
            }
        } catch (mysqli_sql_exception $e) {
            $error = $e->getMessage();
            echo "<script>window.location.href = '../php/$destinationPage?error=noLogSaved';</script>";
            exit();
        } finally {
            $conn->close();
        }
    }
    
    
    

     public function getMysqliConnection() {
        return $this->mysqli;
    }
}
