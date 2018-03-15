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

    use Goteo\Model\Page,
        Goteo\Core\View,
        Goteo\Model;

    class Faq extends \Goteo\Core\Controller {

        public function __construct() {
            // Cache & replica read activated in this controller
            \Goteo\Core\DB::cache(true);
            \Goteo\Core\DB::replica(true);
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
