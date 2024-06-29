    <!DOCTYPE html>
<html lang="es">
<head>
    <title> SWGP-COBACH </title>
    <meta http-equiv=»X-UA-Compatible» content=»IE-edge» charset=UTF-8″>
    <meta name="viewport" content="width=device-width" initial-scale=1.0″>
    <link rel="icon" type="image/ico" href="favicon.ico">
    <link rel="stylesheet" href="assets/font-awesome-4.7.0/css/font-awesome.min.css">    
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <div class="limiter">
    <div class="container-login" style="background-image: url('assets/FondoDigital.png');">
    <div class="wrap-login">
        
        <!-- Formulario de inicio de sesión -->
        <form class="login-form" action="log-session.php?login=true" method="post">
            
            <span class="login-form-title">
            Sistema Web de Gestión de Proyectos del COBACH
            </span>

            <div class="wrap-inputLog">
                <input class="inputLog" type="email" name="userMail" placeholder="Correo" required
                oninvalid="this.setCustomValidity('Ingresa tu correo para iniciar sesión')" oninput="this.setCustomValidity('')"> 
                <i class="fa fa-user"></i>
            </div>
            <div class="wrap-inputLog">
                <input class="inputLog" type="password" name="password" id='password' placeholder="Contraseña" required
                oninvalid="this.setCustomValidity('Ingresa tu contraseña para iniciar sesión')" oninput="this.setCustomValidity('')"> 
                <i class="fa fa-lock"></i>
            </div>
            <div class='showPassDiv'>
                <input type="checkbox" class="checkBx" id="showPass" name="showPass" value="1">
                <label for="showPass" class="lbl">Mostrar contraseña</label>
            </div>
            <?php 
            $fullUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            if(strpos($fullUrl, "?") == true){
                echo "<p class='error'>Datos erróneos. Intenta de nuevo.</p>";
            }
            ?>
            <div class="container-login-form-btn">
                <button class="login-form-btn" type="submit">Iniciar Sesión</button>
            </div>
            <div>
            <a href="secciones/RecoverAccount/PasswordRecovery.php" class="btm-form-btn">Recuperar contraseña</a>
        </div>
        </form> <!-- Fin de login-form -->
    </div>
    </div>
    </div>
    <script>
        document.getElementById('showPass').addEventListener('click', function(){
            const x= document.getElementById('password');
            if(document.getElementById('showPass').checked ===true){
                x.type = 'text';}
            if(document.getElementById('showPass').checked ===false){ x.type = 'password';}
        });
    </script>
</body>
</html>
