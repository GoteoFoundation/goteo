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

if($blog) {
    foreach ($blog->posts as $bpost) {
        if (is_array($bpost->gallery)) {
            foreach ($bpost->gallery as $bpimg) {
                if ($bpimg instanceof Image) {
                    $images[] = $bpimg->getLink(500, 285, false, true);
                }
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
                <div class="by-tags">
                <div class="project-subtitle"><?php echo htmlspecialchars($project->subtitle) ?></div>
                    <div class="project-by"><a href="/user/<?php echo $project->owner; ?>"><?= $this->text('regular-by') ?> <?php echo $project->user->name; ?></a></div>
                    <?php if (!empty($project->cat_names)) : ?>
                        <div class="project-tags">
                        <?php $sep = '';
                        foreach ($project->cat_names as $key=>$value) :
                            echo $sep.'<a href="/discover/results/'.$key.'/'.$value.'">'.htmlspecialchars($value).'</a>';
                            $sep = ', ';
                        endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <br/>

                <?php if ($project->node !== $this->get_config('current_node')&&($project->nodeData->active)) : ?>
                <div class="nodemark">
                    <?php if($project->nodeData->type!="node")
                    {
                        echo '<h4 class="label-title">'.$this->text('regular-channel').'</h4>';
                        $project->nodeData->url=$URL.'/channel/'.$project->nodeData->id;
                    }
                    ?>
                    <a class="node-jump" href="<?php echo $project->nodeData->url ?>" >
                        <img src ="<?= $project->nodeData->label->getLink(100,100) ?>" alt="<?php echo htmlspecialchars($project->nodeData->name) ?>" title="<?php echo htmlspecialchars($project->nodeData->name) ?>"/>
                    </a>
                </div>
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


        <!-- TODO: HEADER -->

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

            if ($project->status == 5 && $show != 'rewards' && $show != 'messages') {
                echo View::get('project/widget/rewards.html.php', array('project' => $project, 'only'=>'social'));
            }

            if (!empty($project->supports)) {
                echo View::get('project/widget/collaborations.html.php', array('project' => $project));
            }

            if ($show != 'rewards' && $show != 'messages') {
                $only_rew = ($project->status == 5) ? 'individual' : null;
                echo View::get('project/widget/rewards.html.php', array('project' => $project, 'only'=>$only_rew));
            }

            echo View::get('user/widget/user.html.php', array('user' => $project->getOwner()));

            ?>
            </div>

            <?php $printSendMsg = false; ?>
            <div class="center">
			<?php
                // los modulos centrales son diferentes segun el show
                switch ($show) {
                    case 'needs':
                        if ($this->non_economic) {
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
                            echo
                                View::get('project/widget/supporters.html.php', $this->vars),
                                View::get('worth/legend.html.php');
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
