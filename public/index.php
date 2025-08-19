<?php
/**
 * --- ENRUTADOR PRINCIPAL (SIMPLIFICADO PARA PRUEBAS) ---
 * Este archivo se enfoca únicamente en las funcionalidades de registro de especies
 * para facilitar las pruebas.
 */

// 1. Cargar el controlador de Especies
require_once '../app/Controllers/SpeciesController.php';

// 2. Analizar la URL para obtener la ruta
// ¡IMPORTANTE! Esta variable debe coincidir EXACTAMENTE con la ruta de tu proyecto en el navegador.
// Ejemplo: Si tu URL es http://localhost/ProyectoLP-Especies/public/, esta línea es correcta.
$base_folder = '/ProyectoLP-Especies/public'; 
$request_uri = $_SERVER['REQUEST_URI'];
$path = str_replace($base_folder, '', $request_uri);
$path = trim(parse_url($path, PHP_URL_PATH), '/');

// 3. Dividir la ruta en segmentos
$segments = explode('/', $path);
$resource = $segments[0] ?? '';
$action = $segments[1] ?? '';

// 4. Crear una instancia del controlador de Especies
$controller = new SpeciesController();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $resource === 'species') {
    // Si es POST a /species -> Guardar la nueva especie
    $controller->store();
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $resource === 'species') {
    // Si es GET a /species -> Mostrar la lista de todas las especies
    $controller->index();
} else {
    // Para cualquier otra cosa (como la raíz), mostramos el formulario de creación
    $controller->create();
}