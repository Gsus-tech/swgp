<!-- Barra lateral -->
<div class="sidebar">
    <!-- Encabezado de barra -->
    <div class="sidebartop">
        <div class="sidebar-logo">
            <span id="LogoBtn">
                <img src="../assets/logoCBC.png">
            </span>
        </div>

        <div class="sidebar-menu">
            <i class="fa fa-list"></i>            
        </div>
        
    </div>

    <hr class="sidebar-divider">
    <!-- Menu de la barra lateral -->
    <nav class="scroll sidebar-menu-list"> 
        <ul>
        <?php   if($_SESSION['rol']=='SAD' || $_SESSION['rol']=='ADM'){  ?>
            <li>
                <button onclick="window.location.href='dashboard.php'" title="Tablero Kanban">
                    <i class="fa fa-dashboard"></i>
                    <span class="text">Tablero Kanban</span>
                </button>
            </li>
            <li>
                <button onclick="window.location.href='userManagement.php'" title="Gestión de usuarios">
                    <i class="fa fa-users"></i>
                    <span class="text">Gestión de usuarios</span>
                </button>
            </li>
            <li>
                <button onclick="window.location.href='projectsManagement.php'" title="Gestión de proyectos">
                    <i class="fa fa-wrench"></i>
                    <span class="text">Gestión de proyectos</span>
                </button>
            </li>
            <li>
                <button onclick="window.location.href='activityManagement.php'" title="Seguimiento de proyectos">
                    <i class="fa fa-tasks"></i>
                    <span class="text">Seguimiento de proyectos</span>
                </button>
            </li>
            <li>
                <button onclick="window.location.href='support.php'" title="Panel de soporte">
                    <i class="fa fa-question"></i>
                    <span class="text">Gestión de soporte</span>
                </button>
            </li>

                        
        <?php } ?>
    <?php   if($_SESSION['rol']=='EST'){
                $isMember = array();
                $user=$condition=$_SESSION['id'];
                $data = Controller\GeneralCrud\Crud::executeResultQuery("SELECT id_usuario FROM tbl_integrantes WHERE id_usuario = ?", [$user], 'i');
                $isMember[0] = Controller\GeneralCrud\Crud::isInArray($data, $_SESSION['id']);
                $q2="SELECT i.responsable
                    FROM tbl_integrantes AS i
                    JOIN tbl_proyectos AS p ON i.id_proyecto = p.id_proyecto
                    WHERE i.id_usuario = ? AND i.responsable = 1 AND p.estado = 1;";
                $data2 = Controller\GeneralCrud\Crud::executeResultQuery($q2, [$user], 'i');
                $isMember[1] = Controller\GeneralCrud\Crud::isInArray($data2, 1);
                
                if ($isMember[0] == true) { ?>
                    <li>
                        <button onclick="window.location.href='dashboard.php'" title="Tablero Kanban">
                            <i class="fa fa-dashboard"></i>
                            <span class="text">Tablero Kanban</span>
                        </button>
                    </li>
                    <?php
                    if ($isMember[1] == true) { ?>
                        <li>
                            <button onclick="window.location.href='activityManagement.php'" title="Actividades del proyecto">
                                <i class="fa fa-gears"></i>
                                <span class="text">Actividades del proyecto</span>
                            </button>
                        </li>
                    <?php }
                    $allow2 = false;

                    $reportAccess=array();
                    $query = "SELECT id_proyecto FROM tbl_actividades WHERE id_usuario = ?";
                    $reportAccess = Controller\GeneralCrud\Crud::executeResultQuery($query, [$_SESSION['id']], 'i');
                    $allow2 = count($reportAccess) >= 1;
                
                    if ($allow2) { ?>
                        <li>
                            <button onclick="window.location.href='actionsManagement.php'" title="Reporte de actividades">
                                <i class="fa fa-tasks"></i>
                                <span class="text">Reporte de actividades</span>
                            </button>
                        </li>
                    <?php }
                    }
                    ?>
               <li>
                    <button onclick="window.location.href='support.php'" title="Módulo de soporte">
                        <i class="fa fa-question"></i>
                        <span class="text">Soporte técnico</span>
                    </button>
                </li>
            <?php
            } ?>
        </ul>
    </nav> 

    <!-- Pie de barra -->
    <div class="account">
        <div class="details">
            <div class="avatar button" id="avatarBtn" onclick="openAccountMenu()">
                <img src="../assets/profilePictures/pp_1.png">
            </div>
            <div class="name">
                <span><?php echo $_SESSION['nickname']; ?></span>
            </div> 
        </div>
    </div>
    
    <div id="accountMenu" class="accountMenu hide">
        <div class="accountDetails">
            <button title="Detalles de cuenta" class="verCuenta" id="verCuenta">
                <i class="fa fa-user">
                    <span>Ver cuenta</span>
                </i>
            </button>
        </div>
        <hr style="margin: 5px 0;">
        <div class="settings">
            <button title="Ajustes" class="ajustes" id="ajustes">
                <i class="fa fa-gear">
                    <span>Ajustes</span>
                </i>
            </button>
        </div>
        <hr style="margin: 5px 0;">
        <div class="logout">
            <button title="Cerrar sesión" class="logOutOption" id="logOutOption" onclick="window.location.href='../controller/log-session.php?logout=true'">
                <i class="fa fa-sign-out">
                    <span>Cerrar sesión</span>
                </i>
            </button>
        </div>

    </div>

</div> <!-- Fin de sidebar -->