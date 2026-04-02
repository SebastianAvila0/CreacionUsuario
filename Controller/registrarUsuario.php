<?php

header('Content-Type: application/json');
require_once('../Model/consultas.php');
try {

    $creador = $_POST['creador'] ?? null;
    $usuario = $_POST['usuario'] ?? null;
    $codigo_interno = $_POST['codigo_interno'] ?? null;

    if (empty($codigo_interno) & empty($cedula) & empty($nombres) & empty($apellidos)) {
        http_response_code(400);
        echo json_encode(['error' => 'Parametros incorrectos']);
        exit;
    }

    $objConsultas = new Consultas();
    $registrarUsuario = $objConsultas->registrar_usuario($creador, $usuario, $codigo_interno);

    echo $json_datos = json_encode(
        $registrarUsuario
    );
} catch (Exception $error) {
    http_response_code(500);
    echo json_encode(['error' => $error->getMessage()]);
}
