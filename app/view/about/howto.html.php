<?php
$bodyClass = 'about';
include __DIR__ . '/../prologue.html.php';
include __DIR__ . '/../header.html.php'; ?>


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

<?php if($_SESSION['messages']) { include __DIR__ . '/../header/message.html.php'; } ?>

    <div id="main">

        <div class="widget">
            <h3 class="title"><?php echo $vars['name']; ?></h3>
            <?php echo $vars['content']; ?>
        </div>

    </div>

<?php include __DIR__ . '/../footer.html.php' ?>

<?php include __DIR__ . '/../epilogue.html.php' ?>
