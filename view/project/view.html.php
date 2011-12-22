<?php

use Goteo\Core\View,
    Goteo\Model\User,
    Goteo\Model\Project\Cost,
    Goteo\Model\Project\Support,
    Goteo\Model\Project\Category,
    Goteo\Model\Blog,
    Goteo\Library\Text;

$project = $this['project'];
$show    = $this['show'];
$invest  = $this['invest'];
$post    = $this['post'];

$owner   = User::get($project->owner, LANG);
$user    = $_SESSION['user'];

$categories = Category::getNames($project->id);

$blog = Blog::get($project->id);


if (!empty($project->investors)) {
    $supporters = ' (' . count($project->investors) . ')';
} else {
    $supporters = '';
}
if (!empty($project->messages)) {
    $messages = ' (' . count($project->messages) . ')';
} else {
    $messages = '';
}
if (!empty($blog->posts)) {
    $updates = ' (' . count($blog->posts) . ')';
} else {
    $updates = '';
}



$bodyClass = 'project-show';

// metas og: para que al compartir en facebook coja bien el nombre y la imagen
$ogmeta = array(
    'title' => $project->name,
    'description' => Text::get('regular-by').' '.$project->user->name,
    'image' => SITE_URL . '/image/'.$project->gallery[0]->id . '/580/580',
    'url' => SITE_URL . '/project/'.$project->id
);
include 'view/prologue.html.php' ?>

<?php include 'view/header.html.php' ?>

        <div id="sub-header">
            <div class="project-header">
                <a href="/user/<?php echo $project->owner; ?>"><img src="<?php echo $project->user->avatar->getLink(56,56, true) ?>" /></a>
                <h2><span><?php echo htmlspecialchars($project->name) ?></span></h2>
                <div class="project-subtitle"><?php echo htmlspecialchars($project->subtitle) ?></div>
                <div class="project-by"><a href="/user/<?php echo $project->owner; ?>"><?php echo Text::get('regular-by') ?> <?php echo $project->user->name; ?></a></div>
                <br clear="both" />

                <div class="categories"><h3><?php echo Text::get('project-view-categories-title'); ?></h3>
                    <?php $sep = ''; foreach ($categories as $key=>$value) :
                        echo $sep.'<a href="/discover/results/'.$key.'">'.htmlspecialchars($value).'</a>';
                    $sep = ', '; endforeach; ?>
                </div>
            </div>

            <div class="sub-menu">
                <?php echo new View('view/project/view/menu.html.php',
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

<?php if(isset($_SESSION['messages'])) { include 'view/header/message.html.php'; } ?>


        <div id="main" class="threecols">

            <div class="side">
            <?php
            // el lateral es diferente segun el show (y el invest)
            echo
                new View('view/project/widget/support.html.php', array('project' => $project));

            if ((!empty($project->investors) &&
                !empty($invest) &&
                in_array($invest, array('start', 'ok', 'fail')) )
                || $show == 'messages' ) {
                echo new View('view/project/widget/investors.html.php', array('project' => $project));
            }

            if (!empty($project->supports)) {
                echo new View('view/project/widget/collaborations.html.php', array('project' => $project));
            }

            if ($show != 'rewards' && $show != 'messages') {
                echo new View('view/project/widget/rewards.html.php', array('project' => $project));
            }

            echo new View('view/user/widget/user.html.php', array('user' => $owner));

            ?>
            </div>

            <?php $printSendMsg = false; ?>
            <div class="center">
			<?php
                // los modulos centrales son diferentes segun el show
                switch ($show) {
                    case 'needs':
                        if ($this['non-economic']) {
                            echo
                                new View('view/project/widget/non-needs.html.php',
                                    array('project' => $project, 'types' => Support::types()));
                        } else {
                        echo
                            new View('view/project/widget/needs.html.php', array('project' => $project, 'types' => Cost::types())),
                            new View('view/project/widget/schedule.html.php', array('project' => $project)),
                            new View('view/project/widget/sendMsg.html.php', array('project' => $project));
                        }
                        break;
						
                    case 'supporters':

						// segun el paso de aporte
                        if (!empty($invest) && in_array($invest, array('start', 'ok', 'fail'))) {

                            switch ($invest) {
                                case 'start':
                                    echo
                                        new View('view/project/widget/investMsg.html.php', array('message' => $invest, 'user' => $user)),
                                        new View('view/project/widget/invest.html.php', array('project' => $project, 'personal' => User::getPersonal($user->id)));
                                    break;
                                case 'continue':
                                    echo
                                        new View('view/project/widget/investMsg.html.php', array('message' => $invest, 'user' => $user)),
                                        new View('view/project/widget/invest_redirect.html.php', array('project' => $project, 'personal' => User::getPersonal($user->id)));
                                    break;
									
                                case 'ok':
                                    echo
                                        new View('view/project/widget/investMsg.html.php', array('message' => $invest, 'user' => $user)), new View('view/project/widget/spread.html.php',array('project' => $project));
										//sacarlo de div#center
										$printSendMsg=true;										
                                    break;
									
                                case 'fail':
                                    echo
                                        new View('view/project/widget/investMsg.html.php', array('message' => $invest, 'user' => User::get($_SESSION['user']->id))),
                                        new View('view/project/widget/invest.html.php', array('project' => $project, 'personal' => User::getPersonal($_SESSION['user']->id)));
                                    break;
                            }
                        } else {
                            echo
                                new View('view/project/widget/supporters.html.php', array('project' => $project)),
                                new View('view/worth/legend.html.php');
                        }
                        break;
						
                    case 'messages':
                        echo
                            new View('view/project/widget/messages.html.php', array('project' => $project));
                        break;
                   
				    case 'rewards':
                        echo
                            new View('view/project/widget/rewards-summary.html.php', array('project' => $project));
                        break;
                    
					case 'updates':
                        echo
                            new View('view/project/widget/updates.html.php', array('project' => $project, 'blog' => $blog, 'post' => $post));
                        break;
                    
					case 'home':
					
                    default:
                        if (!empty($project->media->url)) {
                            echo new View('view/project/widget/media.html.php', array('project' => $project));
                        }
                        echo
                            new View('view/project/widget/share.html.php', array('project' => $project)),
                            new View('view/project/widget/summary.html.php', array('project' => $project));
                        break;
                }
                ?>
             </div>

			<?php
				if($printSendMsg){
					 echo new View('view/project/widget/sendMsg.html.php',array('project' => $project));
				}
            ?>

        </div>

        <?php include 'view/footer.html.php' ?>
		<?php include 'view/epilogue.html.php' ?>
