<?php

namespace Goteo\Library {

    use Goteo\Application\Config;
	use Goteo\Core\Model,
        Goteo\Core\Exception,
        Goteo\Model\Template,
        Goteo\Application\Lang,
        Goteo\Application\View;
	/*
	 * Clase para montar contenido de Boletín
	 *
	 */
    class Newsletter {

		static public function getTesters () {
            $list = array();

            $sql = "SELECT
                        user.id as user,
                        user.name as name,
                        user.email as email,
                        user.lang as lang,
                        user_prefer.comlang as comlang
                    FROM user
                    INNER JOIN user_interest
                        ON  user_interest.user = user.id
                        AND user_interest.interest = 15
                    LEFT JOIN user_prefer
                        ON user_prefer.user = user.id
                    ORDER BY user.id ASC
                    ";

            if ($query = Model::query($sql)) {
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
                        user.email as email,
                        user.lang as lang,
                        user_prefer.comlang as comlang
                    FROM user
                    LEFT JOIN user_prefer
                        ON user_prefer.user = user.id
                    WHERE user.active = 1
                    AND (user_prefer.mailing = 0 OR user_prefer.mailing IS NULL)
                    ORDER BY user.id ASC
                    ";

            if ($query = Model::query($sql)) {
                foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $receiver) {
                    $list[] = $receiver;
                }
            }

            return $list;

        }

        /*
         * Usuarios cofinanciadores del año fiscal actual
         * solo proyectos financiados
         */
		static public function getDonors ($year) {

            $year0 = $year;
            $year1 = $year + 1;

            $list = array();

            $sql = "SELECT
                        user.id as user,
                        user.name as name,
                        user.email as email,
                        IFNULL(user_prefer.comlang, user.lang) as lang
                FROM  invest
                INNER JOIN user ON user.id = invest.user
                LEFT JOIN user_prefer ON user_prefer.user = invest.user
                INNER JOIN project ON project.id = invest.project AND project.status IN (3, 4, 5) AND project.passed IS NOT NULL
                WHERE   invest.status IN ('1', '3')
                AND invest.invested >= '{$year0}-01-01'
                AND invest.invested < '{$year1}-01-01'
                GROUP BY invest.user
                ORDER BY user.email ASC";

            if ($query = Model::query($sql, $values)) {
                foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $receiver) {
                    $list[] = $receiver;
                }
            }

            return $list;

        }

		static public function getContent ($content, $lang = null) {
            if(empty($lang)) $lang = Lang::current();
            // necesitamos generar contenido en diferentes idiomas
            $_SESSION['VAR_LANG'] = $lang;

            // orden de los elementos en portada
            $order = \Goteo\Model\Home::getAll(Config::get('node'));

            // entradas de blog
            $posts_content = '';
            /*
            if (isset($order['posts'])) {
                $home_posts = \Goteo\Model\Post::getList();
                if (!empty($home_posts)) {
//                    $posts_content = '<div class="section-tit">'.Text::get('home-posts-header').'</div>';
                    foreach ($posts as $id=>$title) {
                        $the_post = \Goteo\Model\Post::get($id);
                        $posts_content .= View::render('email/partials/newsletter_post', array('post'=>$the_post));
                        break; // solo cogemos uno
                    }
                }
            }
             *
             */

            // Proyectos destacados
            $promotes_content = '';
            if (isset($order['promotes'])) {
                $home_promotes  = \Goteo\Model\Promote::getAll(true, GOTEO_NODE, $lang);

                if (!empty($home_promotes)) {
//                    $promotes_content = '<div class="section-tit">'.Text::get('home-promotes-header').'</div>';
                    foreach ($home_promotes as $key => $promote) {
                        try {
                            $promotes_content .= View::render('email/partials/newsletter_project', array('promote'=>$promote, 'project'=>$promote->projectData));
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
