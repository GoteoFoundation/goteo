<?php

use Goteo\Library\Text,
    Goteo\Core\View;

$bodyClass = 'info';

$call = $this['call'];

foreach ($call->projects as $key=>$proj) {

    if ($proj->status < 3 || $proj->status > 5) {
        unset($call->projects[$key]);
    }
}


include 'view/call/prologue.html.php';
include 'view/call/header.html.php';
?>
<script type="text/javascript">
$(document).ready(function() {
   $("div.click").click(function() {
       $(this).children("span.icon").toggleClass("open");
       $(this).children("blockquote").toggle();
    });
 });
</script>

	<div id="main">

		<?php echo new View('view/call/side.html.php', $this); ?>
	
		<div id="content">
			<h2 class="title"><?php echo Text::get('call-splash-campaign_title') ?><br /><?php echo $call->name ?></h2>
            
            <?php if ($call->status == 3) : //inscripcion ?>
            <p class="subtitle red"><?php echo Text::get('call-splash-searching_projects') ?></p>
            <?php elseif (!empty($call->amount)) : //en campaña con dinero ?>
            <p class="subtitle"><?php echo Text::get('call-splash-invest_explain', $call->user->name) ?></p>
            <?php else : //en campaña sin dinero, con recursos ?>
            <p class="subtitle"><?php echo $call->resources ?></p>
            <?php endif; ?>
            
			<div class="freetext">

				<h2 class="title"><?php echo Text::get('call-info-main-header') ?></h2>

				<div id="call-description"><?php echo nl2br(Text::urlink($call->description)) ?></div>

                <h3 class="title"><?php echo Text::get('call-info-whom-header') ?></h3>
				<p><?php echo nl2br(Text::urlink($call->whom)) ?></p>

            <?php if ($call->status == 3) : //inscripcion ?>
                <h3 class="title"><?php echo Text::get('call-info-apply-header') ?></h3>
				<p><?php echo nl2br(Text::urlink($call->apply)) ?></p>
            <?php elseif (count($call->projects) > 0) : //en campaña ?>
                
                <h3><?php echo Text::get('call-splash-selected_projects-header') ?></h3>

				<table class="info-table" width="100%">
					<thead class="task">
						<tr>
							<th class="summary">Aportaciones:</th>
							<th class="min"><?php if (!empty($call->amount)) : ?>Campaña<?php endif; ?></th>
							<th class="max">Usuarios</th>
						</tr>
					</thead>
					<tbody>
                    <?php
                        $tot_call  = 0;
                        $tot_users = 0;
                        $odd = true;
                        
                        foreach ($call->projects as $proj) :

                            $tot_call  += $proj->amount_call;
                            $tot_users += $proj->amount_users;
                        ?>
						<tr class="<?php if ($odd) {echo 'odd'; $odd = false;} else {echo 'even'; $odd = true;} ?>">
							<th class="summary">
								<div class="click">
									<span class="icon">&nbsp;</span>
                                    <span><strong><?php echo $proj->name ?></strong></span>
									<blockquote><?php echo empty($proj->subtitle) ? Text::recorta($proj->description, 250) : $proj->subtitle; ?></blockquote>
								</div>
							</th>
                            <td class="min"><?php if (!empty($call->amount)) echo \amount_format($proj->amount_call) . ' &euro;'; ?></td>
                            <td class="max"><?php echo \amount_format($proj->amount_users) ?> &euro;</td>
						</tr>
                    <?php endforeach; ?>
					</tbody>
					<tfoot>
						<tr>
							<th class="total"><?php echo Text::get('regular-total'); ?></th>
							<th class="min"><?php if (!empty($call->amount)) echo \amount_format($tot_call) . ' &euro;';  ?></th>
							<th class="max"><?php echo \amount_format($tot_users) ?> &euro;</th>
						</tr>
					</tfoot>
				</table>
            <?php endif; ?>
                
			</div>

            <p class="block">
                <a class="aqua" href="<?php echo SITE_URL ?>/call/<?php echo $call->id ?>/terms"><?php echo Text::get('call-terms-main-header') ?></a>
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