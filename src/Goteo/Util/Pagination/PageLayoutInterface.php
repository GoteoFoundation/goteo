<?php

namespace Goteo\Util\Pagination;

/**
 * The interface which specifies the behaviour all page layout classes must implement
 * PageLayout is a part of Paginated and can reference programmer defined layouts
 */
interface PageLayoutInterface {
	public function fetchPagedLinks($parent, $queryVars);
}

