<?php

//llamado a clase usaurio 
require_once './models/Pedidos.php';
require_once './models/Ped_Productos.php';
require_once './models/Mesas.php';
require_once './db/AccesoDatos.php';
require_once './utils/Archivos.php';
//llamado a index 
require_once './interfaces/IApiUsable.php';

class pedidosController extends Pedidos implements IApiUsable
{

    public  function CargarUno($request, $response, $args)
    {
        //traer datos desde el compose
        $parametros = $request->getParsedBody();
        //Obtengo por parametros el IdArticulo
        $articulos= $parametros['ID_F_pedido'];
        //Transformo lo enviado por parametro a un Json 
        $array = json_decode($articulos, true);

        $precioTotal = 0;
        foreach($array as $producto){
            $precioProducto = Productos::obtenerPrecioPorId($producto);
            $precioTotal += $precioProducto->precio;
        }
        // Creamos el usuario
        $usr = new Pedidos();
        $usr->idUsuario = $parametros['idUsuario'];
        $usr->idMesa = $parametros['idMesa'];
        $usr->total = $precioTotal;
        
        //llamado a funcion 
        $usr->crearPedido();

        //traigo el objeto acceso de datos
        $ObjetoAccD = AccesoDatos::obtenerInstancia() ;
        //obtengo el ultimo id utilizando la funcion que esta dentro de mi onjeto AccesoDatos
        $ultimoID = $ObjetoAccD  ->obtenerUltimoId();
        //verifico que sea un array
        if (is_array($array)) {
            //recorro el array y voy cargando los productos a mi tabla Ped_productos
            foreach ( $array as $art){
                $ped = new Ped_Productos();
                $ped->id_pedido = $ultimoID;
                $ped->id_articulos =$art;
                $ped->estado = 'PENDIENTE';
                $ped->crearProductos();
            }
        }
        //revisar
        $payload = json_encode(array("mensaje" => "Pedido creado con exito"));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Pedidos::obtenerTodos();
        $payload = json_encode(array("listaPedido" => $lista));

        $response->getBody()->write($payload);
        return $response ->withHeader('Content-Type', 'application/json');
    }

    public function TraerPedidosPendientesMozo($request, $response, $args)
    {
        //traer datos desde el compose
        $lista = Ped_Productos::obtePedPenID();

        if(!empty($lista)){
            $payload = json_encode(array("listaPedidoPendientes" => $lista));

        }else{
            $payload = json_encode(array("mensaje" => "No hay pedidos pendientes"));
        }

        $response->getBody()->write($payload);
        return $response ->withHeader('Content-Type', 'application/json');
    }


//bartender 
    public function TraerPedidosPendientesBartender($request, $response, $args)
    {
        $lista = Ped_Productos::obtenerPedidosPendientesBartender();
        $payload = json_encode(array("listaPedidoPendientes" => $lista));

        $response->getBody()->write($payload);
        return $response ->withHeader('Content-Type', 'application/json');
    }


    
//cocineros
    public function TraerPedidosPendientesCocineros($request, $response, $args)
    {
        $lista = Ped_Productos::obtenerPedidosPendientesCocineros();
        $payload = json_encode(array("listaPedidoPendientes" => $lista));

        $response->getBody()->write($payload);
        return $response ->withHeader('Content-Type', 'application/json');
    }

//cerveceros
public function TraerPedidosPendientesCerveceros($request, $response, $args)
{
    $lista = Ped_Productos::obtenerPedidosPendientesCerveceros();
    $payload = json_encode(array("listaPedidoPendientes" => $lista));

    $response->getBody()->write($payload);
    return $response ->withHeader('Content-Type', 'application/json');
}


    public  function CambiarEstadoProductoPreparacion($request, $response, $args)
    {
        //traer datos desde el compose
        $parametros = $request->getParsedBody();
        
        $pedido = new Ped_Productos();
        $pedido->id_pedido =  $parametros['ID_F_pedido'];
        $pedido->id_articulos =  $parametros['ID_articulo'];
        $pedido->tiempoPedido =  $parametros['tiempoPreparacion'];
        $pedido ->ModificarEstado("PREPARACION");
        $pedido ->ModificarTiempoPreparacion();
        //revisar
        $payload = json_encode(array("mensaje" => "El estado se modifico con exito"));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public  function CambiarEstadoProductoListo($request, $response, $args)
    {
        //traer datos desde el compose
        $parametros = $request->getParsedBody();
        
        $pedido = new Ped_Productos();
        $pedido->id_pedido =  $parametros['ID_F_pedido'];
        $pedido->id_articulos =  $parametros['ID_articulo'];
        $pedido->tiempoPedido =  $parametros['tiempoPreparacion'];
        $pedido ->ModificarEstado("LISTO");
        $pedido ->ModificarTiempoPreparacion();
        //revisar
        $payload = json_encode(array("mensaje" => "El estado se modifico con exito"));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public  function FinalizarPedido($request, $response, $args)
    {
        $parametros = $request->getParsedBody();   
        $pedidoF = new Ped_Productos();
        $pedidoF-> id_pedido=  $parametros['ID_F_pedido'];
        $consulta =  $pedidoF ->verificarPedido($pedidoF-> id_pedido);

        if(empty($consulta)){
            $pedido = new Pedidos();
            $pedido ->id  = $pedidoF-> id_pedido;
            $pedidoEncontrado  =  $pedido ->obtenerPedido_ID($pedido->id);
            $pedido ->ModificarEstado("Entregado");

            //mesa
            $mesa = new Mesas ();
            $mesa ->id = $pedidoEncontrado[0] ->idMesa;
            $mesa->modificarEstado("comiendo");

            $payload = json_encode(array("mensaje" => "El estado se modifico con exito"));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }else{
            $payload = json_encode(array("mensaje" => "Hay un articulo pendiente"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function CargarFoto( $request, $response,$args)
	{
		$params = $request->getParsedBody();
        $idPedido = $params['ID_F_pedido'];
		$pedido = Pedidos::obtenerPedido_ID($idPedido)[0];
		$uriFoto = Archivos::GuardarArchivoPeticion("./FotosMesas/", "Mesa{$pedido->idMesa}_{$idPedido}", 'foto', '.jpg');
		Pedidos::agregarFoto($idPedido, $uriFoto);
		$payload = json_encode(array("msg" => "Foto agregada con exito"));
		$response->getBody()->write($payload);
		return $response->withHeader('Content-Type', 'application/json');
	}

    //Clientes 
    public static function verPedidoCliente( $request, $response,$args){
        $params = $request->getParsedBody();
        $idPedido = $params['ID_F_pedido'];
        $idMesa = $params ['idMesa'];
        $lista = Ped_Productos::obtenerPedidoCliente($idPedido , $idMesa);
        $payload = json_encode(array("Tu pedido " => $lista));
		$response->getBody()->write($payload);
		return $response->withHeader('Content-Type', 'application/json');

    }

    //Socios 
    public static function verPedidosFinalizados( $request, $response,$args){
        $lista = Ped_Productos::obtenerPedidosDetalle();
        $payload = json_encode(array("listado Pedidos " => $lista));
		$response->getBody()->write($payload);
		return $response->withHeader('Content-Type', 'application/json');

    }

    //Mesas
    public static function cambiarEstadoMesa( $request, $response,$args){
        $params = $request->getParsedBody();
        $idMesa = $params ['mesa'];
        $MesaEncontrada = new Mesas();
        $MesaEncontrada ->obtenerUno($idMesa);

        if(!empty($MesaEncontrada)){
            
            $payload = json_encode(array("mensaje" => "El estado se modifico con exito"));
        }else{
            $payload = json_encode(array("mensaje" => "La mesa no existe"));
        }
        
        $response->getBody()->write($payload);
		return $response->withHeader('Content-Type', 'application/json');
    }


    public function pedirCuenta($request, $response, $args)
    {
      $parametros = $request->getParsedBody();

      $pedido = new Pedidos();
      $pedido->id =  $parametros['ID_F_pedido'];

      $lista = Pedidos::obtenerPedido_ID($pedido->id);

      if(!empty($lista)){

        $pedido ->modificarEstado("Pagando");

        $mesa = new Mesas();
        $mesa ->id = $lista[0]->idMesa;
        $mesa ->modificarEstado("Pagando");

        $payload = json_encode(array("mensaje" => "Mesa pagando"));
      }else{
        $payload = json_encode(array("mensaje" => "No se pudo modificar el estado de la mesa"));
      }

      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }

    public function pagarCuenta($request, $response, $args)
    {
      $parametros = $request->getParsedBody();

      $pedido = new Pedidos();
      $pedido->id =  $parametros['ID_F_pedido'];

      $lista = Pedidos::obtenerPedido_ID($pedido->id);

      if(!empty($lista)){


        $pedido ->modificarEstado("pagado/finalizado");

        $mesa = new Mesas();
        $mesa ->id = $lista[0]->idMesa;
        $mesa ->modificarEstado("DISPONIBLE");

        $payload = json_encode(array("mensaje" => "Mesa disponible"));
      }else{
        $payload = json_encode(array("mensaje" => "No se pudo modificar el estado de la mesa"));
      }

      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }


    public function TraerMesaMasUsada($request, $response, $args){
        $lista = Pedidos::obtenerMesaMasUsada();
        $payload = json_encode(array("Mesa mas Usada" => $lista));
        $response->getBody()->write($payload);
        return $response ->withHeader('Content-Type', 'application/json');
    }

    public function TraerMejoresComentarios($request, $response, $args){
        $lista = Pedidos::traerMejComentarios();
        $payload = json_encode(array("MejoresComentarios" => $lista));
        $response->getBody()->write($payload);
        return $response ->withHeader('Content-Type', 'application/json');
    }

}