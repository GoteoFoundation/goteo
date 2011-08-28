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
                foreach ($faqs[$id] as $question) {
                    $faqs[$id]->description = nl2br(str_replace(array('%SITE_URL%'), array(SITE_URL), $question->description));
                }
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