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
    <title>SWGP - Ajustes de cuenta</title>
    <link rel="stylesheet" href="../assets/font-awesome-4.7.0/css/font-awesome.min.css">    
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/accountSettings.css">
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
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <div class="main">
            <div class="fondoCobach"></div>
            <div class="header flexAndSpaceDiv">
                <h4 class="headerTitle">Ajustes de cuenta</h4>
            </div>
            <div class="topBar">
                <ul class="tabMenu">
                    <li class="tab" id="generalTab">General</li>
                    <li class="tab" id="accountTab">Cuenta</li>
                </ul>
            </div>


            <div id="generalTabContent" class="tabContent">
            <?php
                $notification = $preferences[0]['notificaciones']===1 ? 'checked' : '';
                $letter = $preferences[0]['tLetra']==='Grande' ? 'checked' : '';
                $tema = $preferences[0]['tema'];
                switch ($tema) {
                    case 'Oscuro':
                        $t1 = 'checked';
                        $t2 = '';
                        $t3 = '';
                        break;
                    case 'Claro':
                        $t1 = '';
                        $t2 = '';
                        $t3 = 'checked';
                        break;
                    default:
                        $t1 = '';
                        $t2 = 'checked';
                        $t3 = '';
                        break;
                }

            ?>

                <div class="fm-content section" id="notificacionesDiv">
                    <div class="sectionDiv controlSection">
                        <label class="bold" for="name">Notificaciones:</label><br>
                        <label class="switch">
                            <input type="checkbox" id="notificationToggle" <?php echo $notification; ?>>
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                <br><br>
                <div class="fm-content section" id="usabilidadDiv">
                    <div class="sectionDiv">
                        <h3 class="bold" for="name">Usabilidad:</h3><br>
                        <div class="controlSection">
                            <label class="bold" for="name">Tema del sistema:</label><br>
                            

                            <div class="tw-toggle">
                                <input <?php echo $t1; ?> type="radio" name="themeToggle" value="dark" id="dk-tg" title="Oscuro">
                                <label class="toggle toggle-yes"><i class="fa fa-moon-o"></i></label>
                                <input <?php echo $t2; ?> type="radio" name="themeToggle" value="system" id="sy-tg" title="Sistema">
                                <label class="toggle toggle-yes"><i class="fa fa-star-half-full"></i></label>
                                <input <?php echo $t3; ?> type="radio" name="themeToggle" value="light" id="lg-tg" title="Claro">
                                <label class="toggle toggle-yes"><i class="fa fa-sun-o"></i></label>
                                <span></span>  
                            </div>


                        </div>
                        <br><br>
                        <div class="controlSection">
                            <label class="bold" for="name">Tamaño de letra:</label><br>
                            <div class="switch-container">
                                <span class="text-option lbl1">Normal</span>
                                <span title="Normal" class="text-option lbl2">N</span>
                                <label class="switch letterSwitch">
                                    <input type="checkbox" class="ltInput" id="letterToggle" <?php echo $letter; ?>>
                                    <span class="slider round ltSwSpan"></span>
                                </label>
                                <span title="Grande" class="text-option lbl2">G</span>
                                <span class="text-option lbl1">Grande</span>
                            </div>
                        </div>
                    </div>

                </div>
                <div id="preferenceOptions" class="flexAndSpaceDiv preferenceOptions">
                </div>
            </div>

            <div id="accountTabContent" class="tabContent">
                <div class="fm-content section" id="settingsDiv">
                    
                </div>
            </div>
        </div>
    </div>

    <script src="../js/validate.js"></script>
    <script src="../js/accountSettings.js"></script>
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
