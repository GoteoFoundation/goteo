<?php

namespace Goteo\Library {

	use Goteo\Model,
        Goteo\Core\View;
	/*
	 * Clase para montar el contenido de la newsletter
	 *
	 */
    class Newsletter {
		
		static public function getContent ($content) {
            // orden de los elementos en portada
            $order = Model\Home::getAll();

            // entradas de blog
            $posts_content = '';
            /*
            if (isset($order['posts'])) {
                $home_posts = Model\Post::getList();
                if (!empty($home_posts)) {
//                    $posts_content = '<div class="section-tit">'.Text::get('home-posts-header').'</div>';
                    foreach ($posts as $id=>$title) {
                        $the_post = Model\Post::get($id);
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
                $home_promotes  = Model\Promote::getAll(true);

                if (!empty($home_promotes)) {
//                    $promotes_content = '<div class="section-tit">'.Text::get('home-promotes-header').'</div>';
                    foreach ($home_promotes as $key => $promote) {
                        try {
                            $the_project = Model\Project::getMedium($promote->project, LANG);
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
                $calls     = Model\Call::getActive(3); // convocatorias en modalidad 1; inscripcion de proyectos
                $campaigns = Model\Call::getActive(4); // convocatorias en modalidad 2; repartiendo capital riego

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