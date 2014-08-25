<?php

namespace Goteo\Controller\Cron {

    use Goteo\Model,
        Goteo\Core\Redirection,
        Goteo\Core\Error;

    class Cleanup {

        public static function process () {

            echo "LIMPIANDO REGISTROS DE IMAGENES QUE NO SE USAN<br />";
            echo "Tablas: banner, bazar, call, node, user, call_banner, call_sponsor, feed, glossary_image, info_image, post, post_image, project, project_image, sponsor, user_vip<br /><br />";
            
            // obtenemos arrays de imágenes en uso
            echo 'Imagenes en uso: <br />';
            $en_uso = array();
            
            $sql = "SELECT image as id FROM `banner` WHERE image IS NOT NULL AND image REGEXP '^[0-9]+$'
                    UNION DISTINCT
                    SELECT logo as id FROM `call` WHERE logo IS NOT NULL AND logo REGEXP '^[0-9]+$'
                    UNION DISTINCT
                    SELECT image as id FROM `call` WHERE image IS NOT NULL AND image REGEXP '^[0-9]+$'
                    UNION DISTINCT
                    SELECT backimage as id FROM `call` WHERE backimage IS NOT NULL AND backimage REGEXP '^[0-9]+$'
                    UNION DISTINCT
                    SELECT image as id FROM `feed` WHERE image IS NOT NULL AND image REGEXP '^[0-9]+$'
                    UNION DISTINCT
                    SELECT logo as id FROM `node` WHERE logo IS NOT NULL AND logo REGEXP '^[0-9]+$'
                    UNION DISTINCT
                    SELECT avatar as id FROM `user` WHERE avatar IS NOT NULL AND avatar REGEXP '^[0-9]+$'
                    UNION DISTINCT
                    SELECT image as id FROM `call_banner` WHERE image IS NOT NULL AND image REGEXP '^[0-9]+$'
                    UNION DISTINCT
                    SELECT image as id FROM `call_sponsor` WHERE image IS NOT NULL AND image REGEXP '^[0-9]+$'
                    UNION DISTINCT
                    SELECT image as id FROM `glossary_image` WHERE image IS NOT NULL AND image REGEXP '^[0-9]+$'
                    UNION DISTINCT
                    SELECT image as id FROM `info_image` WHERE image IS NOT NULL AND image REGEXP '^[0-9]+$'
                    UNION DISTINCT
                    SELECT image as id FROM `news` WHERE image IS NOT NULL AND image REGEXP '^[0-9]+$'
                    UNION DISTINCT
                    SELECT image as id FROM `post` WHERE image IS NOT NULL AND image REGEXP '^[0-9]+$'
                    UNION DISTINCT
                    SELECT image as id FROM `post_image` WHERE image IS NOT NULL AND image REGEXP '^[0-9]+$'
                    UNION DISTINCT
                    SELECT image as id FROM `project` WHERE image IS NOT NULL AND image REGEXP '^[0-9]+$'
                    UNION DISTINCT
                    SELECT image as id FROM `project_image` WHERE image IS NOT NULL AND image REGEXP '^[0-9]+$'
                    UNION DISTINCT
                    SELECT image as id FROM `sponsor` WHERE image IS NOT NULL AND image REGEXP '^[0-9]+$'
                    UNION DISTINCT
                    SELECT image as id FROM `bazar` WHERE image IS NOT NULL AND image REGEXP '^[0-9]+$'
                    UNION DISTINCT
                    SELECT image as id FROM `stories` WHERE image IS NOT NULL AND image REGEXP '^[0-9]+$'
                    UNION DISTINCT
                    SELECT image as id FROM `user_vip` WHERE image IS NOT NULL AND image REGEXP '^[0-9]+$'
                    ";

            // echo $sql . '<br />';
            $query = Model\Image::query($sql);
            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $row) {
                if (!empty($row->id)) {
                    $en_uso[$row->id] = $row->id;
                }
            }
            
            echo 'Son '.count($en_uso).'<br />';
            echo "<br />";

            // las que no se usan sería
            echo "SELECT * FROM image where id NOT IN ('".implode("','", $en_uso)."')";
            die;
            
            // obtenemos array de imágenes en tabla
            echo 'Actualmente en tabla:<br />';
            $en_tabla = array();
            $sql = "SELECT id FROM image";
            $query = Model\Image::query($sql);
            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $row) {
                $en_tabla[$row->id] = $row->id;
            }
            
            echo 'Hay '.count($en_tabla).'<br />';
            echo "<br />";
            
            
            // sacamos el array de imágenes que no se usan
            echo 'Que no se usan:<br />';
            $quitar_de_tabla = array_diff_assoc($en_tabla, $en_uso);

            // no quitamos los ids 1, 2 y 3
            unset($quitar_de_tabla[1]);
            unset($quitar_de_tabla[2]);
            unset($quitar_de_tabla[3]);
            
            echo 'Resultado, '.count($quitar_de_tabla).' registros a borrar<br />';
            echo "<br />";
            
            // las eliminamos
            if ($_GET['del'] == 'true' && !empty($quitar_de_tabla)) {
                $sql = "DELETE FROM image WHERE id IN (".implode(", ", $quitar_de_tabla).");";
//                echo $sql . '<br />';
                $query = Model\Image::query($sql);
                echo 'Se han borrado ' . $query->rowCount() . ' registros<br />';
            } else {
                $sql = "SELECT * FROM image WHERE id IN (".implode(", ", $quitar_de_tabla).");";
                echo $sql . '<br />';
                echo 'Para que borre automaticamente, pasar el parametro ?del=true<br />';
            }
            
            echo '<hr />';

            //--------------------------------------
            $path = GOTEO_DATA_PATH . 'images';
            
            echo "LIMPIANDO ARCHIVOS DE IMAGENES NO REGISTRADAS<br />";
            
            // obtenemos el array de archivos en la tabla
            $registros_no_carpeta = array();
            $archivos_en_tabla = array();
            $sql = "SELECT name, id FROM image";
            $query = Model\Image::query($sql);
            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $row) {
                $archivos_en_tabla[] = $row->name;
                if (!file_exists($path.DIRECTORY_SEPARATOR.$row->name)) {
                    $registros_no_carpeta[$row->id] = $row->id;
                }
            }
            echo 'En la tabla hay '.count($archivos_en_tabla).'<br />';
            
            // obtenemos el array de archivos en la carpeta de imágenes
            $archivos_en_carpeta = array();
            if ($dh = opendir($path)) { 
                 while (($file = readdir($dh)) !== false) { 
                    $filename = $path . DIRECTORY_SEPARATOR. $file;
                    if (is_file($filename)){ 
                       $archivos_en_carpeta[] = $file;
                    } 
                 }
            }
            closedir($dh);     
            echo 'En la carpeta hay '.count($archivos_en_carpeta).'<br />';
            
            // sacamos el array de archivos no registrados
            $quitar_de_carpeta = array_diff($archivos_en_carpeta, $archivos_en_tabla);
            
            echo 'Se van a eliminar '.count($quitar_de_carpeta).'<br />';
            
            // los eliminamos
            if ($_GET['del'] == 'true' && !empty($quitar_de_carpeta)) {
                $quitados = 0;
                foreach ($quitar_de_carpeta as $quitar_este) {
                    if (unlink($path . DIRECTORY_SEPARATOR. $quitar_este))
                        $quitados++;
                    else 
                        echo $quitar_este . ' no eliminado<br />';
                }
                echo 'Se han eliminado '. $quitados . ' archivos<br />';
            } else {
                echo 'Archivos a eliminar:<br />';
                echo 'Para que elimine automaticamente, pasar el parametro ?del=true<br />';
                echo \trace($quitar_de_carpeta);
            }
            
            // quitar registros de tabla cuyo archivo no está en la carpeta
            echo 'Registros cuyo archivo no está en la carpeta';
            if ($_GET['del'] == 'true' && !empty($registros_no_carpeta)) {
                $sql = "DELETE FROM image WHERE id IN (".implode(", ", $registros_no_carpeta).");";
                echo $sql . '<br />';
                Model\Image::query($sql);
            } else {
                echo \trace($registros_no_carpeta);
            }
            
            echo '<hr />';
            echo 'Listo!';

            return;
        }

    }

}
