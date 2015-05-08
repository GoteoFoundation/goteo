<?php

$this->layout("layout", [
    'bodyClass' => 'about',
    'title' => $this->text('meta-title-glossary'),
    'meta_description' => $this->text('meta-description-glossary')
    ]);

$this->section('content');
?>
<script type="text/javascript">
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
</script>

    <div id="sub-header">
        <div>
            <h2><?php echo $vars['description']; ?></h2>
        </div>
    </div>

    <div id="main">

        <div class="widget">
            <h3 class="title"><?php echo $vars['name']; ?></h3>
            <?php echo $vars['content']; ?>
        </div>

    </div>

<?php $this->replace() ?>
