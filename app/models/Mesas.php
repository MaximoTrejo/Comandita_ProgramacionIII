<?php

class Mesas
{
    public $id;
    public $estado;
    public function crearMesa()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mesas (estado) VALUES (:estado)");
        $consulta->bindValue(':estado', "DISPONIBLE", PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, estado FROM mesas");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'mesas');
    }
    

    public static function CsvToMesa($rutaArchivo)
    {
        $refArchivo = fopen($rutaArchivo, "r");
        $arrayAtr = array();
        $datos = array();

        if ($refArchivo) {

            while (!feof($refArchivo)) {

                $arrayAtr = fgetcsv($refArchivo);

                if (!empty($arrayAtr)) {

                    $model = new Mesas();
                    $model->id = $arrayAtr[0];
                    $model->estado = $arrayAtr[1];
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

        $archivo = Archivos::GuardarArchivoPeticion($ruta, "MesaSubidas", 'archivo', '.csv');
        
        if ($archivo != "N/A") {
            
            $array = self::CsvToMesa($archivo);

            if(!empty($array)){

                foreach ($array as $usuario) {
                    $usuario->crearProductos();
                }
                return true;
            }
        }
        
        return false;
    }


    public static function obtenerUno($mesaID){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM mesas where  id = :id");
        $consulta->bindValue(':id', $mesaID, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'mesas');

    }


    public function modificarEstado($estado){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE mesas set estado = :estado where  id = :id");
        $consulta->bindValue(':id', $this ->id, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'mesas');

    }



}