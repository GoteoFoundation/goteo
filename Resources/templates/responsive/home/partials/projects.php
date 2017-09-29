<div class="section projects" >
    <h2 class="title text-center">
        <?= $this->text('home-projects-title') ?>
    </h2>
    <ul class="filters list-inline center-block text-center">
        <li class="active">
            <?= $this->text('home-projects-outdate') ?>
        </li>
        <li>
            <?= $this->text('home-projects-team-favourites') ?>
        </li>
        <li>
            <?= $this->text('home-projects-near') ?>
        </li>
        <li class="matchfunding">
            <?= $this->text('home-projects-matchfunding') ?>
        </li>
    </ul>
    <div class="container">
        <?php if($this->projects_popular): ?>
            <div class="row">
                <div class="col-xs-12">
                    <div class="slider slider-projects">
                        <?php foreach ($this->projects_popular as $project) : ?>
                            <div class="item widget-slide">
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