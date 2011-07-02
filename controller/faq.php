<?php

namespace Goteo\Controller {

    use Goteo\Library\Page,
        Goteo\Core\View,
        Goteo\Model;

    class Faq extends \Goteo\Core\Controller {

        public function index () {

            $page = Page::get('faq');
            $faqs = array();

            $sections = Model\Faq::sections();
            $colors   = Model\Faq::colors();

            foreach ($sections as $id=>$name) {
                $faqs[$id] = Model\Faq::getAll($id);
            }

            return new View(
                'view/faq.html.php',
                array(
                    'faqs'     => $faqs,
                    'sections' => $sections,
                    'colors'   => $colors
                )
             );

        }

    }

}