<?php
use Goteo\Library\Text;

$project = $this['project'];

$langs = $project->getLangs();

if (count($langs) <= 1) return '';
?>
<style type="text/css">div.widget.project-langs a {margin-left: 6px;} div.widget.project-langs a:hover {color: #20B2B3;}</style>
<div class="widget project-langs" style="padding: 10px 20px;">
    <span style="text-transform: uppercase;" ><?php echo Text::get('project-langs-header'); ?></span>
    <?php
    foreach ($langs as $langId => $langName) {
        if ($langId == \LANG) continue;
        echo '<a href="/project/'.$project->id.'/?lang='.$langId.'">'.$langName.'</a>';
    }
    ?>
</div>
