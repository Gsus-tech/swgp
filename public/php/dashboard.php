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
    <title>SWGP - Panel de inicio</title>
    <link rel="stylesheet" href="../assets/font-awesome-4.7.0/css/font-awesome.min.css">    
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dragula/3.7.3/dragula.min.css">
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
    <?php 
    $data1 = Controller\GeneralCrud\Crud::executeResultQuery("SELECT id_usuario FROM tbl_integrantes WHERE id_usuario = ?", [$_SESSION['id']], 'i');
    $screenAccess = Controller\GeneralCrud\Crud::isInArray($data1, $_SESSION['id']);
    if($screenAccess || $_SESSION['rol'] === 'ADM' || $_SESSION['rol'] === 'SAD'){
        ?>
    <div class="container"> 
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <div class="main">
            <div class="header flexAndSpaceDiv">
                <h4 class="headerTitle">Gestión de proyecto</h4>
                <?php $pagina="dashboard"; include 'topToolBar.php'; ?>
            </div>

            <?php
            $projectId = $_SESSION['projectSelected'];
            // Consulta para identificar si el usuario es responsable del proyecto
            $query = "SELECT responsable FROM tbl_integrantes WHERE id_proyecto = ? AND id_usuario = ?";
            $amIrep = Crud::executeResultQuery($query, [$projectId, $_SESSION['id']], 'ii');
            // Consulta para obtener todas las actividades del proyecto seleccionado
            $query = "SELECT * FROM tbl_actividades WHERE id_proyecto = ?";
            $actividades = Crud::executeResultQuery($query, [$projectId], 'i');

            function calcularPorcentaje($totalActividades, $actividadesCompletadas) {
                if ($totalActividades == 0) {
                    return 0;
                }
                return ($actividadesCompletadas / $totalActividades) * 100;
            }
            $totalActividades = count($actividades);
            $actividadesCompletadas = 0;
            if ($actividades) {
                foreach ($actividades as $actividad) {
                    $actividad['estadoActual'] == 4 ? $actividadesCompletadas++ : false;
                }
            }
            $porcentaje = calcularPorcentaje($totalActividades, $actividadesCompletadas);
            ?>

            <!-- Barra de progreso del proyecto -->
            <div class="nav-buttons progressIcon-div">
                <a id="progressIcon" class="button" title="Porcentaje de progreso"><i class="fa fa-percent"> del proyecto</i></a>
                
                <div class="progress-bar hide">
                    <div id="progress-bar-div" class="progress" style="width: <?php echo $porcentaje; ?>%;">
                        <?php echo round($porcentaje, 2); ?>%
                    </div>
                </div>
            </div>

            <?php
            
            // Consulta para obtener los miembros de las actividades
            $query2 = "SELECT tbl_usuarios.id_usuario, tbl_usuarios.nombre FROM tbl_usuarios JOIN tbl_actividades ON tbl_usuarios.id_usuario = tbl_actividades.id_usuario WHERE tbl_actividades.id_proyecto = ?";
            $participantes = Crud::executeResultQuery($query2, [$projectId], 'i');
            $arrayParticipantes = array();

            if ($participantes) {
                foreach($participantes as $participante){
                    $arrayParticipantes[$participante['id_usuario']] = $participante['nombre'];
                }
            }
            
            

            // Inicializar arrays para cada estado
            $pendientes = [];
            $en_proceso = [];
            $retrasadas = [];
            $terminadas = [];
            
            if ($actividades) {
                foreach ($actividades as $actividad) {
                    switch ($actividad['estadoActual']) {
                        case 1:
                            $pendientes[] = $actividad;
                            break;
                        case 2:
                            $en_proceso[] = $actividad;
                            break;
                        case 3:
                            $retrasadas[] = $actividad;
                            break;
                        case 4:
                            $terminadas[] = $actividad;
                            break;
                    }
                }
            }
            ?>


<!--   TESTING KANBAN DASHBOARDS   -->
 

<div class="kanban">
    <div class="kanban-title"><h2>Tablero de actividades</h2></div>
    <div class="kanban-board">
        <!-- Columna de pendientes -->
    <div class="tasks" data-plugin="dragula" id="pendientes">
        <h5 class="mt-0 task-header text-uppercase">Pendientes (<?php echo count($pendientes); ?>)</h5>
        <div id="task-list-one" class="task-list-items">
            <?php foreach ($pendientes as $tarjeta) : ?>
                <div class="card mb-0 <?php if((int)$_SESSION['id'] === (int)$tarjeta['id_usuario']){ echo 'tmc';} ?>" data-card-id="<?php echo htmlspecialchars($tarjeta['id_actividad']); ?>">
                    <div class="card-body p-3" >
                        <small class="float-end text-muted"><?php echo htmlspecialchars($tarjeta['fecha_estimada']); ?></small>
                       <?php 
                            $q2 = "SELECT * FROM tbl_notas WHERE id_actividad = ?";
                            $p2 = [$tarjeta['id_actividad']];
                            $actNotes = Crud::executeResultQuery($q2, $p2, 'i');
                            // echo "<script>console.log($q2)</script>";
                            if($actNotes && count($actNotes) > 0){
                                switch ($actNotes[0]['tipo']) {
                                    case 'Importante':
                                        $color = 'red-flag';
                                        break;
                                    case 'Completado':
                                        $color = 'green-flag';
                                        break;
                                    case 'InfoRequerida':
                                        $color = 'orange-flag';
                                        break;
                                    default:
                                        $color = null;
                                        break;
                                }
                                if($color !== null){
                                    $contenido = htmlspecialchars($actNotes[0]['contenido']);
                                    echo "<span title=\"$contenido\" class=\"button badge fa fa-sticky-note $color\"></span>";
                                }
                            }
                            
                        ?>
                        <h5 class="pt-10">
                            <a href="#" class="text-body"><?php echo htmlspecialchars($tarjeta['nombre_actividad']); ?></a>
                        </h5>
                        <p class="respName"><?php echo $arrayParticipantes[$tarjeta['id_usuario']] ?></p>
                        <p class="mb-0 hide"><?php
                        echo htmlspecialchars($tarjeta['descripción']); ?>
                        </p>
                        <?php 
                        if($_SESSION['rol'] === 'ADM' || $_SESSION['rol'] === 'SAD' || (!empty($amIrep) && (int)$amIrep[0]['responsable'] === 1)){
                            echo '<i class="fa fa-plus-square button cardMenu" title="Opciones"></i>';
                        }else if((int)$_SESSION['id'] === (int)$tarjeta['id_usuario']){
                            echo '<i class="fa fa-plus-square button cardMenu" title="Opciones"></i>';
                        }
                        ?>
                    </div> <!-- end card-body -->
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Columna de en proceso -->
    <div class="tasks" data-plugin="dragula" id="proceso">
        <h5 class="mt-0 task-header text-uppercase">En Proceso (<?php echo count($en_proceso); ?>)</h5>
        <div id="task-list-two" class="task-list-items">
            <?php foreach ($en_proceso as $tarjeta) : ?>
                <div class="card mb-0 <?php if((int)$_SESSION['id'] === (int)$tarjeta['id_usuario']){ echo 'tmc';} ?>" data-card-id="<?php echo htmlspecialchars($tarjeta['id_actividad']); ?>">
                    <div class="card-body p-3" >
                        <small class="float-end text-muted"><?php echo htmlspecialchars($tarjeta['fecha_estimada']); ?></small>
                        <!-- <span class="badge bg-danger">lol</span> -->
                        <h5 class="pt-10">
                            <a href="#" class="text-body"><?php echo htmlspecialchars($tarjeta['nombre_actividad']); ?></a>
                        </h5>
                        <p class="respName"><?php echo $arrayParticipantes[$tarjeta['id_usuario']] ?></p>
                        <p class="mb-0 hide"><?php 
                        echo htmlspecialchars($tarjeta['descripción']); ?>
                        </p>
                        <?php 
                        if($_SESSION['rol'] === 'ADM' || $_SESSION['rol'] === 'SAD' || (!empty($amIrep) && (int)$amIrep[0]['responsable'] === 1)){
                            echo '<i class="fa fa-plus-square button cardMenu" title="Opciones"></i>';
                        }else if((int)$_SESSION['id'] === (int)$tarjeta['id_usuario']){
                            echo '<i class="fa fa-plus-square button cardMenu" title="Opciones"></i>';
                        }
                        ?>
                    </div> <!-- end card-body -->
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Columna de retrasadas -->
    <div class="tasks" data-plugin="dragula" id="retrasadas">
        <h5 class="mt-0 task-header text-uppercase">Retrasadas (<?php echo count($retrasadas); ?>)</h5>
        <div id="task-list-three" class="task-list-items">
            <?php foreach ($retrasadas as $tarjeta) : ?>
                <div class="card mb-0 <?php if((int)$_SESSION['id'] === (int)$tarjeta['id_usuario']){ echo 'tmc';} ?>" data-card-id="<?php echo htmlspecialchars($tarjeta['id_actividad']); ?>">
                    <div class="card-body p-3" >
                        <small class="float-end text-muted"><?php echo htmlspecialchars($tarjeta['fecha_estimada']); ?></small>
                        <!-- <span class="badge bg-danger">lol</span> -->
                        <h5 class="pt-10">
                            <a href="#" class="text-body"><?php echo htmlspecialchars($tarjeta['nombre_actividad']); ?></a>
                        </h5>
                        <p class="respName"><?php echo $arrayParticipantes[$tarjeta['id_usuario']] ?></p>
                        <p class="mb-0 hide"><?php 
                        echo htmlspecialchars($tarjeta['descripción']); ?>
                        </p>
                        <?php 
                        if($_SESSION['rol'] === 'ADM' || $_SESSION['rol'] === 'SAD' || (!empty($amIrep) && (int)$amIrep[0]['responsable'] === 1)){
                            echo '<i class="fa fa-plus-square button cardMenu" title="Opciones"></i>';
                        } else if((int)$_SESSION['id'] === (int)$tarjeta['id_usuario']){
                            echo '<i class="fa fa-plus-square button cardMenu" title="Opciones"></i>';
                        }
                        ?>                    </div> <!-- end card-body -->
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Columna de terminadas -->
    <div class="tasks" data-plugin="dragula" id="terminadas">
        <h5 class="mt-0 task-header text-uppercase">Terminadas (<?php echo count($terminadas); ?>)</h5>
        <div id="task-list-four" class="task-list-items">
            <?php foreach ($terminadas as $tarjeta) : ?>
                <div class="card mb-0 <?php if((int)$_SESSION['id'] === (int)$tarjeta['id_usuario']){ echo 'tmc';} ?>" data-card-id="<?php echo htmlspecialchars($tarjeta['id_actividad']); ?>">
                    <div class="card-body p-3">
                        <small class="float-end text-muted"><?php echo htmlspecialchars($tarjeta['fecha_estimada']); ?></small>
                        <!-- <span class="badge bg-danger">lol</span> -->
                        <h5 class="pt-10">
                            <a href="#" class="text-body"><?php echo htmlspecialchars($tarjeta['nombre_actividad']); ?></a>
                        </h5>
                        <p class="respName"><?php echo $arrayParticipantes[$tarjeta['id_usuario']] ?></p>
                        <p class="mb-0 hide"><?php 
                        echo htmlspecialchars($tarjeta['descripción']); ?>
                        </p>
                        <?php 
                        if($_SESSION['rol'] === 'ADM' || $_SESSION['rol'] === 'SAD' || (!empty($amIrep) && (int)$amIrep[0]['responsable'] === 1)){
                            echo '<i class="fa fa-plus-square button cardMenu" title="Opciones"></i>';
                        } else if((int)$_SESSION['id'] === (int)$tarjeta['id_usuario']){
                            echo '<i class="fa fa-plus-square button cardMenu" title="Opciones"></i>';
                        }
                        ?>
                    </div> <!-- end card-body -->
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    </div>
</div>


<!--    END OF TESTING KANBAN DASHBOARDS    -->
        </div>     
    </div> <!-- Fin de container -->

    <script src="../js/init.js"></script>
    <?php
        if((!empty($amIrep) && (int)$amIrep[0]['responsable'] === 1) || $_SESSION['rol'] === 'ADM' || $_SESSION['rol'] === 'SAD'){
            echo '<script src="../js/dashboard.js"></script>';
            echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/dragula/3.7.3/dragula.min.js"></script>';
            echo '<script src="../js/ui/component.dragula.js"></script>';
        }else{
            echo '<script src="../js/dashboard-actions.js"></script>';
        } 
    }else{
        ?>
    <div class="container"> 
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <div class="main">
            <div class="header flexAndSpaceDiv">
                <h4 class="headerTitle">Bienvenido a SWGP-COBACH</h4>
                <?php $pagina="dashboard"; include 'topToolBar.php'; ?>
            </div>
    
            <div class="no-project-container">
                <h2>-- Sin proyectos para mostrar --</h2>
                <br>
                <p>No se encontraron proyectos en los que estés participando.</p>
                <p>Si consideras que esto es un error, por favor levanta un ticket en el módulo de soporte.</p>
                <a href="#" class="support-btn">Levantar Ticket</a>
            </div>

        </div>
    </div>
    <script src="../js/init.js"></script>

    <?php
    }
    ?>
</body>
</html>

<?php
}
else{
    header("Location: ../index.php");
    exit();
}
?>
