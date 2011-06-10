<?php
use Goteo\Core\View;

echo new View ('view/dashboard/projects/selector.html.php', $this);

$url = SITE_URL . '/widget/project/' . $_SESSION['project']->id;


$widget_code = '<iframe frameborder="0" height="380px" src="'.$url.'" width="200px"></iframe>';

?>
<div class="widget projects">
    <h2 class="title">Comparte tu proyecto</h2>
    <div>
        <?php
        // el proyecto de trabajo
        echo new View('view/project/widget/project.html.php', array(
            'project'   => $this['project']
        )); ?>
    </div>
    
    <div id="widget-code">
        Código: 
        <blockquote>
        <?php echo htmlentities($widget_code); ?>       
        </blockquote>
        <br />
        Preview: <a href="<?php echo $url; ?>" target="_blank">Preview</a>
    </div>

</div>