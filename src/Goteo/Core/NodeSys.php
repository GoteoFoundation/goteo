<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Core;

use Goteo\Application\Config;
use Goteo\Core\Model;

class NodeSys {

	/*
		     * Comprueba si es un nodo válido
	*/
	public static function isValid($id) {

		//activamos la cache para este metodo
		$current_cache = \Goteo\Core\DB::cache();
		\Goteo\Core\DB::cache(true);

		$query = Model::query("
            SELECT
                id
            FROM node
            WHERE id = :id
            LIMIT 1
            ",
			array(
				':id' => $id,
			)
		);
		$ret = (bool) $query->fetchColumn();
		//dejamos la cache como estaba
		\Goteo\Core\DB::cache($current_cache);
		return $ret;
	}

	/*
		     * Comprueba si es un nodo esta activo
	*/
	public static function isActive($id) {

		//activamos la cache para este metodo
		$current_cache = \Goteo\Core\DB::cache();
		\Goteo\Core\DB::cache(true);

		$query = Model::query("
            SELECT
                id
            FROM node
            WHERE id = :id
            AND active = 1
            AND id != 'testnode'
            LIMIT 1
            ",
			array(
				':id' => $id,
			)
		);
		$ret = (bool) $query->fetchColumn();
		//dejamos la cache como estaba
		\Goteo\Core\DB::cache($current_cache);
		return $ret;
	}

	/**
	 * Solo nodos activos con url propia apra el desplegable
	 * tampoco sacamos el nodo en el que estamos
	 * ni el de testeo
	 * @return <type>
	 */
	public static function activeNodes($current = null) {
		if (empty($current)) {
			$current = Config::get('node');
		}

		//activamos la cache para este metodo
		$current_cache = \Goteo\Core\DB::cache();
		\Goteo\Core\DB::cache(true);

		$list = array();

		$query = Model::query("
            SELECT
                id, name, url
            FROM node
            WHERE id != '$current'
            AND active = 1
            AND id != 'testnode'
            ORDER BY `name` ASC
            ");
		foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $item) {
			if (empty($item->url)) {
				$item->url = \SITE_URL . '/channel/' . $item->id;
			}

			$list[] = $item;
		}
		//dejamos la cache como estaba
		\Goteo\Core\DB::cache($current_cache);

		return $list;
	}

	/*
		     * Saca los datos del nodo
	*/
	public static function setData($id) {

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
				':id' => $id,
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
