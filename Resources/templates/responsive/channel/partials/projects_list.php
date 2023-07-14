<div class="slider slider-projects" data-total="<?= (int)$this->total_projects ?>" data-limit="<?= (int)$this->limit ?>">
    <?php foreach ($this->projects as $project) : ?>
        <div class="item widget-slide">
        <?php if ($project->isPermanent()): ?>
                <?= $this->insert('project/widgets/normal_permanent', ['project' => $project]) ?>
        <?php else: ?>
            <?= $this->insert('project/widgets/normal', ['project' => $project]) ?>
        <?php endif; ?>
        </div>
    <?php endforeach ?>
</div>
