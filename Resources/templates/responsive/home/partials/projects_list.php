<div class="slider slider-projects" data-total="<?= (int)$this->total_projects ?>" data-limit="<?= (int)$this->limit ?>">
    <?php foreach ($this->projects as $project) : ?>
            <div class="item widget-slide">
            <?=    $this->insert('project/widgets/normal', [
                    'project' => $project
            ]) ?>
            </div>
    <?php endforeach ?>
</div>
