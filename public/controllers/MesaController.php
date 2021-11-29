<?php
require_once './models/Mesa.php';

class MesaController extends Mesa
{
    public function CargarUno($request, $response, $args)
    {
        if (isset($_POST["codigo"])) {
            $parametros = $request->getParsedBody();

            $codigo = $parametros['codigo'];

            $mesa = new Mesa();
            $mesa->codigo = $codigo;
            $mesa->AgregarMesa();

            $payload = json_encode(array("mensaje" => "Mesa creada con exito"));

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

    public function TraerTodos($request, $response, $args)
    {
      $lista = Mesa::obtenerTodos();
      $payload = json_encode(array("listaMesas" => $lista));
  
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function TraerMasUsada($request, $response, $args){

        $mesa = Mesa::TraerMesaMasUsada();

        $payload = json_encode(array("Mesa" => $mesa));
  
        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }
}