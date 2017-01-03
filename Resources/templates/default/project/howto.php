<?php

$this->layout("layout", [
    'bodyClass' => 'about',
    'title' => $this->text('meta-title-create-project'),
    'meta_description' => $this->text('meta-description-create-project')
    ]);

$this->section('content');
$action = $this->action ? $this->action : '/project/create';
// We dont want containing <form> tags
$content = preg_replace(
    array('#<form[^>]*>#i', '#</form>#i'),
    array('', ''),
    $this->raw('content')
    );

?>
    <div id="sub-header">
        <div>
            <h2><?= $this->raw('description') ?></h2>
        </div>
    </div>

    <div id="main">
        <form action="<?= $action; ?>" method="post">

        <div class="widget">
            <h3 class="title"><?= $this->raw('name'); ?></h3>
            <?= $content ?>
        </div>

        </form>
    </div>

<?php $this->replace() ?>

<?php $this->section('footer') ?>

<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
jQuery(document).ready(function($) {
    $("#create_accept").click(function (event) {
        if (this.checked) {
            $("#create_continue").removeClass('disabled').addClass('weak');
            $("#create_continue").removeAttr('disabled');
        } else {
            $("#create_continue").removeClass('weak').addClass('disabled');
            $("#create_continue").attr('disabled', 'disabled');
        }
    });
});
// @license-end
</script>

<?php $this->append() ?>
