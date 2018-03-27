<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller {

    use Goteo\Model;
    use Goteo\Application\View;

    use Symfony\Component\HttpFoundation\Response;




    class GlossaryController extends \Goteo\Core\Controller {

        public function __construct() {
            // Cache & replica read activated in this controller
            \Goteo\Core\DB::cache(true);
            \Goteo\Core\DB::replica(true);
        }

        public function indexAction () {

            // Términos por página
            $tpp = 5;

            // indice de letras
            $index = array();

            // sacamos todo el glosario
            $glossary = Model\Glossary::getAll();

            //recolocamos los post para la paginacion
            $p = 1;
            $page = 1;
            $posts = array();
            foreach ($glossary as $id=>$post) {

                // tratar el texto para las entradas
                $post->text = str_replace(array('%SITE_URL%'), array(SITE_URL), $post->text);

                $posts[] = $post;

                // y la inicial en el indice
                $letra = \strtolower($post->title[0]);
                $index[$letra][] = (object) array(
                    'title' => $post->title,
                    'url'   => '/glossary?page='.$page.'#term' . $post->id
                );

                $p++;
                if ($p > $tpp) {
                    $p = 1;
                    $page++;
                }
            }

            return new Response(View::render(
            'glossary/index',
            array(
                    'tpp'   => $tpp,
                    'index' => $index,
                    'posts' => $posts
                )
         ));

        }

    }

}
