<?php

header('Content-Type: application/json');
require_once('../Model/consultas.php');
try {

    $codigo_interno = $_GET['codigo_interno_cons'] ?? null;
    $cedula = $_GET['cedula_cons'] ?? null;
    $nombres = $_GET['nombres_cons'] ?? null;
    $apellidos = $_GET['apellidos_cons'] ?? null;

    if (empty($codigo_interno) & empty($cedula) & empty($nombres) & empty($apellidos)) {
        echo json_encode([
            'status' => false,
            'error' => 'Acceso denegado',
            'mensaje' => ''
        ]);
        exit;
    }

    $objConsultas = new Consultas();
    $consultar_empleado = $objConsultas->consultar_empleado1($codigo_interno, $cedula, $nombres, $apellidos);

    echo json_encode($consultar_empleado);
} catch (Exception $error) {
    echo json_encode([
        'status' => false,
        'error' => 'Error al consultar empleados',
        'mensaje' => $error->getMessage()
    ]);
}
