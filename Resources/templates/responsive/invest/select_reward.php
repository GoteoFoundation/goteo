<?php
$this->layout('layout', [
    'bodyClass' => '',
    'title' => $this->text('invest-method-title'). ':: Goteo.org',
    'meta_description' => $this->text('invest-method-title')
    ]);

$this->section('content');

?>

<?= $this->insert('invest/partials/project_info') ?>

<?= $this->insert('invest/partials/steps_bar') ?>

<div class="container">

	<div class="row row-form">
			<div class="panel panel-default invest-container">
				<div class="panel-body">

                    <h2 class="col-sm-offset-1 padding-bottom-2"><?= $this->text('invest-select-reward') ?></h2>

                    <?= $this->supply('sub-header', $this->get_session('sub-header')) ?>

                    <?= $this->insert('invest/partials/reward_box_resign') ?>

                    <?php foreach($this->rewards as $reward_item): ?>
                        <?= $this->insert('invest/partials/reward_box', ['reward_item' => $reward_item]) ?>
					<?php endforeach ?>

					</form>
				</div>
			</div>

	</div>

</div>


<?php $this->replace() ?>



<?php $this->section('footer') ?>
<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt

$(':radio').change(function(){
    var id = $(this).attr('id');
    $(this).closest( "label" ).addClass( "reward-choosen" );
    $(".reward:not(#" + id + ")").each(function(){
        $(this).closest( "label" ).removeClass( "reward-choosen" );
        $(this).prop('checked', false);
    })

});

// @license-end
</script>

<?php $this->append() ?>


<?php //Add facebook pixel to track Facebook ads ?>
<?php if($this->project->facebook_pixel): ?>

<?php $this->section('footer') ?>
<!-- Facebook Pixel Code -->
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '<?= $this->ee($this->project->facebook_pixel, "js") ?>');
fbq('track', 'PageView');
fbq('track', 'AddToCart');
</script>
<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=<?= $this->ee($this->project->facebook_pixel, "js") ?>&ev=PageView&noscript=1"
/></noscript>
<!-- DO NOT MODIFY -->
<!-- End Facebook Pixel Code -->
<?php $this->append() ?>

<?php endif; ?>
