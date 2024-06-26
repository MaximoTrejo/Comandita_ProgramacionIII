<?php

class Pedidos
{
    public $id;
    public $idUsuario;
    public $idMesa;
    public $total;
    public $estado;
    public $foto;

    public function crearPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (idUsuario , idMesa,total,estado,foto) VALUES (:idUsuario,:idMesa,:total,:estado,:foto)");
        $consulta->bindValue(':idUsuario', $this->idUsuario, PDO::PARAM_STR);
        $consulta->bindValue(':idMesa', $this->idMesa, PDO::PARAM_STR);
        $consulta->bindValue(':total', $this->total, PDO::PARAM_STR);
        $consulta->bindValue(':estado', "PREPARACION", PDO::PARAM_STR);
        $consulta->bindValue(':foto',"N/A", PDO::PARAM_STR);
        $consulta->execute();
        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id,idUsuario,idMesa,total,estado,foto FROM pedidos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS,'pedidos');
    }


    public function ModificarEstado($estado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedidos set estado = :estado where id = :id");
        $consulta->bindValue(':id', $this->id, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
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

    public static function agregarFoto($id, $uri)
	{
		$objAccesoDatos = AccesoDatos::ObtenerInstancia();
		$req = $objAccesoDatos->PrepararConsulta("UPDATE pedidos SET foto=:localizacion WHERE id=:id");
		$req->bindValue(':id', $id, PDO::PARAM_INT);
		$req->bindValue(':localizacion', $uri, PDO::PARAM_STR);
		$req->execute();

		return $objAccesoDatos->ObtenerUltimoId();
	}
    public static function obtenerMesaMasUsada()
	{
		$objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT idMesa, COUNT(idMesa) as cantidadDeVecesQueSeUsoLaMesa FROM pedidos GROUP BY idMesa ORDER BY cantidadDeVecesQueSeUsoLaMesa DESC LIMIT 1;");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS);
	}

    public static function traerMejComentarios()
    {
        $objAccesoDatos = AccesoDatos::ObtenerInstancia();
        $req = $objAccesoDatos->PrepararConsulta("SELECT id, idPedido, SUM(puntMesa+puntRestaurante+puntMozo+puntCocina) as puntuacion ,comentarios FROM  encuestas GROUP BY id  ORDER BY 3 DESC LIMIT 3");
        $req->execute();
        return $req->fetchAll(PDO::FETCH_CLASS);
    }



}