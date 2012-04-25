<?php

namespace Goteo\Controller {

    use Goteo\Library\Page,
        Goteo\Core\View,
        Goteo\Model;

    class Faq extends \Goteo\Core\Controller {

        public function index ($current = 'node') {

            $page = Page::get('faq');
            $faqs = array();

            $sections = Model\Faq::sections();
            $colors   = Model\Faq::colors();

            foreach ($sections as $id=>$name) {
                $qs = Model\Faq::getAll($id);
                
                if (empty($qs)) {
                    if ($id == $current) {
                        throw new \Goteo\Core\Redirection('/faq');
                    }
                    unset($sections[$id]);
                    continue;
                }

                $faqs[$id] = $qs;
                foreach ($faqs[$id] as &$question) {
                    $question->description = nl2br(str_replace(array('%SITE_URL%'), array(SITE_URL), $question->description));
                }
            }

            return new View(
                'view/faq.html.php',
                array(
                    'faqs'     => $faqs,
                    'current'  => $current,
                    'sections' => $sections,
                    'colors'   => $colors
                )
             );

        }

    }

}