<?php

class Pedidos
{
    public $id;
    public $idUsuario;
    public $idArticulo;
    public $idMesa;
    public $total;
    public $estado;
    public $foto;
    public $puntuacion;
    public $descripcionPuntuacion;

    public function crearPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (idUsuario , idArticulo, idMesa,total,estado,foto,puntuacion,descripcionPuntuacion) VALUES (:idUsuario,:idArticulo,:idMesa,:total,:estado,:foto,:puntuacion,:descripcionPuntuacion)");
        $consulta->bindValue(':idUsuario', $this->idUsuario, PDO::PARAM_STR);
        $consulta->bindValue(':idArticulo', $this->idArticulo, PDO::PARAM_STR);
        $consulta->bindValue(':idMesa', $this->idArticulo, PDO::PARAM_STR);
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
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id,idUsuario, idArticulo,idMesa,total,estado,foto,puntuacion,descripcionPuntuacion FROM pedidos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS,'pedidos');
    }
    


}