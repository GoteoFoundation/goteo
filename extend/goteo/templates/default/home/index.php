<?php

$this->layout('default::home/index');

?>

<?php $this->section('index-sub-header-right') ?>

    <div class="mod-pojctopen" id="mod-pojctopen">
        <a href="" id="event-link" class="expand"></a>
        <div class="main-calendar" id="main-calendar">
            <div class="next-event">
                <span><?=$this->text('calendar-home-title')?></span>
            </div>
            <div class="inside" id="inside">
                <div class="event-month" id="event-month"></div>
                <div class="event-day" id="event-day"></div>
                <div id="event-text-day"></div>
                <div class="event-interval"><span class="icon-clock"></span><span id ="event-start"></span><?=$this->text('calendar-home-hour')?><span id ="event-end"></span></div>
            </div>
        </div>
        <div class="extra-calendar" id="extra-calendar">
            <div class="event-category" id="event-category"></div>
            <div class="event-title" style="padding:10px; height:60px;" id="event-title"></div>
            <!--<span class="icon-ubication"></span>-->
            <span class="icon-ubication">
            <span class="path1"></span><span class="path2"></span>
            </span>
            <span id="event-location"></span>
        </div>
    </div>

<?php $this->replace() ?>


<?php $this->section('footer') ?>

<script src="<?php echo SRC_URL ?>/view/js/calendar/jquery.min.js"></script>
<script src="<?php echo SRC_URL ?>/view/js/calendar/moment.min.js"></script>
<script src="<?php echo SRC_URL ?>/view/js/calendar/home_calendar.js"></script>

<?php $this->append() ?>

