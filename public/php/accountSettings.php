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
    <!-- <link rel="stylesheet" href="../css/themeStyles.css"> -->

</head>
<body class="short">
    <div class="container"> 
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <div class="main">
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
                <div class="fm-content">
                    <p>Contenido general.</p>
                    
                </div>
            </div>
            <div id="accountTabContent" class="tabContent">
                <div class="fm-content section" id="settingsDiv">
                    <label class="bold" for="name">Nombre:</label><br>
                    <input class="input" type="text" name="name" id="name" placeholder="Tu nombre" title="Tu nombre" autocomplete="off">
                    
                    <label class="bold" for="nickName">Usuario:</label><br>
                    <input class="input" type="text" name="nickName" id="nickName" placeholder="Nombre de usuario" title="Nombre que se muestra en tu sesión actual">
                    
                    <div class="flexAndSpaceDiv">
                        <button class="generalBtnStyle btn-green dataUpdate" id="dataUpdate" onclick="updateData()">Guardar Cambios</button>
                        <button class="generalBtnStyle btn-blue passwordUpdate" id="passwordUpdate" onclick="updatePassword()">Cambiar contraseña</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
