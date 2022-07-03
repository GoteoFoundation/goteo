<?php
    $section = $this->dataSetsSection;
    if ($section):
?>
    <div class="section data-sets-section">
        <div class="container">
            <h2 class="title text-center"><?= $section->main_title ?></h2>
            <p class="description">
                <?= $section->main_description ?>
            </p>
            <?= $this->insert('partials/components/data_sets', ['dataSets' => $this->dataSets]) ?>
        </div>
    </div>
<?php endif; ?>
