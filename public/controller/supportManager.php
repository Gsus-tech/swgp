<?php
session_start();
require_once '../controller/generalCRUD.php';
use Controller\GeneralCrud\Crud;

$destination = "../php/support.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_SESSION['rol']==='ADM' || $_SESSION['rol']==='SAD' ) {

    }
    else if ($_SESSION['rol']==='EST' ) {

    }
    else {
        echo json_encode(['success' => false, 'message' => 'Método de solicitud no permitido.']);
    }
}else{
    header("Location: $destination");
}
