<?php

namespace Goteo\Model\Call {

    use Goteo\Model;

    class Post extends \Goteo\Core\Model {

        public
            $id,
            $call;


        /**
         * Get the posts assigned to a call
         * @param varcahr(50) $id  Call identifier
         * @return instance of post
         */
		public static function get ($call) {
            try {

                $values = array(':call'=>$call);

                $sql = "SELECT
                            post as id
                        FROM call_post
                        WHERE call_post.call = :call
                        LIMIT 1
                        ";
                
                $query = static::query($sql, $values);
                $id = $query->fetchColumn();
                
                $postData = Model\Blog\Post::get($id);
                
                return $postData;
            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }
		}

		public function validate(&$errors = array()) {
            // Estos son errores que no permiten continuar
            if (empty($this->id))
                $errors[] = 'No hay ninguna entrada para guardar';

            if (empty($this->call))
                $errors[] = 'No hay ningun proyecto al que asignar';

            //cualquiera de estos errores hace fallar la validación
            if (!empty($errors))
                return false;
            else
                return true;
        }

		public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

			try {
	            $sql = "REPLACE INTO call_post (`call`, post) VALUES(:call, :post)";
                $values = array(':call'=>$this->call, ':post'=>$this->id);
				if (self::query($sql, $values)) {
    				return true;
                } else {
                    $errors[] = "$sql <pre>".print_r($values, 1)."</pre>";
                }
			} catch(\PDOException $e) {
				$errors[] = "La entrada {$this->id} no se ha asignado correctamente. Por favor, revise los datos." . $e->getMessage();
                return false;
			}

		}

		/**
		 * Quitar una palabra clave de un proyecto
		 *
		 * @param varchar(50) $call id de un proyecto
		 * @param INT(12) $id  identificador de la tabla keyword
		 * @param array $errors 
		 * @return boolean
		 */
		public function remove (&$errors = array()) {
			$values = array (
				':call'=>$this->call,
				':post'=>$this->id,
			);

			try {
                $sql = "DELETE FROM call_post WHERE post = :post AND `call` = :call";
                if (self::query($sql, $values)) {
                    return true;
                } else {
                    $errors[] = "$sql <pre>".print_r($values, 1)."</pre>";
                }
			} catch(\PDOException $e) {
				$errors[] = 'No se ha podido quitar la entrada ' . $this->id . ' de la convocatoria ' . $this->call . ' ' . $e->getMessage();
                //Text::get('remove-post-fail');
                return false;
			}
		}

    }
    
}