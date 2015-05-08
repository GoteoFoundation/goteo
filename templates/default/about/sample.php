<?php
$this->layout("layout", [
    'bodyClass' => 'about',
    'title' => $this->name,
    'meta_description' => $this->description
    ]);

$this->section('content');
?>
<?php if (\NODE_ID == \GOTEO_NODE) : ?>
    <div id="sub-header">
        <div>
            <h2><?= $this->raw('description') ?></h2>
        </div>
    </div>
<?php endif; ?>


    <div id="main">

        <div class="widget">
            <h3 class="title"><?= $this->raw('name') ?></h3>
            <?= $this->raw('content') ?>
        </div>

    </div>

<?php $this->replace() ?>

