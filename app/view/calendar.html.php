<?php

use Goteo\Library\Text,
    Goteo\Core\View;

$current = $this['current'];

$bodyClass = 'faq';

// funcionalidades con autocomplete
$jsreq_calendar= $this['calendar'];


include __DIR__ . '/prologue.html.php';
include __DIR__ . '/header.html.php';

?>
<script type="text/javascript">
    $(function(){
        $(".faq-question").click(function (event) {
            event.preventDefault();

            if ($($(this).attr('href')).is(":visible")) {
                $($(this).attr('href')).hide();
            } else {
                $($(this).attr('href')).show();
            }

        });

        var hash = document.location.hash;
        if (hash != '') {
            $(hash).show();
        }
    });


</script>
<?php if (\NODE_ID == \GOTEO_NODE) : ?>
		<div id="sub-header-secondary">
            <div class="clearfix">
                <h2>GOTEO<span class="red">AGENDA</span></h2>
                <?php echo View::get('header/share.html.php') ?>
            </div>
        </div>
<?php endif; ?>
        <div id="main">
			<div id="faq-content">
			    <div id='loading'>loading...</div>

                <div id='calendar'></div>
			</div>
        </div>
	<?php include __DIR__ . '/footer.html.php' ?>
<?php include __DIR__ . '/epilogue.html.php' ?>
