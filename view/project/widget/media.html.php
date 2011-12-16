<?php if (!empty($this['project']->media)): ?>
<div class="widget project-media">
    <?php
    // @TODO   universal subtitles standard
    // @FIXME  piñonaco para el video principal de ...
    $universales = array(
        'otroproyecto'
    );
    if (in_array($this['project']->id, $universales)) {
        echo $this['project']->media->getEmbedCode(true);
    } else {
        echo $this['project']->media->getEmbedCode();
    }
     ?>
</div>
<?php endif ?>