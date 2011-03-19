<?php

namespace Goteo\Model {

	class User extends \Goteo\Core\Model {
		
		public 
			// Profile data
			$id,
			$email,
            $name,  //Nickname
			$avatar, //imagen
			$about,
			$interests = array(),
			$twitter,
			$facebook,
			$linkedIn,
                
			// Personal data
			$realName, //Nombre completo
			$country;
		
		/**
		 * @FIXME: Devuelve el usuario como un array, utiliza parámetros con nombre (a modo de ejemplo).
		 * @param string $id
		 */
		public static function get ($id) {            
			$query = User::query("SELECT * FROM user WHERE id = :id  AND active = :visible", array(':id' => $id, ':visible' => true));
			return $query->fetch();                                    
		}

		/**
		 * @FIXME: Devuelve todos los usuarios activos en un array de arrays, utiliza parámetros con signo de interrogación (a modo de ejemplo)
		 * @TODO: La he llamado 'getAll', pero podría ser también 'all' o 'getList'. En un principio pensé en 'list', pero está reservada :(
		 */
		public static function getAll() {
			$query = User::query("SELECT * FROM user WHERE active = ?", array(true));
			return $query->fetchAll();        	
		}

		public function save () {}
	}   
}