<?php if (!empty($this['project']->media)): ?>
<div class="widget project-media" <?php if ($this['project']->media_usubs) : ?>style="height:412px;"<?php endif; ?>>
    <?php echo $this['project']->media->getEmbedCode($this['project']->media_usubs); ?>
    <?php //echo putImages($this['project']->secGallery['play-video']); ?>
</div>
<?php endif ?>