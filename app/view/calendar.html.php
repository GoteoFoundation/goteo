<?php

use Goteo\Library\Text,
    Goteo\Core\View;

$current = $vars['current'];

$bodyClass = 'faq';

// funcionalidades con autocomplete
$jsreq_calendar= $vars['calendar'];


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
                <div id="read-more" class="read-more">
                    <h3 id="event-date"></h3>
                    <div id="event-title"></div>
                    <div id="category-info" class="nodisplay">
                        <span style="margin-right:0;" id="category-letter"></span>
                        <span id="event-category"></span>
                    </div>
                    <div class="extra">
                        <span id="event-hour" class="event-hour"></span><span id="event-location" class="event-location"></span>
                        <a id="event-calendar-add" href=""><img style="float:right;" width="14" src="/view/css/calendar/agenda-icon.svg"/></a>
                        <a id="event-twitter" href=""><img style="float:right; margin-right:5px;" width="14" src="/view/css/calendar/twitter.png"/></a>
                        <a id="event-facebook" href=""><img style="float:right; margin-right:5px;" width="14" src="/view/css/calendar/facebook.png"/></a>
                        <span style="float:right; margin-right:5px;"><?php echo Text::get('calendar-share'); ?></span>



                    </div>
                    <div id="event-description">
                        <div id="event-description-img"></div>
                        <div id="event-description-text"></div>
                    </div>
                    <div id="extra-events">
                        <h3><?php echo Text::get('calendar-others-events'); ?></h3>
                        <div id="other-events">
                        </div>
                    </div>
                </div>
			    <div id='loading'>loading...</div>

                <div id='calendar'></div>
                <div>
                <ul class="event-category">
                <li><span class="category-legend t"><?php echo substr(Text::get('calendar-workshops-category'),0,1); ?></span><?php echo Text::get('calendar-workshops-category'); ?></li>
                <li><span class="category-legend p"><?php echo substr(Text::get('calendar-projects-category'),0,1); ?></span><?php echo Text::get('calendar-projects-category'); ?></li>
                <li><span class="category-legend e"><?php echo substr(Text::get('calendar-events-category'),0,1); ?></span><?php echo Text::get('calendar-events-category'); ?></li>
                <li><span class="category-legend c"><?php echo substr(Text::get('calendar-calls-category'),0,1); ?></span><?php echo Text::get('calendar-calls-category'); ?></li>
                <li><span class="category-legend r"><?php echo substr(Text::get('calendar-net-category'),0,1); ?></span><?php echo Text::get('calendar-net-category'); ?></li>
                </div>
			</div>
        </div>
	<?php include __DIR__ . '/footer.html.php' ?>
<?php include __DIR__ . '/epilogue.html.php' ?>
