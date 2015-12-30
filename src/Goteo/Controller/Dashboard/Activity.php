<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller\Dashboard {

    use Goteo\Model,
        Goteo\Core\Redirection,
        Goteo\Application\Message,
		Goteo\Application\Session,
        Goteo\Library\FileHandler\File,
        Goteo\Library\Text,
		Goteo\Library\Check,
        Goteo\Library\Listing,
        Goteo\Library\PDF;

    class Activity {

        // listados de proyectos a mostrar (proyectos que cofinancia y proyectos suyos)
        public static function projList ($user) {
            $lists = array();
            // mis proyectos
            $projects = Model\Project::ofmine($user->id);
            if (!empty($projects)) {
                $lists['my_projects'] = Listing::get($projects);
            }
            // proyectos que cofinancio
            $invested = Model\User::invested($user->id, false);
            if (!empty($invested)) {
                $lists['invest_on'] = Listing::get($invested);
            }

            //proyectos que coinciden con mis intereses
            $favourite_categories = Model\Project::favouriteCategories($user->id);
            if (!empty($favourite_categories)) {
                $lists['favourite_categories'] = Listing::get($favourite_categories);
            }

            return $lists;
        }

    }

}
