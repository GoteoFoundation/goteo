<?php

namespace Goteo\Controller {

    use Goteo\Library\Page,
        Goteo\Core\View,
        Goteo\Model;

    class Faq extends \Goteo\Core\Controller {

        public function __construct() {
            //activamos la cache para todo el controlador faq
            \Goteo\Core\DB::cache(true);
        }

        public function index ($current = 'node') {

            // si llega una pregunta  ?q=70
            if (isset($_GET['q'])) {
                $current = null;
                $show = $_GET['q'];
            } else {
                $show = null;
            }

            $page = Page::get('faq');
            $faqs = array();

            $sections = Model\Faq::sections();
            $colors   = Model\Faq::colors();

            foreach ($sections as $id=>$name) {
                $qs = Model\Faq::getAll($id);

                if (empty($qs)) {
                    if ($id == $current && $current != 'node') {
                        throw new \Goteo\Core\Redirection('/faq');
                    }
                    unset($sections[$id]);
                    continue;
                }

                $faqs[$id] = $qs;
                foreach ($faqs[$id] as &$question) {
                    $question->description = nl2br(str_replace(array('%SITE_URL%'), array(SITE_URL), $question->description));
                    if (isset($show) && $show == $question->id) {
                        $current = $id;
                    }
                }
            }

            return new View(
                'faq.html.php',
                array(
                    'faqs'     => $faqs,
                    'current'  => $current,
                    'sections' => $sections,
                    'colors'   => $colors,
                    'show'     => $show
                )
             );

        }

    }

}
