<?php
require_once './models/Pedido.php';
require_once './models/Mesa.php';

class PedidoController extends Pedido
{
    public function CargarUno($request, $response, $args)
    {
        $payload = json_encode(array("mensaje" => "Datos insuficientes"));
        
        if (isset($_POST["codigo"]) && isset($_POST["id_mesa"]) && isset($_POST["pedido"])) {
            $parametros = $request->getParsedBody();

            $codigo = $parametros['codigo'];
            $id_mesa = $parametros['id_mesa'];
            $descripcion = $parametros['pedido'];

            if(Mesa::UtilizarMesa($id_mesa)){
                if(!Pedido::VerificarCodigo($codigo)){ 
                    $pedido = new Pedido();
                    $pedido->codigo = $codigo;
                    $pedido->id_mesa = $id_mesa;
                    if($pedido->AgregarPedido($descripcion)) $payload = json_encode(array("mensaje" => "Pedido tomado con exito"));
                    else $payload = json_encode(array("mensaje" => "Producto/s inexistente/s"));

                    
                }else{
                    $payload = json_encode(array("mensaje" => "Codigo existente"));
                    
                }
            }else{
                $payload = json_encode(array("mensaje" => "Mesa inexistente u ocupada"));      
            
            }
            
        } 

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    
    }

    public function TraerTodos($request, $response, $args)
    {
      $lista = Pedido::obtenerTodos();
      $payload = json_encode(array("listaPedidos" => $lista));
  
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function AgregarFoto($request, $response, $args)
    {
        $payload = json_encode(array("mensaje" => "Datos insuficientes"));

        if (isset($_POST["codigo"]) && isset($_FILES["foto"])) {
            $parametros = $request->getParsedBody();

            $codigo = $parametros['codigo'];

            if(Pedido::RelacionarFoto($codigo, $_FILES["foto"]["tmp_name"])) $payload = json_encode(array("mensaje" => "Foto relacionada"));
            else $payload = json_encode(array("mensaje" => "Pedido inexistente"));
        }

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function ListarPendientes($request, $response, $args)
    {
        $payload = json_encode(array("mensaje" => "Datos insuficientes"));

        if (isset($_POST["sector"])) {
            $parametros = $request->getParsedBody();

            $sector = $parametros['sector'];
            $listado = ProductoDelPedido::TraerPendientesPorSector($sector);
            $payload = json_encode(array("listado" => $listado));
            
        }
        
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function PrepararBar($request, $response, $args)
    {
        $payload = json_encode(array("mensaje" => "Datos insuficientes"));

        if (isset($_POST["idPedido"]) && isset($_POST["idProducto"]) && isset($_POST["tiempoEstimado"])) {
            $parametros = $request->getParsedBody();

            $idPedido = $parametros['idPedido'];
            $idProducto = $parametros['idProducto'];
            $tiempoEstimado = $parametros['tiempoEstimado'];

            if(Pedido::PonerEnPreparacion($idPedido, $idProducto, $tiempoEstimado, "bar")) $payload = json_encode(array("mensaje" => "Producto en preparacion"));
            else $payload = json_encode(array("mensaje" => "Error en los datos"));
        }

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function PrepararCocina($request, $response, $args)
    {
        $payload = json_encode(array("mensaje" => "Datos insuficientes"));

        if (isset($_POST["idPedido"]) && isset($_POST["idProducto"]) && isset($_POST["tiempoEstimado"])) {
            $parametros = $request->getParsedBody();

            $idPedido = $parametros['idPedido'];
            $idProducto = $parametros['idProducto'];
            $tiempoEstimado = $parametros['tiempoEstimado'];

            if(Pedido::PonerEnPreparacion($idPedido, $idProducto, $tiempoEstimado, "cocina")) $payload = json_encode(array("mensaje" => "Producto en preparacion"));
            else $payload = json_encode(array("mensaje" => "Error en los datos"));
        }

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function PrepararCerveceria($request, $response, $args)
    {
        $payload = json_encode(array("mensaje" => "Datos insuficientes"));

        if (isset($_POST["idPedido"]) && isset($_POST["idProducto"]) && isset($_POST["tiempoEstimado"])) {
            $parametros = $request->getParsedBody();

            $idPedido = $parametros['idPedido'];
            $idProducto = $parametros['idProducto'];
            $tiempoEstimado = $parametros['tiempoEstimado'];

            if(Pedido::PonerEnPreparacion($idPedido, $idProducto, $tiempoEstimado, "cerveceria")) $payload = json_encode(array("mensaje" => "Producto en preparacion"));
            else $payload = json_encode(array("mensaje" => "Error en los datos"));
        }

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function ObtenerTiempoDeEspera($request, $response, $args)
    {
        $payload = json_encode(array("mensaje" => "Datos insuficientes"));

        if (isset($_POST["codigoMesa"]) && isset($_POST["codigoPedido"])) {
            $parametros = $request->getParsedBody();

            $codigoMesa = $parametros['codigoMesa'];
            $codigoPedido = $parametros['codigoPedido'];

            $tiempoEstimado = Pedido::ObtenerTiermpoDeEsperaEstimado($codigoMesa, $codigoPedido);

            if($tiempoEstimado == -1) $payload = json_encode(array("mensaje" => "No se le asigno un tiempo de espera aun"));
            else if($tiempoEstimado == -2) $payload = json_encode(array("mensaje" => "Datos incorrectos"));
            else $payload = json_encode(array("Tiempo estimado" => $tiempoEstimado));

        }

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function ListarEnPreparacion($request, $response, $args)
    {
        $payload = json_encode(array("mensaje" => "Datos insuficientes"));

        if (isset($_POST["sector"])) {
            $parametros = $request->getParsedBody();

            $sector = $parametros['sector'];
            $listado = ProductoDelPedido::TraerEnPreparacionPorSector($sector);
            $payload = json_encode(array("listado" => $listado));
            
        }
        
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }


    public function ServirBar($request, $response, $args)
    {
        $payload = json_encode(array("mensaje" => "Datos insuficientes"));

        if (isset($_POST["idProducto"])) {
            $parametros = $request->getParsedBody();

            $idProducto = $parametros['idProducto'];

            if(ProductoDelPedido::Servir($idProducto, "bar")) $payload = json_encode(array("mensaje" => "Producto listo para servir"));
            else $payload = json_encode(array("mensaje" => "Error en los datos"));
        }

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function ServirCocina($request, $response, $args)
    {
        $payload = json_encode(array("mensaje" => "Datos insuficientes"));

        if (isset($_POST["idProducto"])) {
            $parametros = $request->getParsedBody();

            $idProducto = $parametros['idProducto'];

            if(ProductoDelPedido::Servir($idProducto, "cocina")) $payload = json_encode(array("mensaje" => "Producto listo para servir"));
            else $payload = json_encode(array("mensaje" => "Error en los datos"));
        }

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function ServirCerveceria($request, $response, $args)
    {
        $payload = json_encode(array("mensaje" => "Datos insuficientes"));

        if (isset($_POST["idProducto"])) {
            $parametros = $request->getParsedBody();

            $idProducto = $parametros['idProducto'];

            if(ProductoDelPedido::Servir($idProducto, "cerveceria")) $payload = json_encode(array("mensaje" => "Producto listo para servir"));
            else $payload = json_encode(array("mensaje" => "Error en los datos"));
        }

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function ServirPedido($request, $response, $args)
    {
        $payload = json_encode(array("mensaje" => "Datos insuficientes"));

        if (isset($_POST["idPedido"]) && isset($_POST["entregadoATiempo"])){
            $parametros = $request->getParsedBody();

            $idPedido = $parametros['idPedido'];
            $entregadoATiempo = $parametros['entregadoATiempo'];

            if(Pedido::Servir($idPedido, $entregadoATiempo)) $payload = json_encode(array("mensaje" => "Pedido servido"));
            else $payload = json_encode(array("mensaje" => "Error en los datos"));
        }

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function CobrarPedido($request, $response, $args){
        $payload = json_encode(array("mensaje" => "Datos insuficientes"));

        if (isset($_POST["idPedido"])){
            $parametros = $request->getParsedBody();

            $idPedido = $parametros['idPedido'];

            if(Pedido::Cobrar($idPedido)) $payload = json_encode(array("mensaje" => "Cobrando pedido"));
            else $payload = json_encode(array("mensaje" => "Error en los datos"));
        }

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function CerrarMesa($request, $response, $args){
        $payload = json_encode(array("mensaje" => "Datos insuficientes"));

        if (isset($_POST["idPedido"])){
            $parametros = $request->getParsedBody();

            $idPedido = $parametros['idPedido'];

            if(Pedido::CerrarMesaYFacturar($idPedido)) $payload = json_encode(array("mensaje" => "Mesa cerrada"));
            else $payload = json_encode(array("mensaje" => "Error en los datos"));
        }

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function CargarExperiencia($request, $response, $args){
        $payload = json_encode(array("mensaje" => "Datos insuficientes"));

        if (isset($_POST["codigoPedido"]) && isset($_POST["codigoMesa"]) && isset($_POST["mesa"]) && isset($_POST["restaurante"]) && isset($_POST["mozo"]) && isset($_POST["cocinero"]) && isset($_POST["experiencia"])){
            $parametros = $request->getParsedBody();
            $codigoPedido = $parametros['codigoPedido'];
            $codigoMesa = $parametros['codigoMesa'];
            $mesa = $parametros['mesa'];
            $restaurante = $parametros['restaurante'];
            $mozo = $parametros['mozo'];
            $cocinero = $parametros['cocinero'];
            $experiencia = $parametros['experiencia'];
            
            Pedido::GuadarExperiencia($codigoPedido, $codigoMesa, $mesa, $restaurante, $mozo, $cocinero, $experiencia);

            $payload = json_encode(array("mensaje" => "reseña guardada"));
        }

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function TraerMejoresComentarios($request, $response, $args){
        $payload = json_encode(array("mensaje" => "Datos insuficientes"));

        if (isset($_POST["cantidad"])){
            $parametros = $request->getParsedBody();
            $cantidad = $parametros['cantidad'];

            $listado = Pedido::ObtenerMejoresComentarios($cantidad);
            $payload = json_encode(array("listado" => $listado));

            
        }

        $response->getBody()->write($payload);
            return $response
            ->withHeader('Content-Type', 'application/json');
    }
    
}

?>