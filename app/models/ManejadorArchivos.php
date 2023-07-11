<?php

class ManejadorArchivos{
    public function __construct(){

    }

    static public function SaveImage($directory, $fileName, $array_FILE)
    {
        try {
            if(!is_null($directory) && !is_null($fileName) && !is_null($array_FILE))
            {
                // Verifico que envie un file con KEY "image"
                if(is_null($array_FILE['image'])) {  
                    echo 'No se encontró archivo con KEY "image". Verifique!!'. PHP_EOL;
                    return false; 
                }

                // Si no existe directorio, lo creo.
                // !!!  Acordarse que solo crea asi './nuevaCarpeta' o asi nuevaCarpeta !!!
                if (!file_exists($directory)) { mkdir($directory, 0777, true); }

                // Seteo el nombre de la Imagen 
                $array_FILE['image']['name'] = $fileName;
                
                // Preparo path para alojar Imagen
                $destino = $directory . '/' . $array_FILE['image']['name'] . '.png';

                return move_uploaded_file($array_FILE['image']['tmp_name'], $destino);
            }
            else
            {
                echo '¡Ocurrió un error! fileName o array_FILE al menos uno es NULL. Verifique!'. PHP_EOL;
            }
        } catch (Exception $th) {
            echo '¡Ocurrió un error! ', $th->getMessage();
        }
    }

    /* Actualiza nombre de imagen en donde ya estaba alojado */
    static public function UpdateImage($directory, $fileNameOld, $fileNameNew)
    {
        if(!is_null($directory) && !is_null($fileNameOld) && !is_null($fileNameNew))
        {
            $destinoOld = $directory . '/' . $fileNameOld . '.png';
            $destinoNew = $directory . '/' . $fileNameNew . '.png';
            
            $r = rename($destinoOld, $destinoNew);
            if($r) { echo 'Se actualizo nombre de imagen de '. $destinoOld .' a '. $destinoNew . PHP_EOL; }
            return $r;
        }

        echo '¡Ocurrió un error en function UpdateImage! directory, fileNameOld o fileNameNew al menos uno es NULL. Verifique!!'. PHP_EOL;
        return false;
    }

    /* Mueve imagen al directorio nuevo pasado por parametro*/
    static public function MoveImage($directoryOld, $directoryNew, $fileName)
    {
        if(!is_null($directoryOld) && !is_null($directoryNew) && !is_null($fileName))
        {
            // Si no existe directorio, lo creo.
            // !!!  Acordarse que solo crea asi './nuevaCarpeta' o asi nuevaCarpeta !!!
            if (!file_exists($directoryNew)) { mkdir($directoryNew, 0777, true); }

            $destinoOld = $directoryOld . '/' . $fileName . '.png';
            $destinoNew = $directoryNew . '/' . $fileName . '.png';

            $r = rename($destinoOld, $destinoNew);
            if($r) { echo 'Se movió imagen de '. $destinoOld .' a '. $destinoNew . PHP_EOL; }
            return $r;
        }

        echo '¡Ocurrió un error en function MoveImage! directoryOld, directoryNew, fileName al menos uno es NULL. Verifique!!'. PHP_EOL;
        return false;
    }

}