<?php 
$bodyClass = 'about';
include 'view/prologue.html.php';
include 'view/header.html.php'; ?>


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
            <h2><?php echo $this['name']; ?></h2>
        </div>
    </div>

    <div id="main">

        <div class="widget">
            <?php echo $this['content']; ?>
        </div>

    </div>
    
<?php include 'view/footer.html.php' ?>

<?php include 'view/epilogue.html.php' ?>