<?php


class Ped_Productos{

    public $id_pedido;
    public $id_articulos;
    public $estado;
    public $tiempoPedido;

    public function crearProductos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO Ped_Productos (id_pedido,id_articulos,estado,tiempoPedido) VALUES (:id_pedido ,:id_articulos,:estado,:tiempoPedido)");
        $consulta->bindValue(':id_pedido', $this->id_pedido, PDO::PARAM_STR);
        $consulta->bindValue(':id_articulos', $this->id_articulos, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':tiempoPedido', "0", PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos;
    }


    public static function obtenerPedidosPendientesBartender()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_pedido ,id_articulos,estado,tiempoPedido FROM Ped_Productos p inner join productos pr on p.id_articulos = pr.id where pr.rol like '%Bart%' and p.estado != 'finalizado';");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS,'Ped_Productos');
    }


    public static function obtenerPedidosPendientesCocineros()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_pedido ,id_articulos,estado,tiempoPedido  FROM Ped_Productos p inner join productos pr on p.id_articulos = pr.id where pr.rol like '%Cocin%' and p.estado != 'finalizado';");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS,'Ped_Productos');
    }

    public static function obtenerPedidosPendientesCerveceros()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_pedido ,id_articulos,estado,tiempoPedido  FROM Ped_Productos p inner join productos pr on p.id_articulos = pr.id where pr.rol like '%Cerv%' and p.estado != 'finalizado';");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS,'Ped_Productos');
    }



    public function ModificarEstado($estado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE Ped_Productos set estado = :estado where id_pedido = :id_pedido and id_articulos = :id_articulos");
        $consulta->bindValue(':id_pedido', $this->id_pedido, PDO::PARAM_STR);
        $consulta->bindValue(':id_articulos', $this->id_articulos, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->execute();
        return $objAccesoDatos;
    }


    public function ModificarTiempoPreparacion()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE ped_productos set tiempoPedido = :tiempo where id_pedido = :id  and id_articulos = :id_articulos");
        $consulta->bindValue(':id', $this->id_pedido, PDO::PARAM_STR);
        $consulta->bindValue(':id_articulos', $this->id_articulos, PDO::PARAM_STR);
        $consulta->bindValue(':tiempo', $this->tiempoPedido, PDO::PARAM_STR);
        $consulta->execute();
        return $objAccesoDatos;
    }

    public static function obtePedPenID()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pd.id_pedido,ped.estado as estadoPedido ,p.nombre,pd.estado as estadoProductos,ped.idMesa FROM Ped_Productos pd inner join productos p on p.id = pd.id_articulos INNER join pedidos ped on ped.id = pd.id_pedido where ped.estado not in ('pagado/finalizado','Entregado','Pagando') GROUP by pd.id_pedido,ped.estado , p.nombre");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS);
    }


    public static function obtePedidos($pedido,$producto)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM Ped_Productos where id_Pedido = :idPedido and id_articulos = :idProducto ");
        $consulta->bindValue(':idPedido', $pedido, PDO::PARAM_STR);
        $consulta->bindValue(':idProducto', $producto, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS,'Ped_Productos');
    }

    public static function verificarPedido($pedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM Ped_Productos where id_pedido = :id_pedido and estado not in ('finalizado','LISTO')");
        $consulta->bindValue(':id_pedido', $pedido, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS,'Ped_Productos');
    }

    public static function obtenerPedidoCliente($pedido,$mesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pd.id_pedido,pd.id_articulos,p.nombre,pd.estado,pd.tiempoPedido ,ped.idMesa FROM Ped_Productos pd inner join productos p on p.id = pd.id_articulos INNER join pedidos ped on ped.id = pd.id_pedido where id_pedido = :id and ped.idMesa = :idmesa");
        $consulta->bindValue(':id', $pedido, PDO::PARAM_STR);
        $consulta->bindValue(':idmesa', $mesa, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS);
    }


    public static function obtenerPedidosDetalle()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pd.id,pd.idMesa,pd.total,pd.estado,sum(ped.tiempoPedido)as tiempo FROM Pedidos pd INNER join ped_productos ped on pd.id = ped.id_pedido GROUP by pd.id,pd.idMesa,pd.total,pd.estado");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS);
    }


    public static function TraerProdPorPedido($idPedido)
	{
		$objAccesoDatos = AccesoDatos::ObtenerInstancia();
		$req = $objAccesoDatos->PrepararConsulta("SELECT id_articulos from Ped_Productos WHERE id_Pedido=:idPedido");
		$req->bindValue(':idPedido', $idPedido, PDO::PARAM_STR);
		$req->execute();

		return $req->fetchAll(PDO::FETCH_COLUMN);
	}
}