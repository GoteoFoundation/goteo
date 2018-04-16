<?php
/*
* This file is part of the Goteo Package.
*
* (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
*
* For the full copyright and license information, please view the README.md
* and LICENSE files that was distributed with this source code.
*/

namespace Goteo\Model\User;

use Goteo\Model\Image;
use Goteo\Application\Lang;
use Goteo\Application\Config;

class Interest extends \Goteo\Model\Category
{

    public
        $interest,
        $user;


    /**
     * Get the interests for a user
     * @param varcahr(50) $id user identifier
     * @return array of interests identifiers
     */
    public static function get($id, $lang = null)
    {
        $array = array();
        try {
            $query = static::query("SELECT * FROM user_interest WHERE user = ?", array($id));
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $interest) {
                $array[$interest->interest] = $interest;
            }

            return $array;
        } catch (\PDOException $e) {
            throw new \Goteo\Core\Exception($e->getMessage());
        }
    }

    public function __toString() {
        return $this->interest;
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
        $lang = Lang::current();
        $array = array();

        try {
            $values = array(':lang' => $lang);

            if (self::default_lang($lang) == Config::get('lang')) {
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

    public function save(&$errors = array())
    {
        $values = array(':user' => $this->user, ':interest' => $this->interest);

        try {
            $sql = "REPLACE INTO user_interest (user, interest) VALUES(:user, :interest)";
            self::query($sql, $values);
            return true;
        } catch (\PDOException $e) {
            $errors[] = "El interés {$this->interest} no se ha asignado correctamente. Por favor, revise los datos." . $e->getMessage();
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
            ':interest' => $this->interest,
        );

        try {
            self::query("DELETE FROM user_interest WHERE interest = :interest AND user = :user", $values);
            return true;
        } catch (\PDOException $e) {
            $errors[] = 'No se ha podido quitar el interes ' . $this->interest . ' del usuario ' . $this->user . ' ' . $e->getMessage();
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
                        user.avatar as user_avatar,
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

                $share->avatar = Image::get($share->user_avatar);
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
                      user.avatar as user_avatar,
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

                $share->avatar = Image::get($share->user_avatar);

                $array[] = $share;
            }

            return $array;
        } catch (\PDOException $e) {
            throw new \Goteo\Core\Exception($e->getMessage());
        }
    }

}

