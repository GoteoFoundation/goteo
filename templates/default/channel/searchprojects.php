<?php

$channel=$this->channel;
$promotes=$this->projects;

$this->layout("layout", [
    'bodyClass' => 'channel home',
    'title' => $this->title_text.' :: '.$channel->name,
    'meta_description' => $this->title_text.'. '.$channel->description
    ]);

$this->section('content');

?>
<div id="channel-main">
    <?= $this->insert("channel/partials/owner_info") ?>
    <div id="side">
    <?php foreach ($this->side_order as $sideitem=>$sideitemName) {
            echo $this->insert("channel/partials/side/$sideitem");
    } ?>
    </div>

    <div id="content">
        <div id="channel-projects-promote" class="content_widget channel-projects rounded-corners" <?php if ($this->hide_promotes) : ?>style="display:none;"<?php endif; ?>>
            <h2 class="title"><?= $this->title_text ?>
            <span class="line"></span>
            </h2>
            <ul>
                <?php foreach ($promotes as $project) {
                    $project->per_amount = round(($project->amount / $project->mincost) * 100);
                    echo $this->insert('project/widget/horizontal_project', ['project' => $project]);
                }?>
            </ul>
        </div>
    </div>
</div>

<?php $this->replace() ?>

