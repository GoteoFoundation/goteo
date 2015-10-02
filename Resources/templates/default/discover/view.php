<?php

$bodyClass = 'discover';

$this->layout("layout", [
    'bodyClass' => 'discover',
    'title' => $this->text('meta-title-discover'),
    'meta_description' => $this->text('meta-description-discover')
    ]);


$this->section('content');
?>
        <div id="sub-header">
            <div>
                <h2 class="title"><?=$this->raw('title')?></h2>
            </div>

        </div>

        <div id="main">

            <div class="widget projects">
                <?php foreach ($this->list as $project): ?>
                     <?= $this->insert('project/widget/project', ['project' => $project]) ?>
                <?php endforeach ?>
            </div>

            <?=$this->insert('partials/utils/paginator', ['total' => $this->total, 'limit' => $this->limit ? $this->limit : 10])?>

        </div>


<?php $this->replace() ?>
