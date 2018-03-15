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
        Goteo\Library\Feed,
        Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Model\User\Interest,
        Goteo\Model\Invest;

    class Community extends \Goteo\Core\Controller {

        public function __construct() {
            // Cache & replica read activated in this controller
            \Goteo\Core\DB::cache(true);
            \Goteo\Core\DB::replica(true);
        }

        public function index ($show = 'activity', $category = null) {

            $page = Page::get('community');

            $items = array();
            $shares = array();

            // Deshabilitamos el Compartiendo - 04/07/2013
            if ($show != 'activity') {
                throw new Redirection('/community');
            }

            $viewData = array(
                    'description' => $page->description,
                    'show' => $show
                );

            switch ($show) {

                /*
                // compartiendo intereses global
                case 'sharemates':
                    $categories = Interest::getAll();

                    foreach ($categories as $catId => $catName) {
                        $gente = Interest::shareAll($catId);
                        if (count($gente) == 0) continue;
                        $shares[$catId] = $gente;
                    }

                    $viewData['category'] = $category;
                    $viewData['categories'] = $categories;
                    $viewData['shares'] = $shares;

                    // top ten cofinanciadores en Goteo
                    $projects = Invest::projects(true);

                    $investors = array();
                    foreach ($projects as $projectId=>$projectName) {

                        foreach (Invest::investors($projectId) as $key=>$investor) {
                            // convocadores no, gracias
                            if (!empty($investor->campaign)) continue;
                            // estos dos tampoco
                            if (in_array($investor->user, array('aportaciones', 'colectivoafinidadrebelde'))) continue;

                            if (\array_key_exists($investor->user, $investors)) {
                                // si es otro proyecto y ya está en el array, añadir uno
                                if ($investors[$investor->user]->lastproject != $projectId) {
                                    ++$investors[$investor->user]->projects;
                                    $investors[$investor->user]->lastproject = $projectId;
                                }
                                $investors[$investor->user]->amount += $investor->amount;
                                $investors[$investor->user]->date = $investor->date;
                            } else {
                                $investors[$investor->user] = (object) array(
                                    'user' => $investor->user,
                                    'name' => $investor->name,
                                    'projects' => 1,
                                    'lastproject' => $projectId,
                                    'avatar' => $investor->avatar,
                                    'worth' => $investor->worth,
                                    'amount' => $investor->amount,
                                    'date' => $investor->date
                                );
                            }
                        }
                    }

                    $viewData['investors'] = $investors;

                    break;
*/
                // feed público
                case 'activity':

                    $items = array();

                    $items['goteo']     = Feed::getAll('goteo', 'public', 50);
                    $items['projects']  = Feed::getAll('projects', 'public', 50);
                    $items['community'] = Feed::getAll('community', 'public', 50);

                    $viewData['items'] = $items;

                    break;
            }

            return new View('community.html.php', $viewData);

        }

    }

}
