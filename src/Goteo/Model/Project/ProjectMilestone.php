<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Model\Project {

    use Goteo\Library\Text;
    use Goteo\Application\Lang;
    use Goteo\Model\Milestone;
    use Goteo\Model\Blog\Post;
    use Goteo\Application\Exception\ModelNotFoundException;
    use Goteo\Application\Exception\ModelException;
    use Goteo\Application\Exception\DuplicatedEventException;

    class ProjectMilestone extends \Goteo\Core\Model {

        protected $Table = 'project_milestone';

        public
            $id,
            $project,
            $milestone=null,
            $milestone_type,
            $post=null,
            $date;

        /*
         *  Get Project milestone
         */
        public static function get ($project, $post=null) {


                $query = static::query("
                    SELECT
                        *
                    FROM project_milestone
                    WHERE project = :project AND post = :post
                    ", array(':project' => $project, ':post'=>$post));

                if($project_milestone = $query->fetchObject('\Goteo\Model\Milestone')) {
                   return $project_milestone;
                }

                return false;
        }

		public static function getAll ($project, $lang = null, $model_lang = null) {
            try {

                $values = [':project'=>$project];

                $sql= "SELECT *
                        FROM project_milestone
                        WHERE project = :project
                        ORDER BY date DESC, id DESC";

                //die(\sqldbg($sql, $values));

				$query = self::query($sql, $values);

                foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $item) {
                    if($item->milestone)
                    {
                        $item->type='milestone';
                        $item->milestone=Milestone::get($item->milestone);
                    }
                    else
                    {
                        $item->type='post';
                        $item->post=Post::get($item->post, $lang, $model_lang);
                    }

                        $array[] = $item;
                }

				return $array;
			} catch (\PDOException $e) {
                throw new \Goteo\Core\Exception($e->getMessage());
			}
		}

		public function validate(&$errors = array()) {
            // Estos son errores que no permiten continuar
            if (!$this->milestone&&!$this->post)
                $errors[] = 'The milestone not have blog or a milestone type';

            //cualquiera de estos errores hace fallar la validación
            if (!empty($errors))
                return false;
            else
                return true;
        }

		/**
         * Save.
         * @param   type array  $errors
         * @return  type bool   true|false
        */
        public function save(&$errors = array()) {

            try {
                if($this->milestone_type)
                {
                    $milestone=Milestone::random_milestone($this->milestone_type);
                    $this->milestone=$milestone->id;
                    $this->date=date('Y-m-d');
                }

            }
            catch(ModelNotFoundException $e){
                $errors[]= 'No milestone: ' . $e->getMessage();
            }

            if(!$this->validate($errors)) return false;

            try {
                $this->dbInsertUpdate(['project', 'milestone', 'date', 'post']);
                return true;
            }
            catch(\PDOException $e) {
                $errors[] = 'Error saving milestone: ' . $e->getMessage();
            }

            return false;
        }

         /*
         * Para quitar un elemento
         */
        public function removePostMilestone (&$errors = array()) {

            $sql = "DELETE FROM project_milestone WHERE project = :project AND post = :post";
            try {
                self::query($sql, array(':project'=>$this->project, ':post'=>$this->post));
            } catch (\PDOException $e) {
                // throw new Exception("Delete error in $sql");
                return false;
            }
            return true;
        }


	}

}
