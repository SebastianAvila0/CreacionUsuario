<?php
require_once('conexion.php');

function arreglar_consulta($consulta)
{
  $fila = [];
  $datos = [];
  while ($result = $consulta->fetch(PDO::FETCH_ASSOC)) {
    foreach ($result as $clave => $valor) {
      if (is_string($valor) && $valor) {
        $fila[$clave] = iconv('ISO-8859-1', 'UTF-8', $valor);
        $fila[$clave] = trim($fila[$clave], " ");
      } else {
        $fila[$clave] = $valor;
      }
    }
    $datos[] = $fila;
  }
  return $datos;
}

class Consultas
{
  private $conexion;
  private $fechaActual;
  private $horaActual;
  private $horaAtrasada;

  // public function __construct ($bd, $user = null) {
  public function __construct()
  {
    $this->fechaActual = date('Y-m-d'); // 'Y-m-d' para formato ISO
    $fecha = new DateTime('now');
    $this->horaActual = $fecha->format('H:i:s');
    $fecha->modify('-60 seconds');
    $this->horaAtrasada = $fecha->format('H:i:s');

    // $conexionDB = new Conexion($bd, $user);
    $conexionDB = new Conexion();
    $this->conexion = $conexionDB->get_conexion();
  }

  public function beginTransaction()
  {
    $this->conexion->beginTransaction();
  }

  public function commit()
  {
    $this->conexion->commit();
  }

  public function rollback()
  {
    $this->conexion->rollback();
  }

  public function consultar_empresa($codcia)
  {
    $script = "SELECT emp_nomb FROM gn_empre WHERE emp_codi = :codcia;";
    $consulta = $this->conexion->prepare($script);
    $consulta->bindParam(':codcia', $codcia);
    $consulta->execute();
    $response = arreglar_consulta($consulta);
    return $response;
  }


  public function consultar_usuario($codigo_interno)
  {
    $script = "SELECT nombres
        FROM empleados 
        WHERE codigo_interno = :codigo_interno 
          AND estado = '1';
      ";
    $consulta = $this->conexion->prepare($script);
    $consulta->bindParam(':codigo_interno', $codigo_interno);
    // $consulta->bindParam(':Fecha_Act', $this->fechaActual);
    $consulta->execute();
    $response = arreglar_consulta($consulta);
    // return ['data' => $response, 'fecha' => $this->fechaActual];
    return ['data' => $response];
  }

  public function consultar_ingreso($codcia, $usuario, $programa)
  {
    $script = "SELECT * FROM siaudpro 
        WHERE codcia = :codcia 
          AND audusua = :usuario 
          AND audfech = :fecha 
          AND hora >= :hora 
          AND programa = :programa;
      ";
    $consulta = $this->conexion->prepare($script);
    $consulta->bindParam(':codcia', $codcia);
    $consulta->bindParam(':usuario', $usuario);
    $consulta->bindParam(':fecha', $this->fechaActual);
    $consulta->bindParam(':hora', $this->horaAtrasada);
    $consulta->bindParam(':programa', $programa);
    $consulta->execute();
    $response = arreglar_consulta($consulta);
    return $response;
  }

  public function consultar_empleado($codigo_interno)
  {
    try {
      $script = "SELECT e.codigo_interno, e.nombres, e.apellidos, c.nombre_cargo
        FROM empleados e
        INNER JOIN cargos c ON e.codigo_cargo = c.codigo WHERE e.codigo_interno = :codigo_interno";
      $consulta = $this->conexion->prepare($script);
      if (!empty($codigo_interno)) {
        $consulta->bindParam(':codigo_interno', $codigo_interno);
      };
      $consulta->execute();
      $response = arreglar_consulta($consulta);
      return [
        'status' => true,
        'data' => $response
      ];
    } catch (Exception $error) {
      return [
        'status' => false,
        'error' => 'Error al consultar empleado',
        'mensaje' => $error->getMessage()
      ];
    }
  }

  public function consultar_empleado1($codigo_interno, $cedula, $nombres, $apellidos)
  {
    try {
      $parametros = '';
      if (!empty($codigo_interno)) {
        $parametros .= "AND e.codigo_interno LIKE :codigo_interno ";
      }
      if (!empty($cedula)) {
        $parametros .= "AND e.cedula LIKE :cedula ";
      }
      if (!empty($nombres)) {
        $parametros .= "AND LOWER(e.nombres) LIKE LOWER(:nombres) ";
      }
      if (!empty($apellidos)) {
        $parametros .= "AND LOWER(e.apellidos) LIKE LOWER(:apellidos) ";
      }
      $script = "SELECT e.codigo_interno, e.nombres, e.apellidos, c.nombre_cargo
      FROM empleados e
      INNER JOIN cargos c ON e.codigo_cargo = c.codigo WHERE 1=1 ";
      $script .= $parametros;
      $script .= "ORDER BY e.codigo_interno";

      $consulta = $this->conexion->prepare($script);
      if (!empty($codigo_interno)) {
        $codigo_interno = "%" . $codigo_interno . "%";
        $consulta->bindParam(':codigo_interno', $codigo_interno);
        $parametros .= ' ' . $codigo_interno;
      }
      if (!empty($cedula)) {
        $cedula = "%" . $cedula . "%";
        $consulta->bindParam(':cedula', $cedula);
        $parametros .= ' ' . $cedula;
      }
      if (!empty($nombres)) {
        $nombres = "%" . $nombres . "%";
        $consulta->bindParam(':nombres', $nombres);
        $parametros .= ' ' . $nombres;
      }
      if (!empty($apellidos)) {
        $apellidos = "%" . $apellidos . "%";
        $consulta->bindParam(':apellidos', $apellidos);
        $parametros .= ' ' . $apellidos;
      }
      $consulta->execute();
      $response = arreglar_consulta($consulta);
      return [
        'status' => true,
        'data' => $response,
        'consulta' => $consulta,
        'parametros' => $parametros
      ];
    } catch (Exception $error) {
      return [
        'status' => false,
        'error' => 'Error al consultar empleados',
        'mensaje' => $error->getMessage()
      ];
    }
  }

  public function registrar_usuario($creador, $usuario, $codigo_interno)
  {
    try {
      $script = 'INSERT INTO usuarios(
              creador,fecha_creacion,usuario,codigo_interno,fecha_vigencia,clave,estado) 
              VALUES(:creador,:fecha_creacion,:usuario,:codigo_interno,:fecha_vigencia,:clave,:estado)
          ';
      $date = new DateTime();
      $date->modify('+3 months');
      $fecha_vigencia = $date->format('Y-m-d');
      $estado = 1;
      $clave = 160428;
      $fechaHora = $this->fechaActual . ' ' . $this->horaActual;
      $consulta = $this->conexion->prepare($script);
      $consulta->bindParam(':creador', $creador);
      $consulta->bindParam(':fecha_creacion', $fechaHora);
      $consulta->bindParam(':usuario', $usuario);
      $consulta->bindParam(':codigo_interno', $codigo_interno);
      $consulta->bindParam(':fecha_vigencia', $fecha_vigencia);
      $consulta->bindParam(':clave', $clave);
      $consulta->bindParam(':estado', $estado);
      // echo 'console.log(' . json_encode($script) . $creador . $fechaHora . $fecha_vigencia . ')';
      $consulta->execute();
      return [
        'status' => true,
        'data' => 'El usuario se ha registrado correctamente'
      ];
    } catch (Exception $error) {
      return [
        'status' => false,
        'error' => 'Error al registrar usuario',
        'mensaje' => $error->getMessage()
      ];
    }
  }
}
