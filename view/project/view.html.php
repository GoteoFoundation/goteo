<?php 

use Goteo\Core\View,
    Goteo\Model\User;

$project = $this['project'];
$user    = User::get($project->owner);

$bodyClass = 'project-show'; include 'view/prologue.html.php' ?>

        <?php include 'view/header.html.php' ?>

        <div id="sub-header">
            <div>
                <h2><?php echo htmlspecialchars($this['project']->name) ?></h2>
            </div>
        </div>

        <?php echo new View('view/project/view/menu.html.php', array('project' => $project)) ?>

        <div id="main" class="threecols">
            
            <div class="side">
            <?php             
            echo
                new View('view/project/widget/support.html.php'),                 
                new View('view/user/widget/user.html.php', array('user' => $user));
            
            ?>                
            </div>
            
            <div class="center">
                <?php             
                echo
                    new View('view/project/widget/media.html.php', array('project' => $project)),
                    new View('view/project/widget/share.html.php', array('project' => $project)),
                    new View('view/project/widget/summary.html.php', array('project' => $project));                    
                ?>
            </div>
                                    
            
            
        </div>        

        <?php include 'view/footer.html.php' ?>
    
<?php include 'view/epilogue.html.php' ?>