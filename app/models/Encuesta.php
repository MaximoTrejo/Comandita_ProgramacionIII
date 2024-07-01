<?php

require_once './db/AccesoDatos.php';

class Encuesta
{
    public $id;
    public $idMesa;
    public $idPedido;
    public $puntMesa;
    public $puntRestaurante;
    public $puntMozo;
    public $puntCocina;
    public $comentarios;

    public function CrearEncuesta()
    {
        $objAccesoDatos = AccesoDatos::ObtenerInstancia();

        $req = $objAccesoDatos->PrepararConsulta("INSERT INTO encuestas (idMesa, idPedido, puntMesa, puntRestaurante, puntMozo, puntCocina, comentarios) VALUES (:idMesa, :idPedido, :mesa, :restaurante, :mozo, :cocina, :comentarios)");
        $req->bindValue(':idMesa', $this->idMesa, PDO::PARAM_INT);
        $req->bindValue(':idPedido', $this->idPedido, PDO::PARAM_STR);
        $req->bindValue(':mesa', $this->puntMesa, PDO::PARAM_INT);
        $req->bindValue(':restaurante', $this->puntRestaurante, PDO::PARAM_INT);
        $req->bindValue(':mozo', $this->puntMozo, PDO::PARAM_INT);
        $req->bindValue(':cocina', $this->puntCocina, PDO::PARAM_INT);
        $req->bindValue(':comentarios', $this->comentarios, PDO::PARAM_STR);

        $req->execute();

        return $objAccesoDatos->ObtenerUltimoId();
    }

    public static function ObtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::ObtenerInstancia();
        $req = $objAccesoDatos->PrepararConsulta("SELECT * FROM encuestas");
        $req->execute();
        return $req->fetchAll(PDO::FETCH_CLASS, 'Encuesta');
    }
}