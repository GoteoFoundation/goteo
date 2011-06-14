<?php 

use Goteo\Core\View,
    Goteo\Model\User,
    Goteo\Model\Project\Cost,
    Goteo\Model\Project\Support,
    Goteo\Model\Project\Category,
    Goteo\Model\Blog;

$project = $this['project'];
$show    = $this['show'];
$invest  = $this['invest'];
$post    = $this['post'];

$owner   = User::get($project->owner);
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



$bodyClass = 'project-show'; include 'view/prologue.html.php' ?>

        <?php include 'view/header.html.php' ?>

        <div id="sub-header">
            <div>
                <h2><?php echo htmlspecialchars($project->name) ?></h2>
                
                <div class="categories"><h3>Categorias:</h3> 
                    <?php 
                    $i = 0;  
                    foreach ($categories as $cat) {
                        if ($i++ > 0) echo ', ';
                        // @todo Enlaces en los nombres de las categorías?
                        echo htmlspecialchars($cat);                        
                    }
                    ?>
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

        

        <div id="main" class="threecols">
            
            <div class="side">
            <?php
            // el lateral es diferente segun el show (y el invest)
            echo
                new View('view/project/widget/support.html.php', array('project' => $project));

            if (!empty($project->investors) && 
                !empty($invest) &&
                in_array($invest, array('start', 'ok', 'fail')) ) {

                echo new View('view/project/widget/investors.html.php', array('project' => $project));
            }
            
            if (!empty($project->supports)) {
                echo new View('view/project/widget/collaborations.html.php', array('project' => $project));
            }

            echo
                new View('view/project/widget/rewards.html.php', array('project' => $project)),
                new View('view/user/widget/user.html.php', array('user' => $owner));
            
            ?>                
            </div>
            
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
                                new View('view/project/widget/needs.html.php',
                                    array('project' => $project, 'types' => Cost::types())),
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
                                case 'ok':
                                    echo
                                        new View('view/project/widget/investMsg.html.php', array('message' => $invest, 'user' => $user)),
                                        new View('view/project/widget/spread.html.php',array('project' => $project)),
                                        new View('view/project/widget/sendMsg.html.php',array('project' => $project));
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
                        echo
                            new View('view/project/widget/media.html.php', array('project' => $project)),
                            new View('view/project/widget/share.html.php', array('project' => $project)),
                            new View('view/project/widget/summary.html.php', array('project' => $project));
                        break;
                }
                ?>
            </div>
                                    
            
            
        </div>        

        <?php include 'view/footer.html.php' ?>
    
<?php include 'view/epilogue.html.php' ?>