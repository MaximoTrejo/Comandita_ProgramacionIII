<?php

require_once './utils/pdf/fpdf.php';
require_once './models/Ped_Productos.php';
require_once './models/Productos.php';

class Pedidos
{
    public $id;
    public $idUsuario;
    public $idMesa;
    public $total;
    public $estado;
    public $foto;
    public $TiempoEstipulado;

    public function crearPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (idUsuario , idMesa,total,estado,foto,tiempoEstipulado) VALUES (:idUsuario,:idMesa,:total,:estado,:foto,:tiempo)");
        $consulta->bindValue(':idUsuario', $this->idUsuario, PDO::PARAM_STR);
        $consulta->bindValue(':idMesa', $this->idMesa, PDO::PARAM_STR);
        $consulta->bindValue(':total', $this->total, PDO::PARAM_STR);
        $consulta->bindValue(':estado', "PREPARACION", PDO::PARAM_STR);
        $consulta->bindValue(':foto',"N/A", PDO::PARAM_STR);
        $consulta->bindValue(':tiempo',"40", PDO::PARAM_STR);
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

        return $consulta->fetchAll(PDO::FETCH_CLASS);
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

    public static function obtenerPedidoTiempoNoEstipulado($id)
    {
        $objAccesoDatos = AccesoDatos::ObtenerInstancia();
        $req = $objAccesoDatos->PrepararConsulta("SELECT pd.id, pd.tiempoEstipulado, SUM(prd.tiempoPedido) AS tiempoTotalPedido FROM ped_productos prd INNER JOIN pedidos pd ON prd.id_pedido = pd.id WHERE prd.estado = 'LISTO' and pd.id = :id GROUP BY pd.id, pd.tiempoEstipulado HAVING SUM(prd.tiempoPedido) >= pd.tiempoEstipulado");
        $req->bindValue(':id', $id, PDO::PARAM_INT);
        $req->execute();
        return $req->fetchAll(PDO::FETCH_CLASS);
    }

    public static function obtenerTodosPedidoTiempoNoEstipulado()
    {
        $objAccesoDatos = AccesoDatos::ObtenerInstancia();
        $req = $objAccesoDatos->PrepararConsulta("SELECT pd.id, pd.tiempoEstipulado, SUM(prd.tiempoPedido) AS tiempoTotalPedido FROM ped_productos prd INNER JOIN pedidos pd ON prd.id_pedido = pd.id WHERE prd.estado = 'LISTO' GROUP BY pd.id, pd.tiempoEstipulado HAVING SUM(prd.tiempoPedido) >= pd.tiempoEstipulado");
        $req->execute();
        return $req->fetchAll(PDO::FETCH_CLASS);
    }



    public static function CrearPdf($idPedido , $idUsuario, $total)
	{
		$pdf = new FPDF();
		$pdf->AddPage();
		$pdf->SetFont('Courier', 'BU', 35);
		$pdf->Cell(187.5, 30, "PEDIDO", 1, 0, 'C');

		$pdf->Image('./LogoImg/logo.png', 11.25, null, 30, 30, 'png');

		$pdf->SetFont('Arial', '', 16);
		$pdf->Write(10, "Id del pedido: #{$idPedido}\n");
		$pdf->Write(10, "Cliente: {$idUsuario}\n");

		$pdf->SetFillColor(255, 239, 219);
		$pdf->Cell(15, 8, "ID", 1, 0, 'C', 1);
		$pdf->Cell(142.5, 8, "PRODUCTO", 1, 0, 'C', 1);
		$pdf->Cell(30, 8, "PRECIO", 1, 1, 'C', 1);

		$idProductos = Ped_Productos::TraerProdPorPedido($idPedido);

		foreach ($idProductos as $prodId) {
			$producto = Productos::TraerPorId($prodId)[0];
			$pdf->Cell(15, 8, "$producto->id", 1, 0, 'L', 1);
			$pdf->Cell(142.5, 8, "$producto->nombre", 1, 0, 'L', 1);
			$pdf->Cell(30, 8, "\$$producto->precio", 1, 1, 'R', 1);
		}

		$pdf->SetFont('Arial', 'B', 16);
		$pdf->Cell(157.5, 10, "TOTAL", 1, 0, 'L');
		$pdf->Cell(30, 10, "\${$total}", 1, 1, 'R');

		return $pdf;
	}

}