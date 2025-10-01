<?php
namespace App\Controller;

use App\Model\Usuario;
use PDO;
use Exception;
use PDOException;

class UsuarioController {
    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    //metodo principal que traer todos los usuarios para la tabla inicial
    public function index() {
        try {
            //usamos la conexion del constructor para evitar generar multiples conexiones
            $usuario = new Usuario($this->conexion);
            $resultados = $usuario->traerTodos();
            echo json_encode($resultados);
        //manejamos excepciones posible por mal consulta o error en el servidor
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error en la base de datos al traer los usuarios."]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Ocurrió un error inesperado en el servidor."]);
        }
    }

    public function id($id) {
        try {
            $usuario = new Usuario($this->conexion);
            $usuario->id = $id;
            $resultado = $usuario->buscarPorId();

            if ($resultado) {
                echo json_encode($resultado);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Usuario no encontrado."]);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error en la base de datos al buscar el usuario."]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Ocurrió un error inesperado en el servidor."]);
        }
    }

    public function crear() {
        $datos = json_decode(file_get_contents("php://input"), true);//guardamos los datos recibidos en formato php valido

        //hacemos validaciones para no confiar solo en los datos enviados en el frontend
        if (empty(trim($datos['nombres'] ?? ''))) {
            http_response_code(400);
            echo json_encode(["error" => "El campo 'nombres' es obligatorio."]);
            return;
        }

        if (empty(trim($datos['apellidos'] ?? ''))) {
            http_response_code(400);
            echo json_encode(["error" => "El campo 'apellidos' es obligatorio."]);
            return;
        }

        if (empty(trim($datos['telefono'] ?? ''))) {
            http_response_code(400);
            echo json_encode(["error" => "El campo 'telefono' es obligatorio."]);
            return;
        }
        if (!preg_match('/^[0-9]{7,15}$/', $datos['telefono'])) {
            http_response_code(400);
            echo json_encode(["error" => "El teléfono debe contener solo números (7 a 15 dígitos)."]);
            return;
        }

        if (empty(trim($datos['correo'] ?? '')) || !filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(["error" => "El correo proporcionado no es válido o está vacío."]);
            return;
        }

        try {
            $usuario = new Usuario($this->conexion);
            $usuario->nombres = trim($datos['nombres']);
            $usuario->apellidos = trim($datos['apellidos'] ?? '');
            $usuario->telefono = trim($datos['telefono'] ?? '');
            $usuario->correo = trim($datos['correo']);

            if ($usuario->crear()) {
                http_response_code(201);
                echo json_encode(["message" => "Usuario creado exitosamente."]);
            } else {
                http_response_code(400);
                echo json_encode(["error" => "No se pudo crear el usuario."]);
            }
        } catch (PDOException $e) {
            // Verificamos si el error es por una entrada duplicada, se lanza a duplicar el correo
            if ($e->getCode() == 23000) {
                http_response_code(409);
                echo json_encode(["error" => "El correo electrónico ya está registrado."]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Error en la base de datos al crear el usuario."]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Ocurrió un error inesperado en el servidor."]);
        }
    }

    public function editar($id) {
        $datos = json_decode(file_get_contents("php://input"), true);

        //hacemos validaciones para no confiar solo en los datos enviados en el frontend
        if (empty(trim($datos['nombres'] ?? ''))) {
            http_response_code(400);
            echo json_encode(["error" => "El campo 'nombres' es obligatorio."]);
            return;
        }

        if (empty(trim($datos['apellidos'] ?? ''))) {
            http_response_code(400);
            echo json_encode(["error" => "El campo 'apellidos' es obligatorio."]);
            return;
        }

        if (empty(trim($datos['telefono'] ?? ''))) {
            http_response_code(400);
            echo json_encode(["error" => "El campo 'telefono' es obligatorio."]);
            return;
        }
        if (!preg_match('/^[0-9]{7,15}$/', $datos['telefono'])) {
            http_response_code(400);
            echo json_encode(["error" => "El teléfono debe contener solo números (7 a 15 dígitos)."]);
            return;
        }

        if (empty(trim($datos['correo'] ?? '')) || !filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(["error" => "El correo proporcionado no es válido o está vacío."]);
            return;
        }

        try {
            $usuario = new Usuario($this->conexion);
            $usuario->id = $id;
            $usuario->nombres = trim($datos['nombres']);
            $usuario->apellidos = trim($datos['apellidos'] ?? '');
            $usuario->telefono = trim($datos['telefono'] ?? '');
            $usuario->correo = trim($datos['correo']);

            if ($usuario->editar()) {
                echo json_encode(["message" => "Usuario actualizado."]);
            } else {
                http_response_code(400);
                echo json_encode(["error" => "No se pudo actualizar el usuario."]);
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                http_response_code(409);
                echo json_encode(["error" => "El correo electrónico ya está en uso por otro usuario."]);
            }
            else {
                http_response_code(500);
                echo json_encode(["error" => "Error en la base de datos al editar el usuario."]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Ocurrió un error inesperado en el servidor."]);
        }
    }

    public function eliminar($id) {
        try {
            $usuario = new Usuario($this->conexion);
            $usuario->id = $id;

            if ($usuario->eliminar()) {
                echo json_encode(["message" => "Usuario eliminado."]);
            }
            else {
                http_response_code(404);
                echo json_encode(["error" => "No se pudo eliminar el usuario o no fue encontrado."]);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error en la base de datos al eliminar el usuario."]);
        }
        catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Ocurrió un error inesperado en el servidor."]);
        }
    }

    public function buscar() {
        try {
            $parametros = $_GET;
            $usuario = new Usuario($this->conexion);
            $resultados = $usuario->buscar($parametros);
            echo json_encode($resultados);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error en la base de datos durante la búsqueda."]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Ocurrió un error inesperado en el servidor."]);
        }
    }
}
