<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'stagiaires_management');

$conDb = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if (!$conDb) {
    echo("Erreur de connexion : " . mysqli_connect_error());
}

mysqli_set_charset($conDb, "utf8");
?>