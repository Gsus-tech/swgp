<!-- Barra lateral -->
<div class="sidebar">
    <!-- Encabezado de barra -->
    <div class="sidebartop">
        <div class="sidebar-logo">
            <a id="LogoBtn" href="dashboard.php" title="Dashboard">
                <img src="../assets/logoCBC.png">
            </a>
        </div>

        <div class="sidebar-menu">
            <i class="fa fa-list"></i>            
        </div>
        
    </div>

    <hr class="sidebar-divider">
    <!-- Menu de la barra lateral -->
    <nav class="scroll sidebar-menu-list"> 
        <ul>
        <?php   if($_SESSION['rol']=='ADM'){  ?>
            <li><a href="userManagement.php" title="Gestión de usuarios"><i class="fa fa-users"><span 
                class="text">Gestión de usuarios</span></i></a></li>
        <?php } ?>
        <?php   if($_SESSION['rol']=='SAD' || $_SESSION['rol']=='ADM'){  ?>
            <li><a href="projectsManagement.php" title="Gestión de proyectos"><i class="fa fa-wrench"><span 
                class="text">Gestión de proyectos</span></i></a></li>
            <li><a href="projectFollowUp.php" title="Seguimiento de proyectos"><i class="fa fa-tasks"><span 
                class="text">Seguimiento de proyectos</span></i></a></li>
            <li><a href="support.php" title="Panel de soporte"><i class="fa fa-question"><span 
                class="text">Gestión de soporte</span></i></a></li>
                        
        <?php } ?>
    <?php   if($_SESSION['rol']=='EST'){  
                $isMember = array();
                $user=$condition=$_SESSION['id'];
                $data = crud::executeResultQuery("SELECT id_usuario FROM tbl_integrantes WHERE id_usuario = '$user'");
                $isMember[0] = crud::isInArray($data, $_SESSION['id']);
                $data2 = crud::executeResultQuery("SELECT responsable FROM tbl_integrantes WHERE id_usuario = '$user'");
                $isMember[1] = crud::isInArray($data2, 1);
                
                if($isMember[0]==true){ ?>
                    <li><a href="#" title="Tablero Kanban"><i class="fa fa-dashboard"><span 
                    class="text">Tablero Kanban</span></i></a></li>
                    <?php if($isMember[1]==true){ ?>
                        <li><a href="userTools/taskManagement.php" title="Gestión de actividades"><i class="fa fa-gears"><span 
                        class="text">Gestión de actividades</span></i></a></li>
                    <?php } ?>
                    <li><a href="#" title="Seguimiento de actividades"><i class="fa fa-tasks"><span 
                    class="text">Actividades del proyecto</span></i></a></li>
                    <li><a href="#" title="Módulo de soporte"><i class="fa fa-question"><span 
                    class="text">Soporte técnico</span></i></a></li>
                <?php
                }
            } ?>
        </ul>
    </nav> 

    <!-- Pie de barra -->
    <div class="account">
        <div class="details">
            <div class="avatar" id="avatarBtn" onclick="openAccountMenu()">
                <img src="../assets/profilePictures/pp_1.png">
            </div>
            <div class="name">
                <h4><?php echo $_SESSION['nickname']; ?></h4>
            </div> 
        </div>
    </div>
    
    <div id="accountMenu" class="accountMenu hide">
        <div class="accountDetails">
            <a href="#" title='Detalles de cuenta' class='verCuenta' id='verCuenta'><i class="fa fa-user"><span>Ver cuenta</span></i></a>
        </div>
        <hr style='margin: 5px 0'>
        <div class="settings">
            <a href="#" title='Detalles de cuenta' class='ajustes' id='ajustes'><i class="fa fa-gear"><span>Ajustes</span></i></a>
        </div>
        <hr style='margin: 5px 0'>
        <div class="logout">
            <a href="../log-session.php?logout=true" title='Cerrar sesión' class='logOutOption' id='logOutOption'><i class="fa fa-sign-out"><span>Cerrar sesión</span></i></a>
        </div>
        <hr style='margin: 5px 0'>
        
    </div>

</div> <!-- Fin de sidebar -->