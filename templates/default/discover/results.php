<?php

$this->layout("layout", [
    'bodyClass' => 'discover',
    'title' => $this->text('meta-title-discover'),
    'meta_description' => $this->text('meta-description-discover')
    ]);

$this->section('content');

?>

        <div id="sub-header">
            <div>
                <h2 class="title"><?=$this->text('discover-results-header')?></h2>
            </div>

        </div>

        <div id="main">

            <?=$this->insert('discover/partials/searcher')?>

            <div class="widget projects">
                <?php if ($this->results) : ?>
                    <?php foreach ($this->results as $result) : ?>
                        <?=$this->insert('project/widget/project', ['project' => $result])?>
                    <?php endforeach ?>
                <?php else : ?>
                    <?=$this->text('discover-results-empty')?>
                <?php endif ?>
            </div>

        </div>

<?php $this->replace() ?>
