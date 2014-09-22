<?php
namespace Goteo\Controller {

    use Goteo\Model,
        Goteo\Model\User,
        Goteo\Library\Text,
        Goteo\Library\Feed;

    class JSON extends \Goteo\Core\Controller {

		private $result = array();

		/**
		 * Método de datos para la visualización de goteo-analytics
         *
         * @param varchar(50) $id Id de proyecto
         * @return array formato json
		 * */
		public function invests($id) {

            // la lonexión a la base de datos la hace el core de goteo y se usa mediante lod modelos

            $invests = array();
            $sql = "SELECT amount, user, invested FROM invest WHERE project = ? AND status IN ('0', '1', '3', '4')"; // solo aportes que aparecen públicamente
            $result = Model\Invest::query($sql, array($id));
            foreach ($result->fetchAll(\PDO::FETCH_ASSOC) as $row){
                $invests[] = $row;
            }

            $dates = array();
            $sql = 'SELECT published, closed, success, passed FROM project WHERE id = ?';
            $result = Model\Invest::query($sql, array($id));
            foreach ($result->fetchAll(\PDO::FETCH_ASSOC) as $row){
                $dates = $row;
            }

            $optimum = $minimum = 0;
            $sql = 'SELECT sum(amount) as amount, required FROM cost WHERE project = ? GROUP BY required';
            $result = Model\Invest::query($sql, array($id));
            foreach ($result->fetchAll(\PDO::FETCH_ASSOC) as $row){
                if ($row['required'] == 1){
                    $minimum = $row['amount'];
                } else {
                    $optimum = $row['amount'];
                }
            }

            $this->result = array('invests' => $invests,
                            'dates' => $dates,
                            'minimum' => $minimum,
                            'optimum' => $optimum);

			return $this->output();
		}


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
                'logged'  => false,
                'expires' => 0,
				'info' => ''
			);

            $init = (int) $_SESSION['init_time'];
            $session_time = defined('GOTEO_SESSION_TIME') ? GOTEO_SESSION_TIME : 3600 ;
            if($_SESSION['user'] instanceof User) {
                $this->result['logged'] = true;
                $this->result['userid'] = $_SESSION['user']->id;
				$this->result['expires'] = START_TIME + $session_time - $init;
                if(START_TIME > $init + $session_time - 600 && empty($_SESSION['init_time_advised'])) {
                    $this->result['info'] = Text::get('session-about-to-expire');;
                    $_SESSION['init_time_advised'] = true;
                }
            }
            elseif(empty($init)) {
                $this->result['info'] = Text::get('session-expired');;
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
		 * Meses en Locale
		 * */
		public function months($full = true) {

            $fmt = ($full) ? '%B' : '%b';

            $months = array();

            for( $i = 1; $i <= 12; $i++ ) {
                $months[ $i ] = strftime( $fmt, mktime( 0, 0, 0, $i, 1 ) );
            }

            $this->result['months'] = $months;

            return $this->output();
		}

		/**
		 * Json encoding...
		 * */
		public function output() {

			header("Content-Type: application/json; charset=utf-8");

			return json_encode($this->result);
		}
    }
}
