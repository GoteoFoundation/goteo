<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */
namespace Goteo\Library {

	use Goteo\Core\Model;
  use Goteo\Core\Exception;
  use Goteo\Model\Call;
  use Goteo\Model\Matcher;
        
	/*
   * Class to order a list of elements from differents classes based on different fields
	 */
    class Ordering {

      public static function orderCallsMatchers($all = array()) {

        uasort($all,  function ($a, $b) {
          if ($a instanceOf Matcher) {
              if ($b instanceOf Matcher) {
                  return ($a->created < $b->created) ? 1 : -1;
              } else if ($b instanceOf Call) {
                  return ($a->created < $b->opened) ? 1 : -1;
              }
          } else if ($a instanceOf Call) {
              if ($b instanceOf Matcher) {
                  return ($a->opened < $b->created) ? 1 : -1;
              } else if ($b instanceOf Call) {
                  return ($a->opened < $b->opened) ? 1 : -1;
              }
          } else {
              return 0;
          }
        });

        return $all;
      }
	}
}