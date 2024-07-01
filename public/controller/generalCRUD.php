<?php

namespace Controller\GeneralCrud;

// class executeResultQuery{
//     public static function executeResultQuery($query)
//     {
//         include "db_connection.php";
//         $data=array();
//         try {
//             $result =  mysqli_query($conn, $query);
//             if ($result) {
//                 $data = mysqli_fetch_all($result);
//                 return $data;
//                 exit();
//             }
//         } catch (mysqli_sql_exception $e) {
//         }
//     }
// }

// class isInArray{
//     public static function isInArray($data, $condition)
//     {
//         $exist= false;
//         for ($i=0; $i<count($data); $i++) {
//             foreach ($data[$i] as $key => $value) {
//                 if ($value == $condition) {
//                     $exist= true;
//                 }
//             }
//         }
//         return $exist;
//     }
// }

// class isInArrayOver1Time{
//     public function isInArrayOver1Time($data, $condition)
//     {
//         $exist= false;
//         $exist2= false;
//         for ($i=0; $i<count($data); $i++) {
//             foreach ($data[$i] as $key => $value) {
//                 if ($value == $condition && $exist== true) {
//                     $exist2= true;
//                 }
//                 if ($value == $condition) {
//                     $exist= true;
//                 }
//             }
//         }
//         if ($exist==true && $exist2==true) {
//             return true;
//         } else {
//             return false;
//         }
//     }
// }

class Crud
{

    public static function selectData($fields, $table, $id, $order)
    {
        include "db_connection.php";
        $myQuery = "SELECT $fields FROM $table ORDER BY $id $order;";
        $data=array();
        try {
            $listaResultados = mysqli_query($conn, $myQuery);
            if ($listaResultados === false) {
                throw new Exception(mysqli_error($conn));
            }
            $data = mysqli_fetch_all($listaResultados);
        } catch (mysqli_sql_exception $e) {
            echo "Error al obtener los datos: " . $e->getMessage();
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
        }
    }

    public static function findRow($fields, $table, $idField, $id)
    {
        include "db_connection.php";
        $queryFind = "SELECT $fields FROM $table WHERE $idField='$id';";
        $data=array();
        try {
            $rowFound =  mysqli_query($conn, $queryFind);
            if ($rowFound ===false) {
                throw new Exception(mysqli_error($conn));
            }
            $data = mysqli_fetch_all($rowFound);
        } catch (mysqli_sql_exception $e) {
        }
        return $data;
    }
    
    public static function findRow2Condition($fields, $table, $condition1, $value1, $condition2, $value2)
    {
        include "db_connection.php";
        $queryFind = "SELECT $fields FROM $table WHERE $condition1='$value1' AND $condition2='$value2';";
        $data=array();
        try {
            $rowFound =  mysqli_query($conn, $queryFind);
            if ($rowFound ===false) {
                throw new Exception(mysqli_error($conn));
            }
            $data = mysqli_fetch_all($rowFound);
        } catch (mysqli_sql_exception $e) {
        }
        return $data;
    }

    public static function findRows($fields, $table, $idField, $id)
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
        }
        return $data;
    }

    public static function findRows2Condition($fields, $table, $condition1, $value1, $condition2, $value2)
    {
        include "db_connection.php";
        $queryFind = "SELECT $fields FROM $table WHERE $condition1 = ? AND $condition2 = ?";
        $data = array();
    
        try {
            if ($stmt = $conn->prepare($queryFind)) {
                // Vincula los valores a los marcadores de posición
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
        }
        return $data;
    }

    public static function findRowsOrderBy($fields, $table, $idField, $id, $by, $order)
    {
        include "db_connection.php";
        $queryFind = "SELECT $fields FROM $table WHERE $idField='$id' ORDER BY `$by` $order;";
        $data = array();

        try {
            $listaResultados = mysqli_query($conn, $queryFind);
            if ($listaResultados === false) {
                throw new Exception(mysqli_error($conn));
            }
            $data = mysqli_fetch_all($listaResultados, MYSQLI_ASSOC);
        } catch (Exception $e) {
            echo "Error al obtener los datos: " . $e->getMessage();
        }
        return $data;
    }

    public static function getLastInserted($idField, $table)
    {
        include "db_connection.php";
        $query = "SELECT MAX($idField) AS last_id FROM $table";
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result);
        $last_inserted_id = $row['last_id'];
        return $last_inserted_id;
    }

    public static function executeNonResultQuery($query, $destinationPage)
    {
        include "db_connection.php";
        try {
            $result = mysqli_query($conn, (string)$query);
            if ($result) {
                echo "<script>window.location.href = '../php/$destinationPage';</script>";
                exit();
            }
        } catch (mysqli_sql_exception $e) {
            $error=$e->getCode();
            echo "<script>window.location.href = '../php/$destinationPage?error=$error';</script>";
            exit();
        }
    }

    public static function executeNonResultQuery2($query)
    {
        include "db_connection.php";
        try {
            $result = mysqli_query($conn, (string)$query);
        } catch (mysqli_sql_exception $e) {
            $error=$e->getCode();
            echo "<script>console.log('Error: $error');</script>";
        }
        return;
    }

    public static function executeResultQuery($query)
    {
        include "db_connection.php";
        $data=array();
        try {
            $result =  mysqli_query($conn, $query);
            if ($result) {
                $data = mysqli_fetch_all($result);
                return $data;
                exit();
            }
        } catch (mysqli_sql_exception $e) {
        }
    }

    public static function selectUserSearchData($fields, $table, $id, $order, $search)
    {
        include "db_connection.php";
        $search = mysqli_real_escape_string($conn, $search);


        if (strtolower($search)=='super-usuario') {
            $searchQuery = " AND (id_usuario LIKE '%$search%' OR nombre LIKE '%$search%' OR rolUsuario LIKE 'ADM' OR correo LIKE '%$search%' OR departamento LIKE '%$search%')";
        } elseif (strtolower($search)=='estándar') {
            $searchQuery = " AND (id_usuario LIKE '%$search%' OR nombre LIKE '%$search%' OR rolUsuario LIKE 'EST' OR correo LIKE '%$search%' OR departamento LIKE '%$search%')";
        } elseif (strtolower($search)=='administrador') {
            $searchQuery = " AND (id_usuario LIKE '%$search%' OR nombre LIKE '%$search%' OR rolUsuario LIKE 'SAD' OR correo LIKE '%$search%' OR departamento LIKE '%$search%')";
        } else {
            $searchQuery = " AND (id_usuario LIKE '%$search%' OR nombre LIKE '%$search%' OR rolUsuario LIKE '%$search%' OR correo LIKE '%$search%' OR departamento LIKE '%$search%')";
        }
        

        $myQuery = "SELECT $fields FROM $table WHERE 1=1 $searchQuery ORDER BY $id $order;";
        $data = array();

        try {
            $listaResultados = mysqli_query($conn, $myQuery);
            if ($listaResultados === false) {
                throw new Exception(mysqli_error($conn));
            }
            $data = mysqli_fetch_all($listaResultados, MYSQLI_ASSOC);
        } catch (Exception $e) {
            echo "Error al obtener los datos: " . $e->getMessage();
        }

        return $data;
    }

    public static function selectProjectSearchData($fields, $table, $id, $order, $search)
    {
        include "db_connection.php";
        $search = mysqli_real_escape_string($conn, $search);
        $searchQuery = " AND (id_proyecto LIKE '%$search%' OR nombre LIKE '%$search%' OR fecha_inicio LIKE '%$search%' OR fecha_cierre LIKE '%$search%')";

        $myQuery = "SELECT $fields FROM $table WHERE 1=1 $searchQuery ORDER BY $id $order;";
        $data = array();

        try {
            $listaResultados = mysqli_query($conn, $myQuery);
            if ($listaResultados === false) {
                throw new Exception(mysqli_error($conn));
            }
            $data = mysqli_fetch_all($listaResultados, MYSQLI_ASSOC);
        } catch (Exception $e) {
            echo "Error al obtener los datos: " . $e->getMessage();
        }

        return $data;
    }

    public static function getFiltersOptions($tabla, $field)
    {
        include "db_connection.php";
        $myQuery = "SELECT DISTINCT $field FROM $tabla";
        $data = array();

        try {
            $listaResultados = mysqli_query($conn, $myQuery);
            if ($listaResultados === false) {
                throw new Exception(mysqli_error($conn));
            }
            $data = mysqli_fetch_all($listaResultados, MYSQLI_ASSOC);
        } catch (Exception $e) {
            echo "Error al obtener los datos: " . $e->getMessage();
        }
        return $data;
    }

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

    public static function antiNaughty($string)
    {
        include "db_connection.php";
        $returnString = mysqli_real_escape_string($conn, $string);
        return $returnString;
    }

    public static function insertNewActivity($query, $destinationPage)
    {
        include "db_connection.php";
        $query2 = 'ALTER TABLE tbl_actividades AUTO_INCREMENT = 1;';
        try {
            $result = mysqli_query($conn, (string)$query);
            if ($result) {
                $result2 = mysqli_query($conn, (string)$query2);
                if ($result2) {
                    echo "<script>window.location.href = '../php/$destinationPage';</script>";
                    exit();
                }
            }
        } catch (mysqli_sql_exception $e) {
            $error=$e->getCode();
            echo "<script>window.location.href = '../php/$destinationPage?error=$error';</script>";
            exit();
        }
    }
}
