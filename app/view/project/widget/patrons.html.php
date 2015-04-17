<?php
use Goteo\Library\Text,
    Goteo\Core\View;

$patrons = $vars['patrons'];

if (count($patrons) > 1) : ?>
<script type="text/javascript">
    $(function(){
        $('#slides_patrons').slides({play: 4000});
    });
</script>
<?php endif; ?>
<div id="slides_patrons" class="widget project-patrons">
    <h4><?php echo Text::get('regular-recomended_by'); ?></h4>

    <div class="slides_container">
        <?php foreach ($patrons as $patron) : ?>
        <div class="patron">
            <div class="patron-name">
                <a href="/user/profile/<?php echo $patron->id ?>" title="<?php echo $patron->name ?>" target="_blank"><?php echo $patron->name ?></a>
            </div>
            <div class="patron-avatar">
                <a href="/user/profile/<?php echo $patron->id ?>" title="<?php echo $patron->name ?>" target="_blank"><img src="<?php echo $patron->avatar->getLink(112, 74, true) ?>" alt="<?php echo htmlspecialchars($patron->name) ?>" /></a>
            </div>
            <div class="patron-reco">
                <span><a href="<?php echo $patron->link ?>" target="_blank"><?php echo $patron->title; ?></a></span>
                <blockquote><a href="<?php echo $patron->link ?>" target="_blank"><?php echo $patron->description; ?></a></blockquote>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

<?php if (count($patrons) > 1) : ?>
    <div class="sliderspatrons-ctrl">
        <a class="prev">prev</a>
        <ul class="paginacion"></ul>
        <a class="next">next</a>
    </div>
<?php endif; ?>
</div>
