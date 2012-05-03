<?php
if (empty($banners)) return;

?>
<script type="text/javascript">
    $(function(){
        $('#node-banners').slides({
            container: 'node-banners-container',
            paginationClass: 'bannerspage',
            generatePagination: true
        });
    });
</script>
<div id="node-banners" class="rounded-corners-bottom">
    <div class="node-banners-container rounded-corners-bottom">
        <?php foreach ($banners as $banner) : ?>
        <div class="node-banner<?php if (!empty($banner->url)) : ?> activable<?php endif; ?>"<?php if ($banner->image instanceof \Goteo\Model\Image) : ?> style="background: url('<?php echo $banner->image->getLink(940); ?>') no-repeat right bottom;"<?php endif; ?>>
            <?php if (!empty($banner->url)) : ?><a href="<?php echo $banner->url; ?>" class="expand" target="_blank"></a><?php endif; ?>
            <div class="title"><?php echo $banner->title ?></div>
            <div class="short-desc"><?php echo $banner->description ?></div>
        </div>
        <?php endforeach; ?>
    </div>
<div id="node-banners-controler"><ul class="bannerspage"></ul></div>
</div>