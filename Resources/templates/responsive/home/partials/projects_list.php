<div class="slider slider-projects">
    <?php foreach ($this->projects_popular as $project) : ?>
            <div class="item widget-slide">
            <?=    $this->insert('project/widgets/normal', [
                    'project' => $project
            ]) ?>
            </div>
    <?php endforeach ?>
</div>