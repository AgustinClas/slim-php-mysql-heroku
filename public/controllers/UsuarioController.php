<?php
require_once './models/Usuario.php';
require_once './interfaces/IApiUsable.php';

class UsuarioController extends Usuario
{
  public function CargarUno($request, $response, $args)
  {
    if (isset($_POST["perfil"]) && isset($_POST["nombre"])) {
      $parametros = $request->getParsedBody();

      $nombre = $parametros['nombre'];
      $perfil = $parametros['perfil'];

      // Creamos el usuario
      $usr = new Usuario();
      $usr->nombre = $nombre;
      $usr->perfil = $perfil;
      $usr->AgregarUsuario();

      $payload = json_encode(array("mensaje" => "Usuario creado con exito"));

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    } else {
      $payload = json_encode(array("mensaje" => "Datos insuficientes"));

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }
  }

  public function Login($request, $response, $args)
  {
    $payload = json_encode(array("mensaje" => "Datos insuficientes"));

    if (isset($_POST["nombre"]) && isset($_POST["perfil"])) {
      $parametros = $request->getParsedBody();

      $nombre = $parametros['nombre'];
      $perfil = $parametros['perfil'];

      $usr = new Usuario();
      $usr->nombre = $nombre;
      $usr->perfil = $perfil;

      if ($usr->ValidarDatos()) {

        $token = AutentificadorJWT::CrearToken(array("mail" => $nombre, "perfil" => $perfil));

        $payload = json_encode(array("mensaje" => "OK", "Token" => $token, "perfil" => $perfil));
      }else{
        $payload = json_encode(array("mensaje" => "Datos erroneos"));
      }
    }   
    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  } 


  public function TraerTodos($request, $response, $args)
  {
    $lista = Usuario::obtenerTodos();
    $payload = json_encode(array("listaUsuario" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

 
}
