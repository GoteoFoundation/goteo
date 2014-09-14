<?php

namespace Goteo\Model\User {

    use Goteo\Model\Image;

    class Interest extends \Goteo\Model\Category
    {

        public
            $id,
            $user;


        /**
         * Get the interests for a user
         * @param varcahr(50) $id user identifier
         * @return array of interests identifiers
         */
        public static function get($id)
        {
            $array = array();
            try {
                $query = static::query("SELECT interest FROM user_interest WHERE user = ?", array($id));
                $interests = $query->fetchAll();
                foreach ($interests as $int) {
                    $array[$int[0]] = $int[0];
                }

                return $array;
            } catch (\PDOException $e) {
                throw new \Goteo\Core\Exception($e->getMessage());
            }
        }

        /**
         * Get all categories available
         *
         *
         * @param user isset get all categories of a user
         * @return array
         */
        public static function getAll($user = null)
        {
            $array = array();

            try {
                $values = array(':lang' => \LANG);

                if (self::default_lang(\LANG) == 'es') {
                    $different_select = " IFNULL(category_lang.name, category.name) as name";
                } else {
                    $different_select = " IFNULL(category_lang.name, IFNULL(eng.name, category.name)) as name";
                    $eng_join = " LEFT JOIN category_lang as eng
                                    ON  eng.id = category.id
                                    AND eng.lang = 'en'";
                }

                $sql = "SELECT
                        category.id as id,
                        $different_select
                    FROM    category
                    LEFT JOIN category_lang
                        ON  category_lang.id = category.id
                        AND category_lang.lang = :lang
                    $eng_join";

                if (!empty($user)) {
                    $sql .= "INNER JOIN user_interest
                                ON  user_interest.interest = category.id
                                AND user_interest.user = :user
                                ";
                    $values[':user'] = $user;
                }
                $sql .= "ORDER BY name ASC
                        ";

                $query = static::query($sql, $values);
                $interests = $query->fetchAll();
                foreach ($interests as $int) {
                    if ($int[0] == 15) continue;
                    $array[$int[0]] = $int[1];
                }

                return $array;
            } catch (\PDOException $e) {
                throw new \Goteo\Core\Exception($e->getMessage());
            }
        }

        public function validate(&$errors = array())
        {
            // Estos son errores que no permiten continuar
            if (empty($this->id))
                $errors[] = 'No hay ningun interes para guardar';
            //Text::get('validate-interest-noid');

            if (empty($this->user))
                $errors[] = 'No hay ningun usuario al que asignar';
            //Text::get('validate-interest-nouser');

            //cualquiera de estos errores hace fallar la validación
            if (!empty($errors))
                return false;
            else
                return true;
        }

        public function save(&$errors = array())
        {
            if (!$this->validate($errors)) return false;

            $values = array(':user' => $this->user, ':interest' => $this->id);

            try {
                $sql = "REPLACE INTO user_interest (user, interest) VALUES(:user, :interest)";
                self::query($sql, $values);
                return true;
            } catch (\PDOException $e) {
                $errors[] = "El interés {$this->id} no se ha asignado correctamente. Por favor, revise los datos." . $e->getMessage();
                return false;
            }

        }

        /**
         * Quitar una palabra clave de un proyecto
         *
         * @param varchar(50) $user id de un proyecto
         * @param INT(12) $id identificador de la tabla keyword
         * @param array $errors
         * @return boolean
         */
        public function remove(&$errors = array())
        {
            $values = array(
                ':user' => $this->user,
                ':interest' => $this->id,
            );

            try {
                self::query("DELETE FROM user_interest WHERE interest = :interest AND user = :user", $values);
                return true;
            } catch (\PDOException $e) {
                $errors[] = 'No se ha podido quitar el interes ' . $this->id . ' del usuario ' . $this->user . ' ' . $e->getMessage();
                //Text::get('remove-interest-fail');
                return false;
            }
        }

        /*
         * Lista de usuarios que comparten intereses con el usuario
         *
         * Si recibimos una categoría de interés, solamente los que comparten esa categoría
         *
         */
        public static function share($user, $category = null, $limit = null)
        {
            $array = array();
            try {

                $values = array(':me' => $user);

                if (!empty($category)) {
                    $sqlFilter = "AND they.interest = :interest
                       ";
                    $values[':interest'] = $category;
                }
                $sql = "SELECT
                            user.id as id,
                            user.id as user,
                            user.name as name,
                            user.avatar as avatar,
                            user.amount as amount,
                            user.num_invested as invests,
                            user.num_owned as projects
                        FROM user
                        INNER JOIN user_interest as they
                            ON  they.user = user.id
                            AND they.user != :me
                            $sqlFilter
                        INNER JOIN user_interest as mine
                            ON they.interest = mine.interest
                            AND mine.user = :me
                        WHERE (user.hide = 0 OR user.hide IS NULL)
                        ";

                $sql .= " ORDER BY RAND()
               ";

                if (!empty($limit)) {
                    $sql .= " LIMIT $limit";
                }

                $query = static::query($sql, $values);
                $shares = $query->fetchAll(\PDO::FETCH_CLASS, 'Goteo\Model\User');
                foreach ($shares as $share) {

                    $share->avatar = Image::get($share->avatar);
                    $share->invests = (isset($share->invests)) ? $share->invests : $share->get_numInvested;
                    $share->projects = (isset($share->projects)) ? $share->projects : $share->get_numOwned;
                    $share->amount = (isset($share->amount)) ? $share->amount : $share->get_amount;

                    $array[] = $share;
                }

                shuffle($array);

                return $array;
            } catch (\PDOException $e) {
                throw new \Goteo\Core\Exception($e->getMessage());
            }
        }

        /*
         * Lista de usuarios de la comunidad que comparten un interés
         *
         */
        public static function shareAll($category)
        {
            $array = array();
            try {

                $values = array(':interest' => $category);

                $sql = "SELECT
                          user.id as user,
                          user.name as name,
                          user.avatar as avatar,
                          user.num_invested as invests,
                          user.num_owned as projects
                        FROM user_interest
                        INNER JOIN user
                            ON  user.id = user_interest.user
                            AND (user.hide = 0 OR user.hide IS NULL)
                        WHERE user_interest.interest = :interest
                        ";

                $query = static::query($sql, $values);
                $shares = $query->fetchAll(\PDO::FETCH_ONJ);
                foreach ($shares as $share) {

                    $share->avatar = Image::get($share->avatar);

                    $array[] = $share;
                }

                return $array;
            } catch (\PDOException $e) {
                throw new \Goteo\Core\Exception($e->getMessage());
            }
        }

    }

}