<?php

$sponsors = $this->sponsors;

?>
<script type="text/javascript">
    $(function(){
        $('#slides_side_sponsor').slides({
            container: 'slides_container',
            effect: 'fade',
            crossfade: false,
            fadeSpeed: 350,
            play: 5000,
            pause: 1
        });
    });
</script>
<div id="slides_side_sponsor" class="side_widget sponsors">
    <p class="title">
        <span class="line"></span>
        <?= $this->text('node-header-sponsorby'); ?>
    </p>
    <div class="slides_container" style="min-height: 75px;">
        <?php $i = 1; foreach ($sponsors as $sponsor) : ?>
        <div class="logo" id="footer-sponsor-<?php echo $i ?>">
            <a href="<?php echo $sponsor->url ?>" title="<?php echo htmlspecialchars($sponsor->name) ?>" target="_blank" rel="nofollow"><img src="<?php echo $sponsor->image->getLink(150, 85) ?>" alt="<?php echo htmlspecialchars($sponsor->name) ?>" /></a>
        </div>
        <?php $i++; endforeach; ?>
    </div>
    <div class="slidersponsors-ctrl">
        <a class="prev">prev</a>
        <ul class="paginacion"></ul>
        <a class="next">next</a>
    </div>
</div>
