<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller;

use Goteo\Application\View;
use Goteo\Core\Controller;
use Goteo\Core\DB;
use Goteo\Model;
use Symfony\Component\HttpFoundation\Response;

class GlossaryController extends Controller {

    public function __construct() {
        DB::cache(true);
        DB::replica(true);
    }

    public function indexAction () {

        $termsPerPage = 5;
        // indice de letras
        $index = array();
        $glossaries = Model\Glossary::getAll();

        //recolocamos los post para la paginacion
        $p = 1;
        $page = 1;
        $posts = array();
        foreach ($glossaries as $post) {
            // tratar el texto para las entradas
            $post->text = str_replace(array('%SITE_URL%'), array(SITE_URL), $post->text);
            $posts[] = $post;

            $firstLetter = strtolower($post->title[0]);
            $index[$firstLetter][] = (object) array(
                'title' => $post->title,
                'url'   => '/glossary?page='.$page.'#term' . $post->id
            );

            $p++;
            if ($p > $termsPerPage) {
                $p = 1;
                $page++;
            }
        }

        return new Response(View::render('glossary/index', array(
            'tpp'   => $termsPerPage,
            'index' => $index,
            'posts' => $posts
        )));
    }

}
