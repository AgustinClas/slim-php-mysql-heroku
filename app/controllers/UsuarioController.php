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

  /* public function TraerUno($request, $response, $args)
  {
    // Buscamos usuario por nombre
    $usr = $args['usuario'];
    $usuario = Usuario::obtenerUsuario($usr);
    $payload = json_encode($usuario);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  } */

  public function TraerTodos($request, $response, $args)
  {
    $lista = Usuario::obtenerTodos();
    $payload = json_encode(array("listaUsuario" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

 /*public function ModificarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $id = $parametros['id'];
    $nombre = $parametros['nombre'];
    $clave = $parametros['clave'];
    Usuario::modificarUsuario($id, $nombre, $clave);

    $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function BorrarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $usuarioId = $parametros['id'];
    Usuario::borrarUsuario($usuarioId);

    $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function IniciarSesion($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $usuario = $parametros['usuario'];
    $clave = $parametros['clave'];

    $retorno = Usuario::VerificarSesion($usuario, $clave);

    if ($retorno === 1) $payload = json_encode(array("mensaje" => "Sesion iniciada"));
    else if ($retorno === -1) $payload = json_encode(array("mensaje" => "Contrasena incorrecta"));
    else $payload = json_encode(array("mensaje" => "Usuario inexistente"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function VerificarCredencial($request, $response, $args)
  {

    $parametros = $request->getParsedBody();

    $nombre = $parametros['nombre'];
    $perfil = $parametros['perfil'];

    if ($perfil == "administrador") {
      $payload = json_encode(array("Bienvenido " . $nombre));
    } else {
      $payload = json_encode(array("No tienes el permiso requerido"));
    }

    $response->getBody()->write($payload);
    
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function VerificarCredencialJSON($request, $response, $args)
  {

    $parametros = $request->getParsedBody();

    $JSON = $parametros['objJSON'];
    $empleado = json_decode($JSON);


    if ($empleado->perfil == "administrador"){  
      $payload = json_encode(array("Bienvenido " . $empleado->nombre));
      //status
    }
    else {
      $payload = json_encode(array("No tienes el permiso requerido"));
      //status
    }

    $response->getBody()->write($payload);

    return $response
      ->withHeader('Content-Type', 'application/json'); 
  }*/
}
