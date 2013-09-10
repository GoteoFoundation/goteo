<?php

namespace Goteo\Controller\Cron {

    use Goteo\Model,
        Goteo\Core\Redirection,
        Goteo\Core\Error;

    class Imgrename {

        public static function process () {

            $path = GOTEO_DATA_PATH . 'images' . DIRECTORY_SEPARATOR;

            echo "RENOMBRANDO ARCHIVO DE IMAGENES<br />";
            echo "Devuelve las extensiones -ext a .ext<br /><br />";
            
            $ext = array(
                '-jpg' => '.jpg',
                '-jpeg' => '.jpeg',
                '-png' => '.png',
                '-gif' => '.gif',
                '-bmp' => '.bmp',
                '-tif' => '.tif'
            );
            
            foreach ($ext as $badExt=>$goodExt) {
            
                // obtenemos los registros chungos
                $sql = "SELECT * FROM image WHERE name LIKE '%{$badExt}%' AND name NOT LIKE '%{$goodExt}'";
                echo $sql.'<br />';
                $query = Model\Image::query($sql);
                // para cada uno 
                foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $row) {

                    $badName = $row->name;
                    $goodName = str_replace($badExt, '', $badName).'r'.$goodExt;
                    
                    // miramos si el archivo con extensión bien existe
                    if (file_exists($path.$badName)) {
                        if (!file_exists($path.$goodName)) {
                            // renombramos el archivo
                            if (rename($path.$badName, $path.$goodName)) {
                                // luego cambiamos el nombre en el registro
                                if (Model\Image::query("UPDATE image SET name = '{$goodName}' WHERE id = '{$row->id}'")) {
                                    // ok
                                } else {
                                    die("El archivo {$badName} renombrado a {$goodName} no se ha podido actualizar en la tabla, registro {$row->id}.");
                                }
                            // 
                            //  si no puede hacer el update, paramos todo con un die importante
                            } else {
                                echo "El archivo {$badName} no se ha podido renombrar a {$goodName}. <br />";
                            }
                        } else {
                            echo "El archivo {$goodName} YA existe. <br />";
                        }
                    } else {
                        echo "El archivo {$badName} NO existe. <br />";
                    }

                }
                
                echo '<hr />';
                
            }
            
            echo '<hr />';
            echo 'Listo!';

            return;
        }

    }

}
