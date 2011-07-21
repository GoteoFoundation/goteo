<?php
use Goteo\Core\View,
    Goteo\Library\Text;

$user    = $_SESSION['user'];
$project = $this['project'];
$level = (int) $this['level'] ?: 3;

$url = SITE_URL . '/widget/project/' . $project->id;
$widget_code = '<iframe frameborder="0" height="380px" src="'.$url.'" width="250px"></iframe>';
$widget_code_investor = '<iframe frameborder="0" height="380px" src="'.$url.'/invested/'.$user->id.'" width="250px"></iframe>';

?>
<div class="widget project-spread">
    
    <h<?php echo $level ?>><?php echo Text::get('project-spread-header'); ?></h<?php echo $level ?>>
    <p>
        <?php echo Text::get('project-spread-widget'); ?>
        &nbsp;&nbsp;&nbsp;
        <?php echo Text::get('project-share-header'); ?>
    </p>


    <div class="widget projects">
            <?php
                // el proyecto de trabajo
                echo new View('view/project/widget/project.html.php', array(
                'project'   => $project,
                'balloon'   =>  '<blockquote class="embed-code">' . htmlentities($widget_code) . '</blockquote>'
                ));
            ?>
            <?php
                // el proyecto de trabajo
                echo new View('view/project/widget/project.html.php', array(
                'project'   => $project,
                'investor'  => $user,
                'balloon'   =>  '<blockquote class="embed-code">' . htmlentities($widget_code) . '</blockquote>'
                ));
            ?>
    </div>

    
    
</div>