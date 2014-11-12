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

            echo $sql . '<br />';
            $query = Model\Image::query($sql);
            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $row) {
                echo $row->id.'<br />';
            }

            echo "<hr />";

            die;

            //--------------------------------------
            $path = GOTEO_DATA_PUBLIC_PATH . 'images';

            echo "LIMPIANDO ARCHIVOS DE IMAGENES NO REGISTRADAS<br />";

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

            echo 'Listo!';

            return;
        }

    }

}
