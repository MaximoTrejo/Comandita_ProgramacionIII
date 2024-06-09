<?php


class Ped_Productos{

    public $id_pedido;
    public $id_articulos;
    public $estado;

    public function crearProductos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO Ped_Productos (id_pedido,id_articulos,estado) VALUES (:id_pedido , :id_articulos,:estado)");
        $consulta->bindValue(':id_pedido', $this->id_pedido, PDO::PARAM_STR);
        $consulta->bindValue(':id_articulos', $this->id_articulos, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos;
    }





}