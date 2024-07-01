
<?php
//Archivo modificado en dev
if($_SESSION['rol']==='ADM' || $_SESSION['rol']==='SAD' || $_SESSION['responsable']==true){
    $proyectos = Controller\GeneralCrud\Crud::getFiltersOptions('tbl_proyectos', 'id_proyecto');
    if($_SESSION['rol']==='ADM' || $_SESSION['rol']==='SAD'){
        if(isset($_GET['id'])){
            $selectedP = $_GET['id'];
            $_SESSION['projectSelected'] = $selectedP;
        }elseif($_SESSION['projectSelected'] != 0){
            $selectedP = $_SESSION['projectSelected'];
        }else{
            $user_id=$_SESSION['id'];
            $myProject = Controller\GeneralCrud\Crud::executeResultQuery("SELECT tbl_proyectos.id_proyecto, tbl_proyectos.nombre FROM tbl_proyectos;");
            $_SESSION['projectSelected'] = $myProject[0][0];
        }
    }
    
    if($_SESSION['responsable']==true){
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['selectedProject'])) {
                $selectedP = $_POST['selectedProject'];
                $_SESSION['projectSelected'] = $selectedP;
                // echo "<script>console.log('$selectedP');</script>";
            }elseif($_SESSION['projectSelected'] != 0){
                $selectedP = $_SESSION['projectSelected'];
            }
            else{ 
                $selected=0;
                $user_id=$_SESSION['id'];
                $myProject = Controller\GeneralCrud\Crud::executeResultQuery("SELECT proyectos.id_proyecto, proyectos.nombre FROM tbl_proyectos proyectos JOIN tbl_integrantes integrantes ON proyectos.id_proyecto = integrantes.id_proyecto WHERE integrantes.id_usuario = '$user_id' AND integrantes.responsable = 1;");
                $_SESSION['projectSelected'] = $myProject[0][0];
            }
    }
    // $abtn = $_SESSION['projectSelected'];
    // echo"<script> console.log('is this set: `$abtn`');</script>";
    
?>
<div class="topToolBar">
    <?php
    if($_SESSION['rol']==='ADM' || $_SESSION['rol']==='SAD' || $_SESSION['responsable+2']==true){
        ?>
        <a class="topBarText">Proyecto seleccionado:</a>
            <?php
            if($_SESSION['responsable+2']==true){
                echo "<form id='switchForm' method='post' action=''>";
                echo "<select name='listProyectosRes' id='listProyectosRes' class='listProyectos'>";
                $user_id=$_SESSION['id'];
                $filters = Controller\GeneralCrud\Crud::executeResultQuery("SELECT proyectos.id_proyecto, proyectos.nombre FROM tbl_proyectos proyectos JOIN tbl_integrantes integrantes ON proyectos.id_proyecto = integrantes.id_proyecto WHERE integrantes.id_usuario = '$user_id' AND integrantes.responsable = 1;");
            }else{
                echo "<select name='listProyectos' id='listProyectos' class='listProyectos'>";
                $filters = Controller\GeneralCrud\Crud::executeResultQuery('SELECT id_proyecto,nombre FROM tbl_proyectos;');
            }
            if(count($filters)>0){
                for($i=0;$i<count($filters);$i++){
                    $selected = ($selectedP == $filters[$i][0]) ? 'selected' : '';
                    echo '<option value='.$filters[$i][0].' ' . $selected .'>'.htmlspecialchars($filters[$i][1], ENT_QUOTES, 'UTF-8').'</option>';
                }
            }
            else{
                echo '<option>No hay proyectos registrados</option>';
            }
            ?>
    </select>
    <?php
    if($_SESSION['responsable+2']==true){
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

            window.location.href = `<?php echo $pagina ?>.php?id=${selectedValue}`;
        
            });
        });

    document.addEventListener('DOMContentLoaded', function() {
        const selectElement = document.getElementById('listProyectosRes');
        const hiddenInput = document.getElementById('selectedProject');
        const form = document.getElementById('switchForm');

        selectElement.addEventListener('change', function() {
            const selectedValue = selectElement.value;
            hiddenInput.value = selectedValue;
            form.submit();
        });
    });
</script>



<?php
}else{

}

?>