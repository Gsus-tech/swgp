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
</head>
<body class="short">
    <div class="container"> 
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <div class="main">
            <div class="header flexAndSpaceDiv">
                <h4 class="headerTitle">Dashboard</h4>
                <?php $pagina="dashboard"; include 'topToolBar.php'; ?>
            </div>

            <?php
            $projectId = $_SESSION['projectSelected'];

            // Consulta para obtener todas las actividades del proyecto seleccionado
            $query = "SELECT * FROM tbl_actividades WHERE id_proyecto = ?";
            $actividades = Crud::executeResultQuery($query, [$projectId], 'i');
            
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
                            $terminadas[] = $actividad;
                            break;
                            case 4:
                            $retrasadas[] = $actividad;
                            break;
                    }
                }
            }
            ?>

<div class="kanban-board">
    <div class="kanban-column" id="pendientes" ondragover="allowDrop(event)" ondrop="drop(event, 1)">
        <h2>Pendientes</h2>
        <?php foreach ($pendientes as $tarea): ?>
            <div class="kanban-item" id='task-<?php echo $tarea["id"]; ?>' draggable="true" ondragstart="drag(event)">
                <h3><?php echo htmlspecialchars($tarea['nombre_actividad']); ?></h3>
                <p><?php echo htmlspecialchars($tarea['fecha_estimada']); ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="kanban-column" id="en_proceso" ondragover="allowDrop(event)" ondrop="drop(event, 2)">
        <h2>En proceso</h2>
        <?php foreach ($en_proceso as $tarea): ?>
            <div class="kanban-item" id='task-<?php echo $tarea["id"]; ?>' draggable="true" ondragstart="drag(event)">
                <h3><?php echo htmlspecialchars($tarea['nombre_actividad']); ?></h3>
                <p><?php echo htmlspecialchars($tarea['fecha_estimada']); ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="kanban-column" id="retrasadas" ondragover="allowDrop(event)" ondrop="drop(event, 3)">
        <h2>Retrasadas</h2>
        <?php foreach ($retrasadas as $tarea): ?>
            <div class="kanban-item" id='task-<?php echo $tarea["id"]; ?>' draggable="true" ondragstart="drag(event)">
                <h3><?php echo htmlspecialchars($tarea['nombre_actividad']); ?></h3>
                <p><?php echo htmlspecialchars($tarea['fecha_estimada']); ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="kanban-column" id="terminadas" ondragover="allowDrop(event)" ondrop="drop(event, 4)">
        <h2>Terminadas</h2>
        <?php foreach ($terminadas as $tarea): ?>
            <div class="kanban-item" id='task-<?php echo $tarea["id"]; ?>' draggable="true" ondragstart="drag(event)">
                <h3><?php echo htmlspecialchars($tarea['nombre_actividad']); ?></h3>
                <p><?php echo htmlspecialchars($tarea['fecha_estimada']); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</div>





        </div>

        
    </div> <!-- Fin de container -->

    <script src="../js/init.js"></script>
</body>
</html>

<?php
}
else{
    header("Location: ../index.php");
    exit();
}
?>
