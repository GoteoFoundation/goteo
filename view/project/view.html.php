<?php 

use Goteo\Core\View,
    Goteo\Model\User,
    Goteo\Model\Project\Cost,
    Goteo\Library\Worth;

$project = $this['project'];
$show    = $this['show'];
$invest  = $this['invest'];

$owner   = User::get($project->owner);
$user    = $_SESSION['user'];

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



$bodyClass = 'project-show'; include 'view/prologue.html.php' ?>

        <?php include 'view/header.html.php' ?>

        <div id="sub-header">
            <div>
                <h2><?php echo htmlspecialchars($this['project']->name) ?></h2>
            </div>
            
            <div class="sub-menu">
                <?php echo new View('view/project/view/menu.html.php', array(
                                        'project' => $project,
                                        'show' => $show,
                                        'supporters' => $supporters, 
                                        'messages' => $messages)); 
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
                in_array($invest, array('start', 'ok', 'fail'))) {

                echo new View('view/project/widget/investors.html.php',
                    array('project' => $project, 'worthcracy' => Worth::getAll()));
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
                        echo
                            new View('view/project/widget/needs.html.php', 
                                array('project' => $project, 'types' => Cost::types())),
                            new View('view/project/widget/sendMsg.html.php', array('project' => $project));
                        break;
                    case 'supporters':
                        // segun el paso de aporte
                        if (!empty($invest) && in_array($invest, array('start', 'ok', 'fail'))) {

                            switch ($invest) {
                                case 'start':
                                    echo 
                                        new View('view/project/widget/investMsg.html.php', array('message' => $invest, 'user' => $user)),
                                        new View('view/project/widget/invest.html.php',
                                            array('project' => $project, 'worthcracy' => Worth::getAll(), 'personal' => User::getPersonal($user->id)));
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
                                        new View('view/project/widget/invest.html.php',
                                            array('project' => $project, 'worthcracy' => Worth::getAll(), 'personal' => User::getPersonal($_SESSION['user']->id)));
                                    break;
                            }
                        } else {
                            echo
                                new View('view/project/widget/supporters.html.php',
                                    array('project' => $project, 'worthcracy' => Worth::getAll()));
                        }
                        break;
                    case 'messages':
                        echo
                            new View('view/project/widget/messages.html.php', array('project' => $project));
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