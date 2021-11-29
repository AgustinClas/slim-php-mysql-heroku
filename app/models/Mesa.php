<?php
include_once "db/AccesoDatos.php";

class Mesa{

    public $id;
    public $codigo;
    public $estado;
    public $cantidadUsos;

    public function AgregarMesa()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mesas (codigo, estado, cantidad_usos) VALUES (:codigo, 'vacia', :cantidadUsos)");
        $consulta->bindValue(':codigo', $this->codigo, PDO::PARAM_STR);
        $consulta->bindValue(':cantidadUsos', 0);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigo, estado, cantidad_usos as cantidadUsos FROM mesas");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }

    public static function VerificarCodigo($codigo, $id){
        
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigo, estado, cantidad_usos as cantidadUsos FROM mesas where codigo = :codigo and id=:id");
        $consulta->bindValue(':codigo', $codigo);
        $consulta->bindValue(':id', $id);

        $consulta->execute();

        $listado = $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');

        if(count($listado) > 0) return true;

        return false;
    }

    public static function VerificarId($id){
        
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigo, estado, cantidad_usos as cantidadUsos FROM mesas id = :id");
        $consulta->bindValue(':id', $id);

        $consulta->execute();

        $listado = $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');

        if(count($listado) > 0) return true;

        return false;
    }

    public static function UtilizarMesa($id){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigo, estado, cantidad_usos as cantidadUsos FROM mesas where id=:id and estado = 'cerrada'");
        $consulta->bindValue(':id', $id);

        $consulta->execute();

        $listado = $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');

        if(count($listado) > 0){
            $consulta = $objAccesoDatos->prepararConsulta("Update mesas set estado = 'cliente esperando pedido', cantidad_usos = :usos where id = :id");
            $cantUsos = $listado[0]->cantidadUsos + 1;
            $consulta->bindValue(':usos', $cantUsos);
            $consulta->bindValue(':id', $id);
            $consulta->execute();
            var_dump($id);

            
            return true;
        }
        return false;
    }

    public static function CambiarEstado($id, $estado){

        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("Update mesas set estado = :estado where id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);


        $consulta->execute();

    }
}

?>