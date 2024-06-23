<?php

class Pedidos
{
    public $id;
    public $idUsuario;
    public $idMesa;
    public $total;
    public $estado;
    public $foto;
    public $puntuacion;
    public $descripcionPuntuacion;

    public function crearPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (idUsuario , idMesa,total,estado,foto,puntuacion,descripcionPuntuacion) VALUES (:idUsuario,:idMesa,:total,:estado,:foto,:puntuacion,:descripcionPuntuacion)");
        $consulta->bindValue(':idUsuario', $this->idUsuario, PDO::PARAM_STR);
        $consulta->bindValue(':idMesa', $this->idMesa, PDO::PARAM_STR);
        $consulta->bindValue(':total', $this->total, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':foto', $this->foto, PDO::PARAM_STR);
        $consulta->bindValue(':puntuacion', $this->puntuacion, PDO::PARAM_STR);
        $consulta->bindValue(':descripcionPuntuacion', $this->descripcionPuntuacion, PDO::PARAM_STR);
        $consulta->execute();
        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id,idUsuario,idMesa,total,estado,foto,puntuacion,descripcionPuntuacion FROM pedidos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS,'pedidos');
    }


    public function ModificarEstado()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedidos set estado = :estado where id = :id");
        $consulta->bindValue(':id', $this->id, PDO::PARAM_STR);
        $consulta->bindValue(':estado', 'FINALIZADO', PDO::PARAM_STR);
        $consulta->execute();
        return $objAccesoDatos;
    }
    public static function obtenerPedido_ID($pedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos where id = :id");
        $consulta->bindValue(':id', $pedido, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS,'pedidos');
    }





}