<?php
require_once './models/Producto.php';

class ProductoController extends Producto
{
    public function CargarUno($request, $response, $args)
    {
        if (isset($_POST["descripcion"]) && isset($_POST["sector"]) && isset($_POST["precio"])) {
            $parametros = $request->getParsedBody();

            $descripcion = $parametros['descripcion'];
            $sector = $parametros['sector'];
            $precio = $parametros['precio'];

            if (!Producto::EsNuevo($descripcion)) {
                $payload = json_encode(array("mensaje" => "Este producto ya existe"));
            }

            // Creamos el usuario
            $producto = new Producto();
            $producto->descripcion = $descripcion;
            $producto->sector = $sector;
            $producto->precio = $precio;
            $producto->AgregarProducto();

            $payload = json_encode(array("mensaje" => "Producto creado con exito"));

        } else {
            $payload = json_encode(array("mensaje" => "Datos insuficientes"));

        }

        $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
      $lista = Producto::obtenerTodos();
      $payload = json_encode(array("listaProductos" => $lista));
  
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

}
