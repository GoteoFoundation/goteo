<?php

use Goteo\Library\Text,
    Goteo\Core\View;

$call = $this['call'];
?>
<script type="text/javascript">
    $(function(){
        $('#call-banners').slides({
            container: 'call-banners-container',
            paginationClass: 'bannerspage',
            generatePagination: true,
            effect: 'fade',
            fadeSpeed: 200
        });
    });
</script>
<div id="call-banners" class="rounded-corners-bottom">
    <div class="call-banners-container rounded-corners-bottom">

        <?php foreach ($call->banners as $banner) : ?>
            <div class="call-banner<?php if (!empty($banner->url)) echo ' activable'; ?>"<?php if ($banner->image instanceof \Goteo\Model\Image) : ?> style="background: url('/data/images/<?php echo $banner->image->name; ?>') no-repeat right bottom;"<?php endif; ?>>
                <?php if (!empty($banner->url)) : ?><a href="<?php echo $banner->url; ?>" class="expand" target="_blank"></a><?php endif; ?>
            </div>
        <?php endforeach; ?>

    </div>
    <div id="call-banners-controler"><ul class="bannerspage"></ul></div>
</div>