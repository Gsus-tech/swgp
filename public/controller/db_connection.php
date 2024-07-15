<?php
/**
 * Define template file.
 *
 * @param string $templateFile
 */
$sname = "localhost";
$userN = "root";
$pass = "";

$db_name = "bd_swgp_cobach";

$conn = mysqli_connect($sname, $userN, $pass, $db_name);

if (!$conn) {
    echo "Falló la conexión con la base de datos";
}
