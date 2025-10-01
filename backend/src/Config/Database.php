<?php

namespace App\Config;

use PDO;
use PDOException;

class Database {
    private $host;
    private $nombre;
    private $usuario;
    private $clave;
    public $conexion;

    public function __construct() {
        $this->host = $_ENV['DB_HOST'];
        $this->nombre = $_ENV['DB_DATABASE'];
        $this->usuario = $_ENV['DB_USER'];
        $this->clave = $_ENV['DB_PASSWORD'];
    }

    public function getConnection() {
        $this->conexion = null;
        try {
            //creamos la conexion
            $this->conexion = new PDO(
                "mysql:host={$this->host};dbname={$this->nombre}",
                $this->usuario,
                $this->clave
            );
            //añadimos los atributos a la conexion para manejar mas adecuadamente los errores en la bd(lanzar excepciones)
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conexion->exec("set names utf8");
        } catch(PDOException $excepcion) {
            throw $excepcion; //lanzamos la excepcion para despues manejarlo en el controlador
        }
        return $this->conexion;
    }
}
