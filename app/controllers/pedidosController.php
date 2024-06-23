<?php

//llamado a clase usaurio 
require_once './models/Pedidos.php';
require_once './models/Ped_Productos.php';
require_once './db/AccesoDatos.php';

//llamado a index 
require_once './interfaces/IApiUsable.php';

class pedidosController extends Pedidos implements IApiUsable
{

    public  function CargarUno($request, $response, $args)
    {
        //traer datos desde el compose
        $parametros = $request->getParsedBody();
        
        // Creamos el usuario
        $usr = new Pedidos();
        $usr->idUsuario = $parametros['idUsuario'];
        $usr->idMesa = $parametros['idMesa'];
        $usr->total = $parametros['total'];
        $usr->estado = $parametros['estado'];
        $usr->foto = $parametros['foto'];
        $usr->puntuacion = $parametros['puntuacion'];
        $usr->descripcionPuntuacion = $parametros['descripcionPuntuacion'];
        
        //llamado a funcion 
        $usr->crearPedido();

        //traigo el objeto acceso de datos
        $ObjetoAccD = AccesoDatos::obtenerInstancia() ;
        //obtengo el ultimo id utilizando la funcion que esta dentro de mi onjeto AccesoDatos
        $ultimoID = $ObjetoAccD  ->obtenerUltimoId();
        //Obtengo por parametros el IdArticulo
        $articulos= $parametros['idArticulo'];
        //Transformo lo enviado por parametro a un Json 
        $array = json_decode($articulos, true);
        //verifico que sea un array
        if (is_array($array)) {
            //recorro el array y voy cargando los productos a mi tabla Ped_productos
            foreach ( $array as $art){
                $ped = new Ped_Productos();
                $ped->id_pedido = $ultimoID;
                $ped->id_articulos =$art;
                $ped->estado = 'PREPARACION';
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

    public function TraerPedidosPendientesID($request, $response, $args)
    {
        //traer datos desde el compose
        $params = $request->getParsedBody();
        $articulo = $params["ID_articulo"];
        $pedido = $params["ID_pedido"];
        $lista = Ped_Productos::obtePedPenID($pedido,$articulo);
        $payload = json_encode(array("listaPedidoPendientes" => $lista));

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


    public  function FinalizarEstadoProducto($request, $response, $args)
    {
        //traer datos desde el compose
        $parametros = $request->getParsedBody();
        
        $pedido = new Ped_Productos();
        $pedido->id_pedido =  $parametros['ID_pedido'];
        $pedido->id_articulos =  $parametros['ID_articulo'];
        $pedido ->ModificarEstado();
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
            $pedido ->id  = $pedidoF-> id_pedido=  $parametros['ID_F_pedido'];
            $pedido ->ModificarEstado();
            $payload = json_encode(array("mensaje" => "El estado se modifico con exito"));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }else{
            $payload = json_encode(array("mensaje" => "Hay un articulo pendiente"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}