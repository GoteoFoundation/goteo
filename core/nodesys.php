<?php

namespace Goteo\Core {

    use Goteo\Core\Model;

    class NodeSys {

        /*
         * Comprueba si es un nodo válido
         */
        public static function isValid ($id) {

            $query = Model::query("
                SELECT
                    active
                FROM node
                WHERE id = :id
                LIMIT 1
                ",
                array(
                    ':id' => $id
                )
            );
            return (bool) $query->fetchColumn();
        }

        /**
         * Solo nodos activos apra el desplegable
         * @return <type>
         */
        public static function activeNodes () {

            $list = array();

            $sql = Model::query("
                SELECT
                    id, name, url
                FROM node
                WHERE id != 'goteo'
                AND active = 1
                ORDER BY `name` ASC
                ");

            foreach ($sql->fetchAll(\PDO::FETCH_OBJ) as $item) {
                $list[] = $item;
            }

            return $list;
        }

        /*
         * Establece las constantes de configuracion de nodo
         */
        public static function setConfig ($id) {

            $query = Model::query("
                SELECT
                    name,
                    url
                FROM node
                WHERE id = :id
                LIMIT 1
                ",
                array(
                    ':id' => $id
                )
            );

            $config = $query->fetch(\PDO::FETCH_ASSOC);

            if (!empty($config)) {
                define('NODE_NAME', $config['name']);
                define('NODE_URL', $config['url']);
            }

            $conf_file = 'nodesys/'.$id.'/config.php';
            if (file_exists($conf_file)) {
                require_once $conf_file;
            }

        }

    }
}