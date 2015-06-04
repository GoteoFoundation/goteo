<?php

use Goteo\Core\View,
    Goteo\Model\User,
    Goteo\Model\Image,
    Goteo\Model\Project\Cost,
    Goteo\Model\Project\Support,
    Goteo\Model\Project\Category,
    Goteo\Model\Blog,
    Goteo\Library\Text;

$project = $this->project;

$show    = $this->show;
$step    = $this->step;
$post    = $this->post;
$blog    = $this->blog;

$user    = $_SESSION['user'];
$personalData = ($user instanceof User) ? User::getPersonal($user->id) : new stdClass();

if (!empty($project->num_investors)) {
    $supporters = ' (' . $project->num_investors . ')';
} else {
    $supporters = '';
}
if (!empty($project->num_messengers)) {
    $messages = ' (' . $project->num_messengers . ')';
} else {
    $messages = '';
}
if (!empty($project->num_posts)) {
    $updates = ' (' . $project->num_posts . ')';
} else {
    $updates = '';
}

$URL = \SITE_URL;

// metas og: para que al compartir en facebook coja bien el nombre y la imagen (todas las de proyecto y las novedades
$title = $project->name;
$url = $URL . '/project/'.$project->id;
$images = array();
if ($show == 'updates') {
    $url .= '/updates';
    if (!empty($post)) {
        $url .= '/'.$post;
        $tpost = $blog->posts[$post];
        $title .= (empty($tpost->title)) ?: ' - '.$tpost->title;
    }
}

// todas las imagenes del proyecto
if (is_array($project->gallery)) {
    foreach ($project->gallery as $pgimg) {
        if ($pgimg->imageData instanceof Image) {
            $images[] = $pgimg->imageData->getLink(580, 580, false, true);
        }
    }
}

foreach ($blog->posts as $bpost) {
    if (is_array($bpost->gallery)) {
        foreach ($bpost->gallery as $bpimg) {
            if ($bpimg instanceof Image) {
                $images[] = $bpimg->getLink(500, 285, false, true);
            }
        }
    }
}

$this->layout("layout", [
    'bodyClass' => 'project-show',
    'title' => $title,
    'meta_description' => $project->subtitle,
    'og_description' => $project->subtitle ."\n". $this->text('regular-by').' '.$project->user->name,
    'url' => $url,
    'image' => $images
    ]);

$this->section('content');

?>


        <div id="sub-header">
            <div class="project-header">
                <a href="/user/<?php echo $project->owner; ?>"><img src="<?php echo $project->user->avatar->getLink(56,56, true) ?>" /></a>
                <h2><span><?php echo htmlspecialchars($project->name) ?></span></h2>
                <div class="project-subtitle"><?php echo htmlspecialchars($project->subtitle) ?></div>
                <div class="project-by"><a href="/user/<?php echo $project->owner; ?>"><?= $this->text('regular-by') ?> <?php echo $project->user->name; ?></a></div>
                <div class="project-by"><a href="/user/<?php echo $project->owner; ?>"><?=  $this->text('regular-by') ?> <?php echo $project->user->name; ?></a></div>
                <br/>

                <?php if (!empty($project->cat_names)) : ?>
                <div class="categories"><h3><?= $this->text('project-view-categories-title') ?></h3>
                    <?php $sep = ''; foreach ($project->cat_names as $key=>$value) :
                        echo $sep.'<a href="/discover/results/'.$key.'/'.$value.'">'.htmlspecialchars($value).'</a>';
                    $sep = ', '; endforeach; ?>
                </div>
                <?php endif; ?>

                <?php if (!empty($project->node) && $project->node != \NODE_ID) : ?>
                <div class="nodemark"><a class="node-jump" href="<?php echo $project->nodeData->url ?>" ><img src ="/nodesys/<?php echo $project->node ?>/sello.png" alt="<?php echo htmlspecialchars($project->nodeData->name) ?>" title="Nodo <?php echo htmlspecialchars($project->nodeData->name) ?>"/></a></div>
                <?php endif; ?>
            </div>

            <div class="sub-menu">
                <?php echo View::get('project/view/menu.html.php',
                            array(
                                'project' => $project,
                                'show' => $show,
                                'supporters' => $supporters,
                                'messages' => $messages,
                                'updates' => $updates
                            )
                    );
                ?>
            </div>

        </div>

<?php if($_SESSION['messages']) { include __DIR__ . '/../header/message.html.php'; } ?>


        <div id="main" class="threecols">

            <div class="side">
            <?php
            // el lateral es diferente segun el show (y el invest)
            echo
                View::get('project/widget/support.html.php', array('project' => $project));

            // seleccionado para capital riego
            if ($project->called) {
                echo View::get('project/widget/called.html.php', array('project' => $project));
            }

            /*if ((!empty($project->investors) &&
                !empty($step) &&
                in_array($step, array('start', 'login', 'confirm', 'continue', 'ok', 'fail')) )
                || $show == 'messages' ) {
                echo View::get('project/widget/investors.html.php', array('project' => $project));
            }*/

            if ($project->status == 5 && $show != 'rewards' && $show != 'messages') {
                echo View::get('project/widget/rewards.html.php', array('project' => $project, 'only'=>'social'));
            }

            if (!empty($project->supports)) {
                echo View::get('project/widget/collaborations.html.php', array('project' => $project));
            }

            if ($show != 'rewards' && $show != 'messages') {
                $only_rew = ($project->status == 5) ? 'individual' : null;
                $only_rew = (in_array($step, array('start', 'login', 'confirm', 'continue', 'ok', 'fail'))) ? 'social' : $only_rew;
                echo View::get('project/widget/rewards.html.php', array('project' => $project, 'only'=>$only_rew));
            }

            echo View::get('user/widget/user.html.php', array('user' => $project->user));

            ?>
            </div>

            <?php $printSendMsg = false; ?>
            <div class="center">
			<?php
                // los modulos centrales son diferentes segun el show
                switch ($show) {
                    case 'needs':
                        if ($this->non-economic) {
                            echo
                                View::get('project/widget/non-needs.html.php',
                                    array('project' => $project, 'types' => Support::types()));
                        } else {
                        echo
                            View::get('project/widget/needs.html.php', array('project' => $project, 'types' => Cost::types())),
                            View::get('project/widget/schedule.html.php', array('project' => $project)),
                            View::get('project/widget/sendMsg.html.php', array('project' => $project));
                        }
                        break;

                    case 'supporters':

						// segun el paso de aporte
                        if (!empty($step) && in_array($step, array('start', 'login', 'confirm', 'continue', 'ok', 'fail'))) {

                            // variables a pasar a las subvistas
                            $subView = array('project' => $project, 'personal' => $personalData, 'step' => $step, 'allowpp'=> $this->allowpp, 'pool'=> $this->pool);

                            switch ($step) {
                                case 'continue':
                                    echo
                                        View::get('project/widget/investMsg.html.php', array('message' => $step, 'user' => $user)),
                                        View::get('project/widget/invest_redirect.html.php', $subView);
                                    break;

                                case 'ok':
                                    echo
                                        View::get('project/widget/investMsg.html.php', array('message' => $step, 'user' => $user)), View::get('project/widget/spread.html.php',array('project' => $project));
										//sacarlo de div#center
										$printSendMsg=true;
                                    break;

                                case 'fail':
                                    echo
                                        View::get('project/widget/investMsg.html.php', array('message' => $step, 'user' => $user)),
                                        View::get('project/widget/invest.html.php', $subView);
                                    break;
                                default:
                                    echo
                                        View::get('project/widget/investMsg.html.php', array('message' => $step, 'user' => $user)),
                                        View::get('project/widget/invest.html.php', $subView);
                                    break;
                            }
                        } else {
                            echo
                                View::get('project/widget/supporters.html.php', (array)$this),
                                View::get('worth/legend.html.php');
                        }
                        break;

                    case 'messages':
                        echo
                            View::get('project/widget/messages.html.php', array('project' => $project));
                        break;

				    case 'rewards':
                        if ($project->status == 5) {
                            echo View::get('project/widget/rewards-summary.html.php', array('project' => $project, 'only'=>'social'));
                            echo View::get('project/widget/rewards-summary.html.php', array('project' => $project, 'only'=>'individual'));
                        } else {
                            echo View::get('project/widget/rewards-summary.html.php', array('project' => $project));
                        }
                        break;

					case 'updates':
                        echo
                            View::get('project/widget/updates.html.php', array('project' => $project, 'blog' => $blog, 'post' => $post));
                        break;

					case 'home':

                    default:
                        echo View::get('project/widget/media.html.php', array('project' => $project));

                        echo View::get('project/widget/langs.html.php', array('project' => $project));

                        echo View::get('project/widget/share.html.php', array('project' => $project));
                        if (!empty($project->patrons)) {
                            echo View::get('project/widget/patrons.html.php', array('patrons' => $project->patrons));
                        }

                        echo View::get('project/widget/summary.html.php', array('project' => $project));

                        // wall of friends, condicional
                        if (round(($project->invested / $project->mincost) * 100) > 20) {
                            echo View::get('project/widget/wof.html.php', array('project' => $project));
                        }

                        break;
                }
                ?>
             </div>

			<?php
				if($printSendMsg){
					 echo View::get('project/widget/sendMsg.html.php',array('project' => $project));
				}
            ?>

        </div>

<?php $this->replace() ?>
