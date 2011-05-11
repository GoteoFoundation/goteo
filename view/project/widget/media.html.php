<?php if (!empty($this['project']->media)): ?>
<div class="widget project-media">
    <?php echo $this['project']->media->getEmbedCode() ?>
</div>
<?php endif ?>