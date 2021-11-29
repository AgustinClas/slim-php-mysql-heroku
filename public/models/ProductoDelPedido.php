<?php
include_once "db/AccesoDatos.php";
require_once './models/Producto.php';
require_once './models/Pedido.php';


class ProductoDelPedido{

    public $id;
    public $id_pedido;
    public $id_producto;
    public $cantidad;
    public $estado;
    public $sector;


    public static function AgregarProductosAlPedido($id, $pedido)
    {
        $arrayPedidos = explode(",", $pedido);

        foreach($arrayPedidos as $array){
            $auxArray = explode(":",$array);

            $id_producto = Producto::VerificarProducto($auxArray[1]);

            if($id_producto == -1)
            return false;
        }

        for($i=0;$i<count($arrayPedidos);$i++){
            $auxArray = explode(":",$arrayPedidos[$i]);
             
            $producto = new ProductoDelPedido();
            $producto->id_pedido = $id;
            $producto->cantidad = $auxArray[0];
        
            $id_producto = Producto::VerificarProducto($auxArray[1]);
            $producto->id_producto = $id_producto;
            $producto->sector = Producto::BuscarSectorPorId($id_producto);

            Producto::SumarCantidadVendidos($id_producto, $auxArray[0]);
                
            $producto->AgregarProductoAlPedido();
            
        }

        return true;
    }

    public function AgregarProductoAlPedido(){

        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO productos_del_pedido (id_pedido, id_producto, estado, cantidad, sector) VALUES (:id_pedido, :id_producto, 'pedido pendiente', :cantidad, :sector)");
        $consulta->bindValue(':id_pedido', $this->id_pedido, PDO::PARAM_STR);
        $consulta->bindValue(':id_producto', $this->id_producto);
        $consulta->bindValue(':cantidad', $this->cantidad);
        $consulta->bindValue(':sector', $this->sector);
        $consulta->execute();

        $id =  $objAccesoDatos->obtenerUltimoId();
    }

    public static function ObtenerProductosPorId($id_pedido){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, id_producto, id_pedido, estado, cantidad, sector FROM productos_del_pedido where id_pedido = :id_pedido");
        $consulta->bindValue(':id_pedido', $id_pedido, PDO::PARAM_STR);
        $consulta->execute();

        $listado = $consulta->fetchAll(PDO::FETCH_CLASS, 'ProductoDelPedido');

        return $listado;
    }

    public static function TraerPendientesPorSector($sector){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, id_producto, id_pedido, estado, cantidad, sector FROM productos_del_pedido where sector = :sector and estado = 'pedido pendiente'");
        $consulta->bindValue(':sector', $sector, PDO::PARAM_STR);
        $consulta->execute();

        $listado = $consulta->fetchAll(PDO::FETCH_CLASS, 'ProductoDelPedido');

        return $listado;
    }

    public static function TraerEnPreparacionPorSector($sector){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, id_producto, id_pedido, estado, cantidad, sector FROM productos_del_pedido where sector = :sector and estado = 'en preparacion'");
        $consulta->bindValue(':sector', $sector, PDO::PARAM_STR);
        $consulta->execute();

        $listado = $consulta->fetchAll(PDO::FETCH_CLASS, 'ProductoDelPedido');

        return $listado;
    }

    public static function PonerEnPreparacion($idPedido, $idProducto, $sector){
        
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, id_producto, id_pedido, estado, cantidad, sector FROM productos_del_pedido where id_pedido = :idPedido and id = :id and sector = :sector");
        $consulta->bindValue(':idPedido', $idPedido, PDO::PARAM_STR);
        $consulta->bindValue(':id', $idProducto, PDO::PARAM_STR);
        $consulta->bindValue(':sector', $sector, PDO::PARAM_STR);

        $consulta->execute();

        $listado = $consulta->fetchAll(PDO::FETCH_CLASS, 'ProductoDelPedido');

        if(count($listado) > 0){
            $consulta = $objAccesoDatos->prepararConsulta("Update productos_del_pedido set estado = 'en preparacion' where id = :id");
            $consulta->bindValue(':id', $idProducto, PDO::PARAM_STR);

            $consulta->execute();

            return true;
        }

        return false;

    }

    public static function Servir($idProducto, $sector){
        
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, id_producto, id_pedido, estado, cantidad, sector FROM productos_del_pedido where  id = :id and sector = :sector and estado = 'en preparacion'");
        $consulta->bindValue(':id', $idProducto, PDO::PARAM_STR);
        $consulta->bindValue(':sector', $sector, PDO::PARAM_STR);

        $consulta->execute();

        $listado = $consulta->fetchAll(PDO::FETCH_CLASS, 'ProductoDelPedido');

        if(count($listado) > 0){
            $consulta = $objAccesoDatos->prepararConsulta("Update productos_del_pedido set estado = 'listo para servir' where id = :id");
            $consulta->bindValue(':id', $idProducto, PDO::PARAM_STR);

            $consulta->execute();

            Pedido::VerificarEstado($listado[0]->id_pedido);

            

            return true;
        }

        return false;
    }

    public static function ActualizarProductosServidos($id){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("Update productos_del_pedido set estado = 'servido' where id_pedido = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);

        $consulta->execute();

    }


}

?>