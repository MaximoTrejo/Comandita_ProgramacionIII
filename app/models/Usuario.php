<?php

require_once './utils/Archivos.php';

class Usuario
{
    public $id;
    public $usuario;
    public $clave;
    public $rol;

    public function crearUsuario()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (usuario, clave , rol) VALUES (:usuario, :clave , :rol)");
        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $claveHash);
        $consulta->bindValue(':rol', $this->rol);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, clave,rol FROM usuarios");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }


    public static function TraerPorNombreClave($usuario, $clave)
    {
        $objAccesoDatos = AccesoDatos::ObtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT *  FROM usuarios where usuario = :usuario and clave = :clave");
        $consulta->bindParam(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->bindParam(':clave', $clave, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }

    public static function TraerPorId($id)
    {
        $objAccesoDatos = AccesoDatos::ObtenerInstancia();
        $req = $objAccesoDatos->PrepararConsulta("SELECT * FROM usuarios WHERE id=:id");
        $req->bindValue(':id', $id, PDO::PARAM_INT);
        $req->execute();
        return $req->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }


    public static function CsvToUsuarios($rutaArchivo)
    {
        $refArchivo = fopen($rutaArchivo, "r");
        $arrayAtr = array();
        $datos = array();

        if ($refArchivo) {

            while (!feof($refArchivo)) {

                $arrayAtr = fgetcsv($refArchivo);

                if (!empty($arrayAtr)) {

                    $usuario = new Usuario();
                    $usuario->id = $arrayAtr[0];
                    $usuario->usuario = $arrayAtr[1];
                    $usuario->clave = $arrayAtr[2];
                    $usuario ->rol = $arrayAtr[3];
                    array_push($datos, $usuario);
                }
            }
            fclose($refArchivo);
        }

        return $datos;
    }

    public static function SubirDatosCsv()
    {
        $ruta = BASE_PATH . '/ArchivosSubidos/';
        $archivo = Archivos::GuardarArchivoPeticion($ruta, "UsuariosSubidos", 'archivo', '.csv');
        
        if ($archivo != "N/A") {
            
            $array = self::CsvToUsuarios($archivo);

            if(!empty($array)){

                foreach ($array as $usuario) {
    
                    $usuario->crearUsuario();
        
                }
                return true;
            }
        }
        
        return false;
    }
}
