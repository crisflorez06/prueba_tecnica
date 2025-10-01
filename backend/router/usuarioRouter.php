<?php
use App\Controller\UsuarioController;
//configuraciones necesarias para consumir nuestro backend desde angular con los diferentes metodos
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

//creamos el objeto de la bd y generamos la conexion
$bd = new App\Config\Database();
$conexion = $bd->getConnection();

//instaciamos el controlador
$controlador = new UsuarioController($conexion);

$metodo = $_SERVER['REQUEST_METHOD'];//guardamos el metodo
$uri = isset($_GET['uri']) ? explode('/', rtrim($_GET['uri'], '/')) : [];//guardamos los parametros

if ($uri[0] === 'usuarios') {
    header("Content-Type: application/json; charset=UTF-8");

    switch ($metodo) {
        case 'GET':
            //aca verificamos si estamos buscando con los datos mas generales o si necesitamos el usuario con id, si no existe segundo parametro es porque necesitamos traer todos los registros
            if (isset($uri[1])) {
                if ($uri[1] === 'buscar') {
                    $controlador->buscar();
                } else {
                    $controlador->id($uri[1]); 
                }
            } else {
                $controlador->index();
            }
            break;
        case 'POST':
            $controlador->crear();
            break;
        case 'PUT':
            //verificamos si le pasamos el id ya que es necesario
            if (isset($uri[1])) {
                $controlador->editar($uri[1]);
            } else {
                http_response_code(400);
                echo json_encode(["error" => "ID requerido"]);
            }
            break;
            //el mismo caso que tenemos en el put
        case 'DELETE':
            if (isset($uri[1])) {
                $controlador->eliminar($uri[1]);
            } else {
                http_response_code(400);
                echo json_encode(["error" => "ID requerido"]);
            }
            break;
            //default para metodos no utilizados
        default:
            http_response_code(405);
            echo json_encode(["error" => "Método no permitido"]);
    }
}
