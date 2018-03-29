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

use Goteo\Model\Category;

class CategoriesApiController extends AbstractApiController {

    public function __construct() {
        parent::__construct();
        // Activate cache & replica read for this controller
        $this->dbReplica(true);
        $this->dbCache(true);
    }


    /**
     * Returns a list of keywords for suggestions
     */
    public function keywordsAction(Request $request) {
        $keywords = array_map(function($el) {
            return ['tag' => $el];
        }, Category::getKeywords($request->query->get('q')));

        return $this->jsonResponse($keywords);
    }

}
