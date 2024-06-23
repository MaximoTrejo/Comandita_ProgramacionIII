<?php


class Archivos{
    public static function GuardarArchivoPeticion($directorio, $nuevoNombre, $archivoSubido, $extension)
    {
        if (isset($_FILES[$archivoSubido])){

            $tmpName = $_FILES[$archivoSubido]["tmp_name"];

            $destino = $directorio . $nuevoNombre . $extension;

            if(move_uploaded_file($tmpName, $destino)){

                return $destino;

            }else{
                return "N/A";
            }
        }
        return "N/A";
    }

}