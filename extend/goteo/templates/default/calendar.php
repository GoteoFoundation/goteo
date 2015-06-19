<?php

use Goteo\Core\View;

$this->layout('layout', [
    'bodyClass' => 'faq',
    'title' => 'Calendario Goteo'
    ]);



$current = $this->current;

$this->section('content');

?>
<?php if (\Goteo\Application\Config::isMasterNode()) : ?>
		<div id="sub-header-secondary">
            <div class="clearfix">
                <h2>GOTEO<span class="red">AGENDA</span></h2>
                <?= View::get('header/share.html.php') ?>
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
                        <span style="float:right; margin-right:5px;"><?= $this->text('calendar-share') ?></span>



                    </div>
                    <div id="event-description">
                        <div id="event-description-img"></div>
                        <div id="event-description-text"></div>
                    </div>
                    <div id="extra-events">
                        <h3><?= $this->text('calendar-others-events') ?></h3>
                        <div id="other-events">
                        </div>
                    </div>
                </div>
			    <div id='loading'>loading...</div>

                <div id='calendar'></div>
                <div>
                <ul class="event-category">
                <li><span class="category-legend t"><?= substr($this->text('calendar-workshops-category'),0,1) ?></span><?= $this->text('calendar-workshops-category') ?></li>
                <li><span class="category-legend p"><?= substr($this->text('calendar-projects-category'),0,1) ?></span><?= $this->text('calendar-projects-category') ?></li>
                <li><span class="category-legend e"><?= substr($this->text('calendar-events-category'),0,1) ?></span><?= $this->text('calendar-events-category') ?></li>
                <li><span class="category-legend c"><?= substr($this->text('calendar-calls-category'),0,1) ?></span><?= $this->text('calendar-calls-category') ?></li>
                <li><span class="category-legend r"><?= substr($this->text('calendar-net-category'),0,1) ?></span><?= $this->text('calendar-net-category') ?></li>
                </div>
			</div>
        </div>

<?php $this->replace() ?>


<?php $this->section('head') ?>

<link href="<?php echo SRC_URL ?>/view/css/calendar/fullcalendar.css" rel="stylesheet" />

<?php $this->append() ?>

<?php $this->section('footer') ?>

<script src="<?php echo SRC_URL ?>/view/js/calendar/moment.min.js"></script>
<script src="<?php echo SRC_URL ?>/view/js/calendar/jquery.min.js"></script>
<script src="<?php echo SRC_URL ?>/view/js/calendar/fullcalendar.js"></script>
<script src="<?php echo SRC_URL ?>/view/js/calendar/lang/es.js"></script>
<script src="<?php echo SRC_URL ?>/view/js/calendar/gcal.js"></script>
<script src="<?php echo SRC_URL ?>/view/js/calendar/custom_calendar.js"></script>

<?php $this->append() ?>
