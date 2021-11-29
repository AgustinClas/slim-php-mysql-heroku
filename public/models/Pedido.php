<?php
include_once "db/AccesoDatos.php";
require_once './models/ProductoDelPedido.php';
require_once './models/Producto.php';


class Pedido{

    public $id;
    public $codigo;
    public $id_mesa;
    public $estado;
    public $tiempoEstimado;
    public $entregaATiempo;
    public $productosDelPedido;

    public function AgregarPedido($pedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        
        $arrayPedidos = explode(",", $pedido);

        foreach($arrayPedidos as $array){
            $auxArray = explode(":",$array);

            $id_producto = Producto::VerificarProducto($auxArray[1]);

            if($id_producto == -1)
            return false;
        }
        

        
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (codigo, id_mesa, estado) VALUES (:codigo, :id_mesa, 'pedido pendiente')");
        $consulta->bindValue(':codigo', $this->codigo, PDO::PARAM_STR);
        $consulta->bindValue(':id_mesa', $this->id_mesa);
        $consulta->execute();

        $id = Pedido::ObtenerUltimoId();

        ProductoDelPedido::AgregarProductosAlPedido($id, $pedido);

        return true;
        return false;
    }

    public static function ObtenerUltimoId(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("Select max(id) from pedidos");
        $consulta->execute();

        return $consulta->fetchColumn();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigo, id_mesa, estado, tiempo_estimado as tiempoEstimado FROM pedidos");
        $consulta->execute();

        $listado = $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');

        foreach($listado as $pedido){
            $pedido->productosDelPedido = ProductoDelPedido::ObtenerProductosPorId($pedido->id);
        }

        return $listado;
    }


    public static function RelacionarFoto($codigo, $imagen)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigo, id_mesa, estado, tiempo_estimado as tiempoEstimado FROM pedidos where codigo = :codigo");
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
        $consulta->execute();
        
        $listado = $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');


        if(count($listado) == 0) return false;

        if (!is_dir("FotosPedidos/")) {
            mkdir("FotosPedidos/", 0777, true);
        }

        $destino = "FotosPedidos/" . $listado[0]->codigo . "-" . $listado[0]->id_mesa . ".jpg";
        move_uploaded_file($imagen, $destino);

        return true;
    }

    public static function VerificarCodigo($codigo){

        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigo, id_mesa, estado, tiempo_estimado as tiempoEstimado FROM pedidos where codigo = :codigo");
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
        $consulta->execute();
        
        $listado = $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');


        if(count($listado) == 0) return false;

        return true;
    }

    public static function PonerEnPreparacion($idPedido, $idProducto , $tiempoEstimado, $sector){

        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigo, id_mesa, estado, tiempo_estimado as tiempoEstimado FROM pedidos where id = :id");
        $consulta->bindValue(':id', $idPedido, PDO::PARAM_STR);

        $consulta->execute();

        $listado = $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');


        if(count($listado) > 0 && ProductoDelPedido::PonerEnPreparacion($idPedido, $idProducto, $sector)){
            $consulta = $objAccesoDatos->prepararConsulta("Update pedidos set estado = 'en preparacion', tiempo_estimado = :tiempoEstimado where id = :id");
            $consulta->bindValue(':id', $idPedido, PDO::PARAM_STR);
            $consulta->bindValue(':tiempoEstimado', Pedido::AsignarTiempo($tiempoEstimado, $listado[0]->tiempoEstimado));

            $consulta->execute();

            return true;
        }

        return false;
    }

    public static function AsignarTiempo($tiempoEstimadoNuevo, $tiempoEstimado){
        if($tiempoEstimado == null || $tiempoEstimadoNuevo > $tiempoEstimado) return $tiempoEstimadoNuevo;

        return $tiempoEstimado;
    }   

    public static function ObtenerTiermpoDeEsperaEstimado($codigoMesa, $codigoPedido){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigo, id_mesa, estado, tiempo_estimado as tiempoEstimado FROM pedidos where codigo = :codigo");
        $consulta->bindValue(':codigo', $codigoPedido);

        $consulta->execute();

        $listado = $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');


        if(count($listado) > 0 && Mesa::VerificarCodigo($codigoMesa, $listado[0]->id_mesa)){
            if($listado[0]->tiempoEstimado == null) return -1;

            return $listado[0]->tiempoEstimado;
        }

        return -2;
    }

    public static function VerificarEstado($id){

        $listado = ProductoDelPedido::ObtenerProductosPorId($id);

        foreach($listado as $producto){
            if($producto->estado != "listo para servir") return false;
        }


        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("Update pedidos set estado = 'listo para servir' where id = :id");
        $consulta->bindValue(':id', $id);


        $consulta->execute();
    }

    public static function Servir($id, $entrega){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigo, id_mesa, estado, tiempo_estimado as tiempoEstimado FROM pedidos where id = :id and estado = 'listo para servir'");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);

        $consulta->execute();

        $listado = $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');


        if(count($listado) > 0){
            $consulta = $objAccesoDatos->prepararConsulta("Update pedidos set estado = 'servido', entrega_a_tiempo = :entregado where id = :id");
            $consulta->bindValue(':id', $id);
            $consulta->bindValue(':entregado', $entrega);


            $consulta->execute();

            Mesa::CambiarEstado($listado[0]->id_mesa, "Cliente comiendo");
            ProductoDelPedido::ActualizarProductosServidos($id);

            return true;
        }

        return false;
    }

    public static function Cobrar($id){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigo, id_mesa, estado, tiempo_estimado as tiempoEstimado FROM pedidos where id = :id and estado = 'servido'");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);


        $consulta->execute();

        $listado = $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');


        if(count($listado) > 0){
            $consulta = $objAccesoDatos->prepararConsulta("Update pedidos set estado = 'cobrando' where id = :id");
            $consulta->bindValue(':id', $id, PDO::PARAM_STR);

            $consulta->execute();

            Mesa::CambiarEstado($listado[0]->id_mesa, "Cliente pagando");

            return true;
        }

        return false;
    }
    
    public static function CerrarMesaYFacturar($id){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigo, id_mesa, estado, tiempo_estimado as tiempoEstimado FROM pedidos where id = :id and estado = 'cobrando'");
        $consulta->bindValue(':id', $id);
        $consulta->execute();

        $listado = $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');


        if(count($listado) > 0){
            $consulta = $objAccesoDatos->prepararConsulta("Update pedidos set estado = 'cobrado' where id = :id");
            $consulta->bindValue(':id', $id);

            $consulta->execute();

            Mesa::CambiarEstado($listado[0]->id_mesa, "cerrada");

            Pedido::Facturar($listado[0]);

            return true;
        }

        return false;
    }

    public static function Facturar($pedido){

        $productosDelPedido = ProductoDelPedido::ObtenerProductosPorId($pedido->id);

        $precio = 0;

        foreach($productosDelPedido as $producto){
            $precio += Producto::ObtenerPrecio($producto->id_producto);
        }

        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO facturas (id_mesa, id_pedido, precio, fecha, hora) VALUES (:id_mesa, :id_pedido, :precio, :fecha, :hora)");
        $consulta->bindValue(':id_mesa', $pedido->id_mesa);
        $consulta->bindValue(':id_pedido', $pedido->id);
        $consulta->bindValue(':precio', $precio);
        $consulta->bindValue(':fecha', date('y-m-d'));
        $consulta->bindValue(':hora', date('H:i'));

        $consulta->execute();
    }

    public static function GuadarExperiencia($codigoPedido, $codigoMesa, $mesa, $restaurante, $mozo, $cocinero, $experiencia){
        
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO reseñas (codigoPedido, codigoMesa, calificacionMesa, calificacionRestaurante, calificacionMozo, calificacionCocinero, experiencia, promedio) 
                                                        VALUES (:codigoPedido, :codigoMesa, :mesa, :restaurante, :mozo, :cocinero, :experiencia, :promedio)");
        $consulta->bindValue(':codigoMesa', $codigoMesa);
        $consulta->bindValue(':codigoPedido', $codigoPedido);
        $consulta->bindValue(':mesa', $mesa);
        $consulta->bindValue(':restaurante', $restaurante);
        $consulta->bindValue(':mozo', $mozo);
        $consulta->bindValue(':experiencia', $experiencia);
        $consulta->bindValue(':cocinero', $cocinero);

        $promedio = ($mesa + $restaurante + $mozo + $cocinero) / 4;

        $consulta->bindValue(':promedio', $promedio);


        $consulta->execute();
    }

    public static function ObtenerMejoresComentarios($cantidad){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM reseñas  order by promedio DESC limit :cantidad");
        $consulta->bindValue(':cantidad', $cantidad);
        $consulta->execute();

        $listado = $consulta->fetchAll(PDO::FETCH_CLASS);

        return $listado;
    }
}

?>