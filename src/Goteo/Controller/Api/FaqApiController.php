<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Goteo\Application\Exception\ControllerAccessDeniedException;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Message;
use Goteo\Application\AppEvents;
use Goteo\Application\Config;

use Goteo\Model\Faq;
use Goteo\Library\Text;
use Goteo\Library\Check;

class FaqApiController extends AbstractApiController {

    protected function validateFaq($id) {

        if(!$this->user)
            throw new ControllerAccessDeniedException();

        $faq = $id ? Faq::get($id) : new Faq();
        
        if($this->user->hasPerm('admin-module-faqs') ) {
            return $faq;
        }

        throw new ControllerAccessDeniedException(Text::get('admin-faq-not-active-yet'));
    }
    
    public function faqSortAction($id, Request $request) {
        $faq = $this->validateFaq($id);

        $result = ['value' => (int)$faq->order, 'error' => false];

        if($request->isMethod('put') && $request->request->has('value')) {

            $res = Check::reorder($id, $request->request->get('value'), 'faq', 'id', 'order', ['subsection_id' => $faq->subsection_id]);

            if($res != $result['value']) {
                $result['value'] = $res;
            } else {
                $result['error'] = true;
                $result['message'] = 'Sorting failed';
            }
        }

        return $this->jsonResponse($result);
    }

}