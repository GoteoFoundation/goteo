<?php

$bodyClass = 'discover';

$this->layout("layout", [
    'bodyClass' => 'discover',
    'meta_description' => $this->text('meta-description-discover'),
    'image' => $og_image
    ]);


$this->section('content');
?>
        <div id="sub-header">
            <div>
                <h2 class="title"><?=$this->title?></h2>
            </div>

        </div>

        <div id="main">

            <div class="widget projects">
                <?php foreach ($this->list as $project): ?>
                     <?= $this->insert('project/widget/project', ['project' => $project]) ?>
                <?php endforeach ?>
            </div>

            <?php echo View::get('pagination.html.php', $vars); ?>
        </div>


<?php $this->replace() ?>
