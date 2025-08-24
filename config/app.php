<?php
define('BASE_URL', '/ProyectoLP-Especies');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('America/Guayaquil');

function debug($data) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}