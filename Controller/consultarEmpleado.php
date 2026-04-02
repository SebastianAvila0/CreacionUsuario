<?php

header('Content-Type: application/json');
require_once('../Model/consultas.php');
try {

    $tipoConsulta = $_POST['tipoConsulta'] ?? null;
    if ($tipoConsulta == 1) {
        $codigo_interno = $_POST['codigo_interno'] ?? null;
    } elseif ($tipoConsulta == 2) {
        $codigo_interno = $_POST['codigo_interno_cons'] ?? null;
    }
    $cedula = $_POST['cedula_cons'] ?? null;
    $nombres = $_POST['nombres_cons'] ?? null;
    $apellidos = $_POST['apellidos_cons'] ?? null;

    if (empty($codigo_interno) & empty($cedula) & empty($nombres) & empty($apellidos)) {
        http_response_code(400);
        echo json_encode(['error' => 'Parametros incorrectos']);
        exit;
    }

    $objConsultas = new Consultas();
    if ($tipoConsulta == 1) {
        $consultar_empleado = $objConsultas->consultar_empleado($codigo_interno);
    } elseif ($tipoConsulta == 2) {
        $consultar_empleado = $objConsultas->consultar_empleado1($codigo_interno, $cedula, $nombres, $apellidos);
    }

    echo $json_datos = json_encode(
        $consultar_empleado
    );
} catch (Exception $error) {
    http_response_code(500);
    echo json_encode(['error' => $error->getMessage()]);
}
