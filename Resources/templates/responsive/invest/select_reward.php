<?php
$this->layout('invest/layout');

$this->section('main-content');

?>


<div class="container">

    <div class="row row-form">
        <div class="panel panel-default invest-container">
            <div class="panel-body">

                <h2 class="col-sm-offset-1 padding-bottom-2"><?= $this->text('invest-select-reward') ?></h2>

                <?= $this->supply('sub-header') ?>

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

<?php $this->section('facebook-pixel') ?>
    <?= $this->insert('partials/facebook_pixel', ['pixel' => $this->project->facebook_pixel, 'track' => ['PageView', 'AddToCart']]) ?>
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


