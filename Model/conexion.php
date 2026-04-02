<?php
require_once('../../../lib/vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable('../../..');
$dotenv->load();

class Conexion
{

  private $conexion;

  // public function __construct($bd, $user) {
  public function __construct()
  {
    try {
      $HOST = $_ENV['HOST'];
      $PORT = $_ENV['PORT'];
      $DBNAME = $_ENV['DBNAME'];
      $USER = $_ENV['USER'];
      $PASS = $_ENV['PASS'];

      // $conexion = new PDO("pgsql:host=$HOST;port=$PORT;dbname=$DBNAME", $USER, $PASS);
      $conexion = new PDO("pgsql:host=$HOST;port=$PORT;dbname=$DBNAME", $USER, $PASS);
      $this->conexion = $conexion;
    } catch (Exception $error) {
      throw new Exception('No se pudo conectar con la base de datos' . $error);
    }
  }

  // private function crear_conexion($host, $port, $name, $user, $pass)
  // {
  //   try {
  //     $conexion = new PDO("pgsql:host=$host;port=$port;dbname=$name", $user, $pass);
  //     $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  //     return $conexion;
  //   } catch (Exception $e) {
  //     throw new Exception("Error de conexión: No se pudo conectar a la base de datos.");
  //   }
  // }

  public function get_conexion()
  {
    return $this->conexion;
  }
}
