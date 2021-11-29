<?php
include_once "db/AccesoDatos.php";

class Usuario
{
    public $id;
    public $nombre;
    public $perfil;
    public $numeroPedido;
    public $estado;
    public $fechaAlta;
    public $horaAlta;
    public $cantidadOperaciones;

    public function CrearUsuario($id, $nombre, $perfil, $numeroPedido, $estado, $fechaAlta){

        $usr = new Usuario();
        $usr->id = $id;
        $usr->nombre = $nombre;
        $usr->perfil = $perfil;
        $usr->numeroPedido = $numeroPedido;
        $usr->estado = $estado;
        $usr->fechaAlta = $fechaAlta;

        return $usr;
    }

    public function AgregarUsuario()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (nombre, perfil, estado, fecha_alta, hora_alta, cantidad_operaciones) VALUES (:nombre, :perfil, 'activo' , :fechaAlta, :horaAlta, '0')");
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':perfil', $this->perfil);
        $consulta->bindValue(':fechaAlta', date('y-m-d'));
        $consulta->bindValue(':horaAlta', date('H:i'));
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, perfil, numero_pedido as numeroPedido, estado, fecha_alta as fechaAlta, hora_alta as horaAlta, cantidad_operaciones as cantidadOperaciones FROM usuarios");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }

    public static function obtenerUsuario($usuario)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, clave FROM usuarios WHERE usuario = :usuario");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }

    public static function modificarUsuario($id, $nombre, $clave)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET usuario = :usuario, clave = :clave WHERE id = :id");
        $consulta->bindValue(':usuario', $nombre, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $clave, PDO::PARAM_STR);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
    } 

    public static function borrarUsuario($id)
    {
        var_dump($id);
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET fechaBaja = :fechaBaja WHERE id = :id");
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->execute();
    }

    public function ValidarDatos(){
        
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, estado, perfil FROM usuarios where nombre = :nombre");
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->execute();

        $usuario = $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');

        if(count($usuario) > 0 && $usuario[0]->perfil == $this->perfil && $usuario[0]->estado == "activo")
            return true;

        return false;
    }
                 
}