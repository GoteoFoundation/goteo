<?php
use Goteo\Core\View,
    Goteo\Library\Text;

$promotes = $this['promotes'];
// random si más de 6
if (count($promotes) > 6) shuffle($promotes);
?>
<div class="widget projects">

    <h2 class="title"><?php echo Text::get('home-promotes-header'); ?></h2>

    <?php foreach ($promotes as $promo) : ?>

            <?php echo new View('view/project/widget/project.html.php', array(
                'project' => $promo->projectData,
                'balloon' => '<h4>' . htmlspecialchars($promo->title) . '</h4>' .
                             '<blockquote>' . $promo->description . '</blockquote>'
            )) ?>

    <?php endforeach ?>

</div>