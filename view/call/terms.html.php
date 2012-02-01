<?php

use Goteo\Library\Page,
    Goteo\Library\Text,
    Goteo\Core\View;

$bodyClass = 'terms';

$call = $this['call'];

$terms = Page::get('callterms');

include 'view/call/prologue.html.php';
include 'view/call/header.html.php';
?>

	<div id="main">

		<?php echo new View('view/call/side.html.php', $this); ?>
	
		<div id="content">
            <?php echo new View('view/call/widget/title.html.php', $this); ?>

			<div class="freetext">

				<h2 class="title"><?php echo Text::get('call-terms-main-header') ?></h2>
				
				<div id="call-terms"><?php echo nl2br(Text::urlink($call->legal)) ?></div>
				
			</div>

			<div class="freetext">

				<h2 class="title"><?php echo $terms->description; ?></h2>

                <div class="goteo-call-terms"><?php echo $terms->content; ?></div>

			</div>

            <p class="block">
                <a class="aqua" href="<?php echo SITE_URL ?>/call/<?php echo $call->id ?>/info"><?php echo Text::get('call-info-main-header') ?></a>
            </p>
            <p>
       <?php if ($call->status == 3) : //inscripcion ?>
            <?php if (!$call->expired) : // sigue abierta ?>
            <a href="<?php echo SITE_URL ?>/call/<?php echo $call->id ?>/apply" class="button red join" target="_blank"><?php echo Text::get('call-splash-apply-button') ?></a>
            <?php endif; ?>
        <?php else : //en campaña ?>
            <a href="<?php echo SITE_URL ?>/call/<?php echo $call->id ?>/projects" class="button red view"><?php echo Text::get('call-splash-see_projects-button') ?></a>
        <?php endif; ?>
            </p>
		</div>	
	
	</div>

<?php 

include 'view/call/footer.html.php';

include 'view/epilogue.html.php';

 ?>