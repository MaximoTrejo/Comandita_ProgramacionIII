<?php

require_once './utils/Archivos.php';

class Productos
{
    public $id;
    public $nombre;
    public $precio;
    public $rol;

    public function crearProductos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO productos (nombre,precio,rol) VALUES (:nombre , :precio,:rol)");
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_STR);
        $consulta->bindValue(':rol', $this->rol, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id,nombre,precio,rol FROM productos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'productos');
    }
    
    public static function CsvToProductos($rutaArchivo)
    {
        $refArchivo = fopen($rutaArchivo, "r");
        $arrayAtr = array();
        $datos = array();

        if ($refArchivo) {

            while (!feof($refArchivo)) {

                $arrayAtr = fgetcsv($refArchivo);

                if (!empty($arrayAtr)) {

                    $model = new Productos();
                    $model->id = $arrayAtr[0];
                    $model->nombre = $arrayAtr[1];
                    $model->precio = $arrayAtr[2];
                    $model ->rol = $arrayAtr[3];
                    array_push($datos, $model);
                }
            }
            fclose($refArchivo);
        }

        return $datos;
    }

    public static function SubirDatosCsv()
    {
        $ruta = BASE_PATH . '/ArchivosSubidos/';

        $archivo = Archivos::GuardarArchivoPeticion($ruta, "ProductosSubidos", 'archivo', '.csv');
        
        if ($archivo != "N/A") {
            
            $array = self::CsvToProductos($archivo);

            if(!empty($array)){

                foreach ($array as $usuario) {
                    $usuario->crearProductos();
                }
                return true;
            }
        }
        
        return false;
    }
    public static function obtenerPrecioPorId($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM Productos where id = :id");
        $consulta->bindValue(':id', $id , PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('productos');
    }

}