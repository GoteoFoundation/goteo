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

                    <?= $this->insert('invest/partials/reward_box_resign') ?>

                    <?php foreach($this->rewards as $reward_item): ?>
                        <?= $this->insert('invest/partials/reward_box', ['reward_item' => $reward_item]) ?>
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
    var id = $(this).attr('id');
	$(this).closest( "label" ).addClass( "reward-choosen" );
    $(".reward:not(#" + id + ")").each(function(){
        $(this).closest( "label" ).removeClass( "reward-choosen" );
        $(this).prop('checked', false);
	})

});

</script>

<?php $this->append() ?>
