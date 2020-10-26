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

use Goteo\Library\Text;
use Goteo\Application\Message;
use Goteo\Application\Lang;
use Goteo\Application\Config;
use Goteo\Application\Session;
use Goteo\Application\View;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Model;
use Goteo\Model\Faq;
use Goteo\Model\Faq\FaqSection as FaqSection;
use Goteo\Model\Faq\FaqSubsection as FaqSubsection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class FaqController extends \Goteo\Core\Controller {

    public function __construct() {
        // Cache & replica read activated in this controller
        $this->dbReplica(true);
        $this->dbCache(true);
        View::setTheme('responsive');
    }

    public function indexAction ($section='', $tag='', Request $request) {

        $faq_sections=FaqSection::getList();

        return $this->viewResponse('faq/index', [
                    'meta_title' => Text::get('faq-meta-title'),
                    'meta_description' => Text::get('faq-meta-description'),
                    'faq_sections' => $faq_sections
                ]
        );
    }

    public function sectionAction($section, Request $request)
    {
        $faq_section=FaqSection::getBySlug($section);
        $subsections=FaqSubsection::getList(['section' => $faq_section->id]);

        return $this->viewResponse('faq/section', [
            'meta_title' => $faq_section->name.' :: Faq',
            'meta_description' => Text::get('faq-meta-description'),
            'faq_section' => $faq_section,
            'subsections' => $subsections
        ]);

    }

    public function individualAction($faq, Request $request)
    {
        $faq=Faq::getBySlug($faq);
        $faq_subsection=FaqSubsection::get($faq->subsection_id);
        $faq_section=FaqSection::getById($faq_subsection->section_id);

        // Sidebar menu
        $subsections=FaqSubsection::getList(['section' => $faq_section->id]);

        return $this->viewResponse('faq/individual', [
            'meta_title' => $faq->title.' :: Faq',
            'meta_description' => Text::get('faq-meta-description'),
            'faq' => $faq,
            'faq_section' => $faq_section,
            'subsections' => $subsections
        ]);

    }

}


