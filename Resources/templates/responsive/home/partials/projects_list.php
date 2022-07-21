<div class="slider slider-projects"
     data-total="<?= $this->response->getTotalProjects() ?>"
     data-limit="<?= $this->response->getLimit() ?>">
    <?php foreach ($this->response->getProjects() as $project) : ?>
            <div class="item widget-slide">
            <?=    $this->insert('project/widgets/normal', [
                    'project' => $project
            ]) ?>
            </div>
    <?php endforeach ?>
</div>
