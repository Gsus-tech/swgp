<?php
session_start();
require_once '../controller/generalCRUD.php';
use Controller\GeneralCrud\Crud;

if (isset($_SESSION['rol']) && isset($_SESSION['nombre'])) {

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv=»X-UA-Compatible» content=»IE-edge» charset=UTF-8″>
    <meta name="viewport" content="width=device-width" initial-scale=1.0″>
    <title>SWGP - Soporte</title>
    <link rel="stylesheet" href="../assets/font-awesome-4.7.0/css/font-awesome.min.css">    
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/table-style.css">
    <link rel="stylesheet" href="../css/support.css">
    <?php
    $query = "SELECT notificaciones,tema,tLetra FROM `tbl_preferencias_usuario` WHERE id_usuario = ?";
    $params = [$_SESSION['id']];
    $preferences = Crud::executeResultQuery($query, $params, 'i');
    if(count($preferences) > 0){
        $tema = '';
        if($preferences[0]['tema'] !== 'Sistema'){
            $tema = $preferences[0]['tema'] === 'Claro' ? 'lightMode' : 'darkMode';
        }
        $fontStyle = '';
        if($preferences[0]['tLetra'] === 'Grande'){
            $fontStyle = 'bigFont';
        }
    }
    
    ?>
</head>
<body class="short <?php $classes="$tema $fontStyle"; echo $classes; ?>">
    <div class="container">
<?php
    include 'sidebar.php';
    echo " <div class='main'> <div class='fondoCobach'></div>";
        if (isset($_SESSION['error_message'])) {
            $error_message = $_SESSION['error_message'];
            unset($_SESSION['error_message']);
            echo "<script>alert('Error: $error_message');</script>";
        }

        if ($_SESSION['rol'] === 'ADM' || $_SESSION['rol'] === 'SAD') {
            $query = 'SELECT tck.id_solicitud, usr.nombre, tck.tipoSolicitud, tck.estado, tck.fecha_creacion
            FROM tbl_solicitudes_soporte tck JOIN tbl_usuarios usr ON tck.id_usuario = usr.id_usuario 
            WHERE tck.estado = ? OR tck.estado = ?';
            $par = ['Abierto', 'Pendiente'];
            $tickets = Crud::executeResultQuery($query, $par, 'ss');
        ?>
        <div class="header flexAndSpaceDiv">
            <h4 class="headerTitle">Gestión de Soporte</h4>
        </div>

        <div class="table">
                <table id="ticketsTable">
                    <thead>
                        <tr>
                            <th class="rowNombre">Tipo de ticket</th>
                            <th class="rowAcciones">Usuario</th>
                            <th class="rowFecha">Fecha de creación</th>
                            <th class="rowEstado">Estado</th>
                            <th class="rowActions">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="ticketsTable_tbody">
                        <?php
                            if($tickets && count($tickets)>0){
                                foreach ($tickets as $ticket) {
                                    if($ticket['tipoSolicitud'] > 0 && $ticket['tipoSolicitud'] < 4){
                                        $ticketId = $ticket['id_solicitud'];
                                        $user = $ticket['nombre'];
                                        switch ($ticket['tipoSolicitud']) {
                                            case 1:
                                                $tipo = 'Error del Sistema';
                                                break;
                                            case 2:
                                                $tipo = 'Actualización de proyectos';
                                                break;
                                            case 3:
                                                $tipo = 'Cuentas de usuario';
                                                break;
                                        }
                                        $state = $ticket['estado'];
                                        $date = $ticket['fecha_creacion'];
                                        $switchState = $state === 'Abierto' ?
                                        "<i title=\"Marcar como pendiente\" class=\"button fa fa-toggle-on\"></i>" :
                                        "<i title=\"Marcar como abierto\" class=\"button fa fa-toggle-off\"></i>";
                                        echo 
                                        "<tr tck=\"$ticketId\" tcp=\"".$ticket['tipoSolicitud']."\">
                                            <td>".htmlspecialchars($tipo)."</td>
                                            <td>".htmlspecialchars($user)."</td>
                                            <td class='dateRow'>".htmlspecialchars($date)."</td>
                                            <td>".htmlspecialchars($state)."</td>
                                            <td>$switchState<i title=\"Resolver solicitud\" class=\"button fa fa-reply\"></i></td>
                                        </tr>";
                                    }
                                }
                            }else{
                                echo "<tr><td colspan='5' id='noRows'><i>Sin tickets de soporte que atender</i></td></tr>";
                            }
                        ?>
                        
                    </tbody>
                </table>
            </div>

        <div class="solvingArea"></div>
        <script src="../js/supportAdmin.js"></script>
        <script src="../js/validate.js"></script>
        <?php

        }else{
        ?>
        <div class="header flexAndSpaceDiv">
            <h4 class="headerTitle">Módulo de Soporte</h4>
        </div>

        <div class="supportFirstAction">
            <div class="fm-content">
                <button class="generalBtnStyle btn-orange" id="raiseTicketBtn">Levantar ticket</button>
                <button class="generalBtnStyle btn-blue" id="viewTicketStatusBtn">Ver estado de ticket</button>
            </div>
        </div>
            
        <div class="ticketDetails"></div>

        <script src="../js/validate.js"></script>
        <script src="../js/support.js"></script>
        <?php
        }
        ?>
        </div> <!-- Main -->
    </div> <!-- Container -->

<script src="../js/init.js"></script>
</body>
</html>
<?php
}
else{
    header("Location: dashboard.php");
    exit();
}
?>

