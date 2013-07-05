<?php
namespace Goteo\Controller {

    use Goteo\Model,
        Goteo\Model\User,
        Goteo\Library\Feed;

    class JSON extends \Goteo\Core\Controller {

		private $result = array();

		/**
		 * Localizaciones para autocomplete
		 * */
		public function locations() {

            $locations = Model\Location::getAll();
            
            // ordenar por nombre
            uasort($locations,
                function ($a, $b) {
                    if ($a->name == $b->name) return 0;
                    return ($a->name > $b->name) ? 1 : -1;
                    }
                );
            
			foreach ($locations as $loc) {
                $this->result[] = $loc->name;
            }

			return $this->output();
		}

		/**
		 * Solo retorna si la sesion esta activa o no
		 * */
		public function keep_alive() {

			$this->result = array(
				'logged'=>false
			);
			if($_SESSION['user'] instanceof User) {
				$this->result['logged'] = true;
				$this->result['userid'] = $_SESSION['user']->id;
			}

			return $this->output();
		}

		/**
		 * Intenta asignar proyecto a convocatoria
		 * */
		public function assign_proj_call($id = null) {

			$this->result = array(
				'assigned'=>false
			);
			if($_SESSION['assign_mode'] === true && !empty($_SESSION['call']->id) && !empty($id)) {

                $registry = new Model\Call\Project;
                $registry->id = $id;
                $registry->call = $_SESSION['call']->id;
                if ($registry->save($errors)) {
    				$this->result['assigned'] = true;

                    $projectData = Model\Project::get($id);

                    // Evento feed
                    $log = new Feed();
                    $log->setTarget($projectData->id);
                    $log->populate('proyecto asignado a convocatoria por convocador', 'admin/calls/'.$_SESSION['call']->id.'/projects',
                        \vsprintf('El convocador %s ha asignado el proyecto %s a la convocatoria %s', array(
                            Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                            Feed::item('project', $projectData->name, $projectData->id),
                            Feed::item('call', $_SESSION['call']->name, $_SESSION['call']->id))
                        ));
                    $log->doAdmin('call');

                    // si la convocatoria está en campaña, feed público
                    if ($_SESSION['call']->status == 4) {
                        $log->populate($projectData->name, '/project/'.$projectData->id,
                            \vsprintf('Ha sido seleccionado en la convocatoria %s', array(
                                Feed::item('call', $_SESSION['call']->name, $_SESSION['call']->id))
                            ), $projectData->gallery[0]->id);
                        $log->doPublic('projects');
                    }
                    unset($log);
                }
			}

			return $this->output();
		}

		/**
		 * Json encoding...
		 * */
		public function output() {

			header("Content-Type: application/javascript");

			return json_encode($this->result);
		}
    }
}
