<?php

header('Content-Type: application/json');
require_once('../Model/consultas.php');
try {
  // $bd = $_GET['bd'] ?? null;
  // $codcia = $_GET['codcia'] ?? null;
  $codigo_interno = $_GET['codigo_interno'] ?? null;
  // $programa = $_GET['programa'] ?? null;

  // if (empty($bd) || empty($codcia) || empty($usuario) || empty($programa)) {
  if (empty($codigo_interno)) {
    http_response_code(400);
    echo json_encode(['error' => 'Parametros incorrectos']);
    exit;
  }

  // $objConsultas = new Consultas($bd);
  $objConsultas = new Consultas();
  // $consultar_empresa = $objConsultas->consultar_empresa($codcia);

  $consultar_usuario = $objConsultas->consultar_usuario($codigo_interno);

  // $consultar_ingreso = $objConsultas->consultar_ingreso($codcia, $usuario, $programa);

  echo $json_datos = json_encode(
    // 'empresa' => $consultar_empresa,
    // 'usuario' => $consultar_usuario,
    $consultar_usuario
    // 'ingreso' => $consultar_ingreso
  );
} catch (Exception $error) {
  http_response_code(500);
  echo json_encode(['error' => $error->getMessage()]);
}
