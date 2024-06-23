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


    public static function obtenerPedidosPendientesBartender()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_pedido ,id_articulos,estado FROM Ped_Productos p inner join productos pr on p.id_articulos = pr.id where pr.rol like '%Bart%' and p.estado != 'finalizado';");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS,'Ped_Productos');
    }


    public static function obtenerPedidosPendientesCocineros()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_pedido ,id_articulos,estado FROM Ped_Productos p inner join productos pr on p.id_articulos = pr.id where pr.rol like '%Cocin%' and p.estado != 'finalizado';");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS,'Ped_Productos');
    }

    public static function obtenerPedidosPendientesCerveceros()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_pedido ,id_articulos,estado FROM Ped_Productos p inner join productos pr on p.id_articulos = pr.id where pr.rol like '%Cerv%' and p.estado != 'finalizado';");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS,'Ped_Productos');
    }



    public function ModificarEstado()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE Ped_Productos set estado = :estado where id_pedido = :id_pedido and id_articulos = :id_articulos");
        $consulta->bindValue(':id_pedido', $this->id_pedido, PDO::PARAM_STR);
        $consulta->bindValue(':id_articulos', $this->id_articulos, PDO::PARAM_STR);
        $consulta->bindValue(':estado', 'FINALIZADO', PDO::PARAM_STR);
        $consulta->execute();
        return $objAccesoDatos;
    }
    public static function obtePedPenID($pedido,$articulo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_pedido,id_articulos,estado FROM Ped_Productos where id_pedido = :id_pedido and id_articulos = :id_articulos  and estado != 'finalizado'");
        $consulta->bindValue(':id_pedido', $pedido, PDO::PARAM_STR);
        $consulta->bindValue(':id_articulos', $articulo, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS,'Ped_Productos');
    }

    public static function verificarPedido($pedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM Ped_Productos where id_pedido = :id_pedido  and estado != 'finalizado'");
        $consulta->bindValue(':id_pedido', $pedido, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS,'Ped_Productos');
    }

}