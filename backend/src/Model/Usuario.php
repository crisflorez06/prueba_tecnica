<?php
namespace App\Model;

use PDO;

class Usuario {
    private $conexion;
    private $nombreTabla = "usuarios";

    public $id;
    public $nombres;
    public $apellidos;
    public $telefono;
    public $correo;
    public $fechaRegistro;
    public $fechaModificacion;
    public $estado;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function traerTodos() {
        $stmt = $this->conexion->prepare("SELECT * FROM {$this->nombreTabla} WHERE estado=TRUE ORDER BY nombres ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear() {
        $stmt = $this->conexion->prepare(
            "INSERT INTO {$this->nombreTabla} 
            (nombres, apellidos, telefono, correo) 
            VALUES (:nombres, :apellidos, :telefono, :correo)"
        );
        return $stmt->execute([
            ":nombres" => $this->nombres,
            ":apellidos" => $this->apellidos,
            ":telefono" => $this->telefono,
            ":correo" => $this->correo
        ]);
    }

    public function editar() {
        $stmt = $this->conexion->prepare(
            "UPDATE {$this->nombreTabla} 
            SET nombres=:nombres, apellidos=:apellidos, telefono=:telefono, correo=:correo WHERE id=:id AND estado=TRUE"
        );
        return $stmt->execute([
            ":nombres" => $this->nombres,
            ":apellidos" => $this->apellidos,
            ":telefono" => $this->telefono,
            ":correo" => $this->correo,
            ":id" => $this->id
        ]);
    }

    public function eliminar() {
        $stmt = $this->conexion->prepare(
            "UPDATE {$this->nombreTabla} SET estado=FALSE WHERE id=:id"
        );
        return $stmt->execute([":id" => $this->id]);
    }

    public function buscarPorId() {
        $stmt = $this->conexion->prepare("SELECT * FROM {$this->nombreTabla} WHERE id=:id AND estado=TRUE LIMIT 1");
        $stmt->execute([":id" => $this->id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function buscar($parametros) {
        //traemos todos los usuarios activos
        $sql = "SELECT * FROM {$this->nombreTabla} WHERE estado=TRUE";

        //creamos un array con los parametros para evitar inyeccion sql
        $arrayParametros = [];

        //verificamos si existen parametros, si no que me traiga todos
        if (!empty($parametros)) {
            $condiciones = [];
            //añadimos cada parametro valido a la consulta
            foreach ($parametros as $key => $value) {
                if (in_array($key, ['nombres', 'apellidos', 'telefono', 'correo'])) {
                    $condiciones[] = "$key LIKE :$key";
                    $arrayParametros[$key] = "%$value%";
                }
            }
            //editamos la consulta sql inicial con los parametros
            if (!empty($condiciones)) {
                $sql .= " AND " . implode(" AND ", $condiciones);
            }
        }

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($arrayParametros);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
