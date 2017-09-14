<div class="section projects" >
    <h2 class="title text-center">
        <?= $this->text('home-projects-title') ?>
    </h2>
    <div class="container">
        <?php if($this->projects_popular): ?>
            <div class="row">
                <div class="col-xs-12">
                    <div class="slider slider-projects">
                        <?php foreach ($this->projects_popular as $project) : ?>
                            <div style="margin-left: 20px">
                            <?= $this->insert('project/widgets/normal', [
                                'project' => $project
                                ]) ?>
                            </div>
                        <?php endforeach ?>
                    </div>
                </div>
            </div>
        <?php endif ?>
    </div>
</div>