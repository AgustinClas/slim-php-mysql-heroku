<?php
include_once "db/AccesoDatos.php";

class Producto{

    public $id;
    public $descripcion;
    public $sector;
    public $precio;
    public $cantidadVendidos;

    public function CrearProducto($id, $descripcion, $sector, $precio, $cantidadVendidos){

        $pedido = new Producto();
        $pedido->id = $id;
        $pedido->descripcion = $descripcion;
        $pedido->sector = $sector;
        $pedido->precio = $precio;
        $pedido->cantidadVendidos = $cantidadVendidos;

        return $pedido;
    }

    public function AgregarProducto()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO productos (descripcion, sector, precio, cantidad_vendidos) VALUES (:descripcion, :sector, :precio , :cantidadVendidos)");
        $consulta->bindValue(':descripcion', $this->descripcion, PDO::PARAM_STR);
        $consulta->bindValue(':sector', $this->sector);
        $consulta->bindValue(':precio', $this->precio);
        $consulta->bindValue(':cantidadVendidos', 0);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, descripcion, sector, precio, cantidad_vendidos as cantidadVendidos FROM productos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
    }

    public static function EsNuevo($descripcion){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, descripcion, sector, precio, cantidad_vendidos as cantidadVendidos FROM productos WHERE descripcion = :descripcion");
        $consulta->bindValue(':descripcion', $descripcion);
        $consulta->execute();

        $listado = $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');

        if(count($listado) > 0) return false;

        return true;
    }

    public static function VerificarProducto($descripcion){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, descripcion, sector, precio, cantidad_vendidos as cantidadVendidos FROM productos WHERE descripcion = :descripcion");
        $consulta->bindValue(':descripcion', $descripcion);
        $consulta->execute();

        $listado = $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');

        if(count($listado) > 0) return $listado[0]->id;

        return -1;
    }

    public static function BuscarSectorPorId($id){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, descripcion, sector, precio, cantidad_vendidos as cantidadVendidos FROM productos WHERE id = :id");
        $consulta->bindValue(':id', $id);
        $consulta->execute();

        $listado = $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');

        return $listado[0]->sector;
    }

    public static function ObtenerPrecio($id){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, descripcion, sector, precio, cantidad_vendidos as cantidadVendidos FROM productos WHERE id = :id");
        $consulta->bindValue(':id', $id);
        $consulta->execute();

        $listado = $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');

        return $listado[0]->precio;
    }

    public static function SumarCantidadVendidos($id, $cantidad){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("update productos set cantidad_vendidos = cantidad_vendidos + :cantidad where id = :id");
        $consulta->bindValue(':id', $id);
        $consulta->bindValue(':cantidad', $cantidad);
        $consulta->execute();
    }

    

}

?>