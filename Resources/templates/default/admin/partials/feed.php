<div class="admin-side">
    <a name="feed"></a>
    <div class="widget feed">

        <h3><?= $this->text('regular-recent-activity') ?></h3>
        Ver Feeds por:

        <p class="categories">
            <?php foreach (\Goteo\Library\Feed::$admin_types as $id => $cat) : ?>
            <a href="/admin/recent?feed=<?php echo $id ?>#feed" <?php echo ($feed == $id) ? 'class="'.$cat['color'].'"': 'class="hov" rel="'.$cat['color'].'"' ?>><?php echo $cat['label'] ?></a>
            <?php endforeach; ?>
        </p>

        <div class="scroll-pane">
            <?php foreach ($this->feed as $item) :
                $odd = !$odd ? true : false;
                ?>
            <div class="subitem<?php if ($odd) echo ' odd';?>">
               <span class="datepub"><?= $this->text('feed-timeago', $item->timeago) ?></span>
               <div class="content-pub"><?php echo $item->html; ?></div>
            </div>
            <?php endforeach; ?>
        </div>

        <a href="/admin/recent" style="margin-top:10px;float:right;text-transform:uppercase">Ver más</a>

    </div>
</div>

