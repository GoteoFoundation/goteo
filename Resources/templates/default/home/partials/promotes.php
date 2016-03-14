<?php
use Goteo\Core\View;

$promotes = $this->promotes;
// random y que solo pinte seis si hubiera más
if (count($promotes) > 6) {
	shuffle($promotes);
	$promotes = array_slice($promotes, 0, 6);
}
?>

<div class="widget projects">

    <h2 class="title"><?php echo $this->text('home-promotes-header'); ?></h2>

    <?php foreach ($promotes as $promo) : ?>

            <?php echo View::get('project/widget/project.html.php', array(
                'project' => $promo->projectData,
                'balloon' => '<h4>' . $promo->title . '</h4>' .
                             '<blockquote>' . $promo->promo_text . '</blockquote>'
            )) ?>

    <?php endforeach ?>

</div>

