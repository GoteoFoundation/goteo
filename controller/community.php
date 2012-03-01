<?php

namespace Goteo\Controller {

    use Goteo\Library\Page,
        Goteo\Library\Feed,
        Goteo\Core\View,
        Goteo\Model\User\Interest,
        Goteo\Model\Invest;

    class Community extends \Goteo\Core\Controller {

        public function index ($show = 'activity', $category = null) {

            $page = Page::get('community');

            $items = array();
            $shares = array();

            if (!in_array($show, array('sharemates', 'activity'))) $show = 'activity';

            $viewData = array(
                    'description' => $page->description,
                    'show' => $show
                );

            switch ($show) {

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

                // feed público
                case 'activity':
                    
                    $items = array();

                    $items['goteo']     = Feed::getAll('goteo', 'public', 50);
                    $items['projects']  = Feed::getAll('projects', 'public', 50);
                    $items['community'] = Feed::getAll('community', 'public', 50);

                    $viewData['items'] = $items;

                    break;
            }

            return new View('view/community.html.php', $viewData);

        }

    }

}