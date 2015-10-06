<?php
$this->layout('layout', [
    'bodyClass' => '',
    'title' => $this->text('invest-method-title'). ':: Goteo.org',
    'meta_description' => $this->text('invest-method-title')
    ]);

$this->section('content');

?>

<div class="container">

	<div class="row row-form">
			<div class="panel panel-default select-method">
				<div class="panel-body">

					<div class="col-md-10 col-md-offset-1 reminder">
	                    <strong><?= $this->project->name ?></strong>
	                        <?= $this->text('project-invest-start') ?>
                    </div>

                    <?= $this->supply('sub-header', $this->get_session('sub-header')) ?>

					<h2 class="col-sm-offset-1 padding-bottom-2"><?= $this->text('invest-select-reward') ?></h2>

					<form class="form-horizontal" role="form" method="GET" action="/invest/<?= $this->project->id ?>/payment">

					<div class="row no-padding col-sm-10 col-sm-offset-1">
						<label class="label-reward <?= $this->reward ? '' : 'reward-choosen' ?>" for="no-reward">
						<div class="col-sm-11 no-padding">
							<input name="reward" class="reward" id="no-reward" <?= $this->reward ? '' : 'checked="checked"' ?> value="0" type="radio">
                            <strong class="margin-left-2"><?= $this->text('invest-resign') ?></strong>
						</div>
						<?php if(!$this->reward): ?>
							<div class="row no-padding col-sm-10 col-sm-offset-2 margin-2" id="amount-container">
								<div class="col-sm-1 col-sm-offset-1 no-padding col-xs-1">
									<strong class="reward-amount"><?= $this->get_currency('symbol') ?></strong>
								</div>
								<div class="no-padding container-input-amount col-md-4 col-sm-3 col-xs-10">
									<input type="number" min="0" class="form-control input-amount" name="amount" value="<?= $this->amount ?>" id="amount" required>
								</div>
								<div class="col-md-5 col-sm-4 col-md-offset-1 reward-button">
									<button type="submit" class="btn btn-block btn-success col-xs-3 margin-2"><?= $this->text('invest-button') ?></button>
								</div>
							</div>
						<?php endif ?>
						</label>



					</div>

					<?php $checked=' checked="checked"'; foreach($this->rewards as $reward):?>

					<div class="row no-padding col-sm-10 col-sm-offset-1">
						<label class="label-reward <?= $reward->id==$this->reward->id ? 'reward-choosen' : '' ?>" for="reward-<?= $reward->id?>">
						<div class="col-sm-2 no-padding">
							<input name="reward" class="reward" id="reward-<?= $reward->id?>" <?= $reward->id==$this->reward->id ? $checked : '' ?> value="<?= $reward->amount ?>" type="radio">
                            <strong class="reward-amount"><?= amount_format($reward->amount) ?></strong>
						</div>
						<div class="col-sm-9">
							<strong><?= $reward->reward ?></strong>
							<div class="reward-description">
							<?= $reward->description ?>
							</div>
							<div style="margin-top:10px">
								<?php if ($reward->none) : // no quedan ?>
								<span class="limit-reward"><?= $this->text('invest-reward-none') ?></span>
				                    <?php elseif (!empty($reward->units)) : // unidades limitadas ?>
				                    <strong class="limit-reward"><?= $this->text('project-rewards-individual_reward-limited');?></strong><br>
				                    <?php $units = ($reward->units - $reward->taken); // resto?>
									<span class="limit-reward-number">
				                    <?= $this->text('project-rewards-individual_reward-units_left', $units) ?><br>
				                	</span>
				                <?php endif ?>
							</div>
						</div>

						<div class="col-sm-1 no-padding sm-display-none">
							<img class="img-responsive reward-icon" src="<?= SRC_URL ?>/assets/img/rewards/<?= $reward->icon ?>.svg">
						</div>
						<?php if($reward->id==$this->reward->id): ?>
							<div class="row no-padding col-md-10 col-md-offset-2 margin-2" id="amount-container">
								<div class="col-md-1 col-md-offset-1 no-padding col-xs-1">
									<strong class="reward-amount"><?= $this->get_currency('symbol') ?></strong>
								</div>
								<div class="no-padding container-input-amount col-md-4 col-sm-3 col-xs-10">
									<input type="number" class="form-control input-amount" name="amount" value="<?= $this->amount ?>" id="amount" min="<?= $reward->amount ?>" required>
								</div>
								<div class="col-md-5 col-sm-4 col-md-offset-1 reward-button">
									<button type="submit" class="btn btn-block btn-success col-xs-3 margin-2"><?= $this->text('invest-button') ?></button>
								</div>
							</div>
						<?php endif ?>
						</label>

					</div>

					<?php endforeach ?>


					</form>
				</div>
			</div>

	</div>
<?= $this->insert('invest/partials/steps_bar') ?>
</div>



<?php $this->replace() ?>



<?php $this->section('footer') ?>
<script type="text/javascript">

$(':radio').change(function(){
	$(".reward").each(function(){
		tmpID=$(this).attr("id");
		if($(this).is(":checked"))
		{
			$('#'+tmpID).closest( "label" ).addClass( "reward-choosen" );
			$('#'+tmpID).closest( "label" ).append( $( "#amount-container" ) );
			$('#amount').val($('#'+tmpID).val());
			$('#amount').attr('min', $('#'+tmpID).val());
		}
		else
		{
			$('#'+tmpID).closest( "label" ).removeClass( "reward-choosen" );

		}
	})

})


</script>

<?php $this->append() ?>
