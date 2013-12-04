<?php

namespace Goteo\Library {

	use Goteo\Core\Model,
        Goteo\Core\Exception,
        Goteo\Library\Template,
        Goteo\Core\View;
	/*
	 * Clase para montar el contenido de la newsletter
	 *
	 */
    class Newsletter {

		static public function getTesters () {
            $list = array();

            $sql = "SELECT
                        user.id as user,
                        user.name as name,
                        user.email as email
                    FROM user
                    INNER JOIN user_interest
                        ON  user_interest.user = user.id
                        AND user_interest.interest = 15
                    ORDER BY user.id ASC
                    ";

            if ($query = Model::query($sql, $values)) {
                foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $receiver) {
                    $list[] = $receiver;
                }
            }

            return $list;

        }

        /*
         * Usuarios actualmente activos que no tienen bloqueado el envio de newsletter
         */
		static public function getReceivers () {

            $list = array();

            $sql = "SELECT
                        user.id as user,
                        user.name as name,
                        user.email as email
                    FROM user
                    LEFT JOIN user_prefer
                        ON user_prefer.user = user.id
                    WHERE user.id != 'root'
                    AND user.active = 1
                    AND (user_prefer.mailing = 0 OR user_prefer.mailing IS NULL)
                    ORDER BY user.id ASC
                    ";

            if ($query = Model::query($sql, $values)) {
                foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $receiver) {
                    $list[] = $receiver;
                }
            }

            return $list;

        }

		static public function initiateSending ($subject, $receivers, $tpl) {

            /*
             * Grabar el contenido para el sinoves en la tabla mail, obtener el id y el codigo para sinoves
             *
             */


            try {
                Model::query("START TRANSACTION");
                // eliminamos los datos del envío
                Model::query("DELETE FROM mailer_content");
                // eliminamos los destinatarios
                Model::query("DELETE FROM mailer_send");

                // contenido (plantilla, mas contenido de newsletter
                $template = Template::get($tpl);
                $content = ($tpl == 33) ? Newsletter::getContent($template->text) : $template->text;

                $sql = "INSERT INTO mail (id, email, html, template, node) VALUES ('', :email, :html, :template, :node)";
                $values = array (
                    ':email' => 'any',
                    ':html' => $content,
                    ':template' => $tpl,
                    ':node' => $_SESSION['admin_node']
                );
                $query = Model::query($sql, $values);

                $mailId = Model::insertId();
                $sql = "INSERT INTO `mailer_content` (`id`, `active`, `mail`, `subject`)
                    VALUES ('' , '0', :mail, :subject)";
                Model::query($sql, array(':subject'=>$subject, ':mail'=>$mailId));

                // destinatarios
                $sql = "INSERT INTO `mailer_send` (`id`, `user`, `email`, `name`) VALUES ('', :user, :email, :name)";
                foreach ($receivers as $user) {
                    Model::query($sql, array(':user'=>$user->user, ':email'=>$user->email, ':name'=>$user->name));
                }

                Model::query("COMMIT");
                return true;

            } catch(\PDOException $e) {
                echo "HA FALLADO!!" . $e->getMessage();
                die;
                return false;
            }

        }

		static public function getSending () {
            try {
                // recuperamos los datos del envío
                $query = Model::query("
                    SELECT
                        mailer_content.id as id,
                        mailer_content.active as active,
                        mailer_content.mail as mail,
                        mailer_content.subject as subject,
                        DATE_FORMAT(mailer_content.datetime, '%d/%m/%Y %H:%i:%s') as date,
                        mailer_content.blocked as blocked
                    FROM mailer_content
                    ORDER BY active DESC, id DESC
                    LIMIT 1
                    ");
                $mailing = $query->fetchObject();

                // y el estado
                $query = Model::query("
                SELECT
                        COUNT(mailer_send.id) AS receivers,
                        SUM(IF(mailer_send.sended = 1, 1, 0)) AS sended,
                        SUM(IF(mailer_send.sended = 0, 1, 0)) AS failed,
                        SUM(IF(mailer_send.sended IS NULL, 1, 0)) AS pending
                FROM	mailer_send
                ");
                $sending = $query->fetchObject();

                $mailing->receivers = $sending->receivers;
                $mailing->sended    = $sending->sended;
                $mailing->failed    = $sending->failed;
                $mailing->pending   = $sending->pending;

                return $mailing;
            } catch(\PDOException $e) {
                $errors[] = "HA FALLADO!!" . $e->getMessage();
                return false;
            }
        }

        /*
         * Listado completo de destinatarios/envaidos/fallidos/pendientes 
         */
		static public function getDetail ($detail = 'receivers') {

            $list = array();

            switch ($detail) {
                case 'sended':
                    $sqlFilter = " AND mailer_send.sended = 1";
                    break;
                case 'failed':
                    $sqlFilter = " AND mailer_send.sended = 0";
                    break;
                case 'pending':
                    $sqlFilter = " AND mailer_send.sended IS NULL";
                    break;
                case 'receivers':
                default:
                    $sqlFilter = '';
            }

            $sql = "SELECT
                    user.id as user,
                    user.name as name,
                    user.email as email
                FROM user
                INNER JOIN mailer_send
                    ON mailer_send.user = user.id
                    $sqlFilter
                ORDER BY user.id";

            if ($query = Model::query($sql, $values)) {
                foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $user) {
                    $list[] = $user;
                }
            }

            return $list;

        }


		static public function activateSending () {
            // marcamos como activo el envio
            $query = Model::query("UPDATE mailer_content SET active = 1 ORDER BY id DESC LIMIT 1");
            return ($query->rowCount() == 1);
        }

		static public function getContent ($content) {
            // orden de los elementos en portada
            $order = \Goteo\Model\Home::getAll();

            // entradas de blog
            $posts_content = '';
            /*
            if (isset($order['posts'])) {
                $home_posts = \Goteo\Model\Post::getList();
                if (!empty($home_posts)) {
//                    $posts_content = '<div class="section-tit">'.Text::get('home-posts-header').'</div>';
                    foreach ($posts as $id=>$title) {
                        $the_post = \Goteo\Model\Post::get($id);
                        $posts_content .= new View('view/email/newsletter_post.html.php', array('post'=>$the_post));
                        break; // solo cogemos uno
                    }
                }
            }
             *
             */

            // Proyectos destacados
            $promotes_content = '';
            if (isset($order['promotes'])) {
                $home_promotes  = \Goteo\Model\Promote::getAll(true);

                if (!empty($home_promotes)) {
//                    $promotes_content = '<div class="section-tit">'.Text::get('home-promotes-header').'</div>';
                    foreach ($home_promotes as $key => $promote) {
                        try {
                            $the_project = \Goteo\Model\Project::getMedium($promote->project, LANG);
                            $promotes_content .= new View('view/email/newsletter_project.html.php', array('promote'=>$promote, 'project'=>$the_project));
                        } catch (\Goteo\Core\Error $e) {
                            continue;
                        }
                    }
                }
            }

            // capital riego
            $drops_content = '';
            /*
            if (isset($order['drops'])) {
                $calls     = \Goteo\Model\Call::getActive(3); // convocatorias en modalidad 1; inscripcion de proyectos
                $campaigns = \Goteo\Model\Call::getActive(4); // convocatorias en modalidad 2; repartiendo capital riego

                if (!empty($calls) || !empty($campaigns)) {
//                    $drops_content = '<div class="section-tit">'.str_replace('<br />', ': ', Text::get('home-calls-header')).'</div>';
                    // aqui lo del contenido dinamico
                }
            }
            */

            // montammos el contenido completo
            $tmpcontent = $content;
            foreach (\array_keys($order) as $item) {
                $var = $item.'_content';
                $tmpcontent .= $$var;
            }

            return $tmpcontent;
		}

	}
	
}