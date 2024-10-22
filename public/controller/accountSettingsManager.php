<?php
session_start();
require_once '../controller/generalCRUD.php';
use Controller\GeneralCrud\Crud;

// Verificar que el usuario esté autenticado
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['rol']) && isset($_SESSION['nombre'])) {

    if(isset($_GET['verify']) && $_GET['verify'] == 'true'){
        echo json_encode(['success' => true, 'message' => 'Worked to here.']);
    }

} else {
    echo"<script>window.location.href = `../php/actionsManagement.php`;</script>";
}
?>
