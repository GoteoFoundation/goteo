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
                    id
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
         * tampoco sacamos el nodo en el que estamos
         * ni el de testeo
         * @return <type>
         */
        public static function activeNodes ($current = \GOTEO_NODE) {

            $list = array();

            $sql = Model::query("
                SELECT
                    id, name, url
                FROM node
                WHERE id != '$current'
                AND active = 1
                AND id != 'testnode'
                ORDER BY `name` ASC
                ");

            foreach ($sql->fetchAll(\PDO::FETCH_OBJ) as $item) {
                $list[] = $item;
            }

            return $list;
        }

        /*
         * Saca los datos del nodo
         */
        public static function setData ($id) {

            // datos del nodo
            $query = Model::query("
                SELECT
                    name,
                    url,
                    subtitle,
                    description,
                    logo
                FROM node
                WHERE id = :id
                LIMIT 1
                ",
                array(
                    ':id' => $id
                )
            );

            $config = $query->fetch(\PDO::FETCH_OBJ);

            if (!empty($config)) {
                if (!empty($config->logo)) {
                    $config->logo = \Goteo\Model\Image::get($config->logo);
                }
                return $config;
            }

        }

    }
}