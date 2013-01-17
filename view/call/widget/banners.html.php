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
<div id="call-banners" class="rounded-corners-bottom"<?php if ($bodyClass == 'home') echo ' style="width: 545px; margin: 0px auto;"'; ?>>
    <div class="call-banners-container rounded-corners-bottom"<?php if ($bodyClass == 'home') echo ' style="width: 545px;"'; ?>>

        <?php/* foreach ($banners as $banner) : ?>
        <div class="call-banner<?php if (!empty($banner->url)) : ?> activable<?php endif; ?>"<?php if ($banner->image instanceof \Goteo\Model\Image) : ?> style="background: url('/data/images/<?php echo $banner->image->name; ?>') no-repeat right bottom;"<?php endif; ?>>
            <?php if (!empty($banner->url)) : ?><a href="<?php echo $banner->url; ?>" class="expand" target="_blank"></a><?php endif; ?>
            <div class="title"><?php echo $banner->title ?></div>
            <div class="short-desc"><?php echo $banner->description ?></div>
        </div>
        <?php endforeach; */?>
		
		
			<div class="node-banner activable" style="background: url('/data/images/boteollacc.png') no-repeat right bottom;">
			    <a href="/about" class="expand" target="_blank"></a>            <div class="title">Cómo abrir tu proyecto? </div>
			    <div class="short-desc">Compartiendo conocimiento, procesos, resultado,  responsabilidad o beneficio, desde la filosofía del procomún.</div>
			</div>
			<div class="node-banner activable" style="background: url('/data/images/banner-5.png') no-repeat right bottom;">
			    <a href="/about" class="expand" target="_blank"></a>            <div class="title">Cómo abrir tu proyecto? </div>
			</div>
		
		
    </div>
<div id="call-banners-controler"<?php if ($bodyClass == 'home') echo ' style="width: 940px;"'; ?>><ul class="bannerspage"></ul></div>
</div>