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

use Goteo\Application\Lang;
use Goteo\Application\View;
use Goteo\Core\Controller;
use Goteo\Core\Traits\LoggerTrait;
use Goteo\Library\Text;
use Goteo\Model\Faq;
use Goteo\Model\Faq\FaqSection;
use Goteo\Model\Faq\FaqSubsection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FaqController extends Controller {
    use LoggerTrait;

    public function __construct() {
        // Cache & replica read activated in this controller
        $this->dbReplica(true);
        $this->dbCache(true);
        View::setTheme('responsive');
    }

    public function indexAction (Request $request, string $section='', string $tag='' ): Response
    {
        $faq_sections=FaqSection::getList();

        return $this->viewResponse('faq/index', [
                    'meta_title' => Text::get('faq-meta-title'),
                    'meta_description' => Text::get('faq-meta-description'),
                    'faq_sections' => $faq_sections
                ]
        );
    }

    public function searchAction(Request $request): Response
    {
        $search = $request->query->getAlnum('search');

        $faqsCount = Faq::getListCount(['search' => $search]);
        $faqs = Faq::getList(['search' => $search], 0, $faqsCount, false, Lang::current());

        return $this->viewResponse('faq/search', [
            'faqs' => $faqs
        ]);
    }

    public function sectionAction(Request $request, string $section): Response
    {
        $faq_section = FaqSection::getBySlug($section);
        $subsections = FaqSubsection::getList(['section' => $faq_section->id]);

        return $this->viewResponse('faq/section', [
            'meta_title' => $faq_section->name.' :: Faq',
            'meta_description' => Text::get('faq-meta-description'),
            'faq_section' => $faq_section,
            'subsections' => $subsections
        ]);
    }

    public function individualAction(Request $request, string $faq): Response
    {
        $faq = Faq::getBySlug($faq);
        $faq_subsection = FaqSubsection::get($faq->subsection_id);
        $faq_section = FaqSection::getById($faq_subsection->section_id);

        // Sidebar menu
        $subsections = FaqSubsection::getList(['section' => $faq_section->id]);

        return $this->viewResponse('faq/individual', [
            'meta_title' => $faq->title.' :: Faq',
            'meta_description' => Text::get('faq-meta-description'),
            'faq' => $faq,
            'faq_section' => $faq_section,
            'subsections' => $subsections
        ]);
    }

}


