<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Util\Pagination;

use Goteo\Library\Text;

//DoubleBarLayout.php
class DoubleBarLayout implements PageLayoutInterface {

	public function fetchPagedLinks($parent, $queryVars) {

		$currentPage = $parent->getPageNumber();
		$str = "";

		if(!$parent->isFirstPage()) {
			if($currentPage != 1 && $currentPage != 2 && $currentPage != 3 && $currentPage != 4) {
					$str .= "<li><a href='?page=1$queryVars' title='".Text::get('regular-first')."'>".Text::get('regular-first')."</a></li><li class='hellip'>&hellip; </li>";
			}
		}

		//write statement that handles the previous and next phases
	   	//if it is not the first page then write previous to the screen
		if(!$parent->isFirstPage()) {
			$previousPage = $currentPage - 1;
			$str .= "<li><a href=\"?page=$previousPage$queryVars\"><  </a> </li>";
		}

		for($i = $currentPage - 3; $i <= $currentPage + 3; $i++) {
			//if i is less than one then continue to next iteration
			if($i < 1) {
				continue;
			}

			if($i > $parent->fetchNumberPages() || $parent->fetchNumberPages()==1) {
				break;
			}
			if($i == $currentPage) {
				$str .= "<li class='selected'>$i</li> ";
			}
			else {
				$str .= "<li><a href=\"?page=$i$queryVars\">$i</a></li> ";
			}
		}//end for

		if(!$parent->isLastPage()) {
			$nextPage = $currentPage + 1;
			$str .= "<li><a href=\"?page=$nextPage$queryVars\"> ></a></li>";
		}

		if (!$parent->isLastPage()) {
			if($currentPage != $parent->fetchNumberPages() && $currentPage != $parent->fetchNumberPages() -1 && $currentPage != $parent->fetchNumberPages() - 2 && $currentPage != $parent->fetchNumberPages() - 3)
			{
				$str .= " <li class='hellip'>&hellip;</li><li><a href=\"?page=".$parent->fetchNumberPages()."$queryVars\" title=\"".Text::get('regular-last')."\">".Text::get('regular-last')."(".$parent->fetchNumberPages().") </a></li>";
			}
		}
		return $str;
	}
}
