
<?php
//Archivo modificado en dev
if($_SESSION['rol']==='ADM' || $_SESSION['rol']==='SAD' || $_SESSION['responsable']==true){
    $proyectos = Controller\GeneralCrud\Crud::getFiltersOptions('tbl_proyectos', 'id_proyecto');
    if($_SESSION['rol']==='ADM' || $_SESSION['rol']==='SAD'){
        if(isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT) !== false){
            $selectedP = $_GET['id'];
            $_SESSION['projectSelected'] = $selectedP;
        }elseif($_SESSION['projectSelected'] != 0){
            $selectedP = $_SESSION['projectSelected'];
        }else{
            $user_id=$_SESSION['id'];
            $myProject = Controller\GeneralCrud\Crud::executeResultQuery("SELECT tbl_proyectos.id_proyecto, tbl_proyectos.nombre FROM tbl_proyectos WHERE tbl_proyectos.estado = 1;");
            $_SESSION['projectSelected'] = $myProject[0]['id_proyecto'];
            $selectedP = $_SESSION['projectSelected'];
        }
    }
    
    if($_SESSION['responsable']==true){
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['selectedProject']) && filter_var($_POST['selectedProject'], FILTER_VALIDATE_INT) !== false) {
            $selectedP = (int)$_POST['selectedProject'];
            $_SESSION['projectSelected'] = $selectedP;
        }elseif($_SESSION['projectSelected'] != 0){
            $selectedP = $_SESSION['projectSelected'];
        }
        else{ 
            $selected=0;
            $user_id=$_SESSION['id'];
            $myProject = Controller\GeneralCrud\Crud::executeResultQuery("SELECT proyectos.id_proyecto, proyectos.nombre FROM tbl_proyectos proyectos JOIN tbl_integrantes integrantes ON proyectos.id_proyecto = integrantes.id_proyecto WHERE integrantes.id_usuario = '$user_id' AND integrantes.responsable = 1 AND proyectos.estado = 1;");
            $_SESSION['projectSelected'] = $myProject[0]['id_proyecto'];
            $selectedP = $_SESSION['projectSelected'];
        }
    }
?>
<div class="topToolBar">
    <?php
    if($_SESSION['rol']==='ADM' || $_SESSION['rol']==='SAD' || $_SESSION['varios-proyectos']==true){
        
        echo "<a class='topBarText'>Proyecto seleccionado:</a>";
        
        if(isset($_SESSION['varios-proyectos']) && $_SESSION['varios-proyectos']==true){
            echo "<form id='switchForm' method='post' action=''>";
            echo "<select name='listProyectosRes' id='listProyectosRes' class='listProyectos'>";
            $user_id=$_SESSION['id'];
            $filterOpt = isset($filterOpt) && $filterOpt===true ? true : false;
            $filterReportAccess = isset($reportAccessOnly) && $reportAccessOnly===true ? true : false;
            if($filterOpt===true){
                $query = "SELECT proyectos.id_proyecto, proyectos.nombre FROM tbl_proyectos proyectos JOIN tbl_integrantes integrantes ON proyectos.id_proyecto = integrantes.id_proyecto WHERE integrantes.id_usuario = ? AND integrantes.responsable = 1 AND proyectos.estado = 1;";
                $filters = Controller\GeneralCrud\Crud::executeResultQuery($query, [$user_id], 'i');
            }elseif($filterReportAccess===true){
                $query = "SELECT DISTINCT proyectos.id_proyecto, proyectos.nombre FROM tbl_proyectos proyectos JOIN tbl_actividades actividades ON proyectos.id_proyecto = actividades.id_proyecto WHERE actividades.id_usuario = ? AND proyectos.estado = 1;";
                $filters = Controller\GeneralCrud\Crud::executeResultQuery($query, [$user_id], 'i');
            }else{    
                $query = "SELECT proyectos.id_proyecto, proyectos.nombre FROM tbl_proyectos proyectos JOIN tbl_integrantes integrantes ON proyectos.id_proyecto = integrantes.id_proyecto WHERE integrantes.id_usuario = ? AND proyectos.estado = 1;";
                $filters = Controller\GeneralCrud\Crud::executeResultQuery($query, [$user_id], 'i');
            }
        }
        else{
            echo "<select name='listProyectos' id='listProyectos' class='listProyectos'>";
            $filters = Controller\GeneralCrud\Crud::executeResultQuery('SELECT id_proyecto,nombre FROM tbl_proyectos WHERE estado = 1;');
        }
        if(count($filters)>0){
            for($i=0;$i<count($filters);$i++){
                $selected = ($selectedP == $filters[$i]['id_proyecto']) ? 'selected' : '';
                echo '<option value='.$filters[$i]['id_proyecto'].' ' . $selected .'>'.htmlspecialchars($filters[$i]['nombre'], ENT_QUOTES, 'UTF-8').'</option>';
            }
        }
        else{
            echo '<option>No hay proyectos registrados</option>';
        }
        
        echo "</select>";

        if(isset($_SESSION['varios-proyectos']) && $_SESSION['varios-proyectos']==true){
            echo "<input type='hidden' name='selectedProject' id='selectedProject'>";
            echo "</form>";
            echo "<input type='hidden' id='listProyectos'>";
        }else{
            echo "<input type='hidden' id='listProyectosRes'>";
            echo "<input type='hidden' id='selectedProject'>";
            echo "<input type='hidden' id='switchForm'>";
        }
    }else{
        echo "<input type='hidden' id='listProyectos'>";
        echo "<input type='hidden' id='listProyectosRes'>";
        echo "<input type='hidden' id='selectedProject'>";
        echo "<input type='hidden' id='switchForm'>";
    }
    ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dtoFilter = document.getElementById('listProyectos');

        dtoFilter.addEventListener('change', function() {
            const selectedOption = dtoFilter.options[dtoFilter.selectedIndex];
            const selectedValue = selectedOption.value;
            <?php 
                
                if(isset($projectDetails) && $projectDetails===true){
                    echo "window.location.href = '{$pagina}.php?projectDetails=' + selectedValue;";
                }else{
                    echo "window.location.href = '{$pagina}.php?id=' + selectedValue;";
                }
            ?>
            });
        });

    document.addEventListener('DOMContentLoaded', function() {
    const selectElement = document.getElementById('listProyectosRes');
    const hiddenInput = document.getElementById('selectedProject');

    selectElement.addEventListener('change', function() {
        const selectedValue = selectElement.value;
        hiddenInput.value = selectedValue;

        const formData = new FormData();
        formData.append('selectedProject', selectedValue);

        fetch('../controller/PHP-Request.php?change-selectedProject=true', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Proyecto cambiado exitosamente');
                window.location.reload();
            } else {
                console.error('Error al cambiar proyecto');
            }
        })
        .catch(error => {
            console.error('Error en la solicitud:', error);
        });
    });
});

</script>



<?php
}else{

}

?>