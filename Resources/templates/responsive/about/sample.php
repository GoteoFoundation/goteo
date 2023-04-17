<?php
$this->layout("layout", [
    'bodyClass' => 'about',
    'title' => $this->name,
    'meta_description' => $this->description
    ]);

$this->section('content');
?>
<div class="container">

    <?php if ($this->get_config('current_node') == $this->get_config('node')) : ?>
        <div id="sub-header">
            <h1><?= $this->raw('description') ?></h1>
        </div>
    <?php endif; ?>

    <div id="main">
        <div class="widget margin-bottom-4">
            <h2 class="title"><?= $this->raw('name') ?></h2>
            <?= $this->raw('content') ?>
        </div>
    </div>
</div>

<?php $this->replace() ?>

