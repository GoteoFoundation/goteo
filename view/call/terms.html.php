<?php

use Goteo\Library\Text,
    Goteo\Core\View;

$bodyClass = 'terms';

$call = $this['call'];

include 'view/call/prologue.html.php';
include 'view/call/header.html.php';
?>

	<div id="main">

		<?php echo new View('view/call/side.html.php', $this); ?>
	
		<div id="content">
			<h2 class="title"><?php echo Text::get('call-splash-campaign_title') ?><br /><?php echo $call->name ?></h2>
            <?php if ($call->status == 3) : //inscripcion ?>
            <p class="subtitle red"><?php echo Text::get('call-splash-searching_projects') ?></p>
            <?php else : //en campaña ?>
            <p class="subtitle"><?php echo Text::get('call-splash-invest_explain', $call->user->name) ?></p>
            <?php endif; ?>
			<div class="freetext">

				<h2 class="title">Terminos y condiciones de la campa&ntilde;a</h2>
				
				<div id="call-terms"><?php echo $call->legal ?></div>
				
			</div>

            <p class="block">
                <a class="aqua" href="<?php echo SITE_URL ?>/call/<?php echo $call->id ?>/info">Informaci&oacute;n general de la campa&ntilde;a</a>
            </p>
       <?php if ($call->status == 3) : //inscripcion ?>
            <?php if (!$call->expired) : // sigue abierta ?>
            <a href="<?php echo SITE_URL ?>/call/<?php echo $call->id ?>/apply" class="button red join" target="_blank"><?php echo Text::get('call-splash-apply-button') ?></a>
            <?php endif; ?>
        <?php else : //en campaña ?>
            <a href="<?php echo SITE_URL ?>/call/<?php echo $call->id ?>/projects" class="button red view"><?php echo Text::get('call-splash-see_projects-button') ?></a>
        <?php endif; ?>
		</div>	
	
	</div>

<?php 

include 'view/call/footer.html.php';

include 'view/epilogue.html.php';

 ?>