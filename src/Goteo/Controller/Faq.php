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

use Goteo\Core\Controller;
use Goteo\Core\DB;
use Goteo\Core\Redirection;
use Goteo\Core\View;
use Goteo\Model;
use Goteo\Model\Page;

class Faq extends Controller {

    public function __construct() {
        DB::cache(true);
        DB::replica(true);
    }

    public function index ($current = 'node') {

        // si llega una pregunta  ?q=70
        if (isset($_GET['q'])) {
            $current = null;
            $show = $_GET['q'];
        } else {
            $show = null;
        }

        $faqs = array();
        $sections = Model\Faq::sections();
        $colors   = Model\Faq::colors();

        foreach ($sections as $id=>$name) {
            $qs = Model\Faq::getAll($id);

            if (empty($qs)) {
                if ($id == $current && $current != 'node') {
                    throw new Redirection('/faq');
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

        return new View('faq.html.php', [
            'faqs'     => $faqs,
            'current'  => $current,
            'sections' => $sections,
            'colors'   => $colors,
            'show'     => $show
        ]);
    }

}
