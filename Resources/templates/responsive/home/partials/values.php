<div class="fluid-container data-container goteo-values">
    <div class="container">
        <div><h1 class="title text-center">Valores de Goteo</h1></div>
        <div class="text-center footprint-tabs">
            <ul>
                <?php foreach($this->footprints as $index => $footprint): ?>
                    <li>
                        <a href="" data-footprint="<?= $footprint->id ?>" class="<?= ($index == 0)? "active" : '' ?>" ></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php foreach($this->footprints as $index => $footprint): ?>
            <div class="row <?= ($index != 0)? "hidden" : '' ?>" id="goteo-values-<?= $footprint->id ?>">
                <div class="col footprint-briefing">
                    <img src="assets/img/footprint/<?= $footprint->id ?>.svg" heigh="70" width="70" alt="<?= $footprint->name ?>" class="footprint" />
                    <p><span class="footprint-label"><?= $this->t('footprint-' . $foorptint->id) ?></span></p>
                    <h2><?= $this->t('footprint-' . $footprint->id . '-title') ?></h2>
                    <p><?= $this->t('footprint-' . $footprint->id . '-description') ?></p>
                    <h3><?= $this->t('footprint-related-sdgs') ?>:</h3>
                    <p><?= $this->t('click-know-more') ?></p>
                    <ul>
                        <?php foreach($this->sdg_by_footprint[$footprint->id] as $sdg): ?>
                        <li><a href=""><img src="assets/img/ods/ods<?= $sdg->id ?>.svg" width="75" height="75" alt="<?= $sdg->name ?>"/></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="col footprint-info">
                    <div class="slider slider-footprint-data">
                        <?php foreach($footprint->getAllImpactData() as $impact_data): ?>
                            <div class="">
                                <?php if ($impact_data->image): ?>
                                    <img
                                        src="<?= $impact_data->getImage()->getLink(165,240,true) ?>" 
                                        alt="<?= $footprint->title ?>"
                                        height="165"
                                        width="240"
                                        >
                                <?php endif; ?>
                                <h2><?= $impact_data->title ?></h2>
                                <h3><span><?= $impact_data->subtitle?><span></h3>
                                <p><?= $impact_data->description ?></p>
                            </div>
                        <?php endforeach ?>
                    </div>
                    <div class="slider slider-footprint-projects">
                        <?php foreach($this->projects_by_footprint[$footprint->id] as $index => $project): ?>
                            <div class="footprint-project">
                                <img src="<?= $project->image->getLink(600, 416, true); ?>" class="bg-project eco">
                                <div class="project-footprint">
                                    <img src="assets/img/footprint/<?= $footprint->id ?>.svg" height="70" width="70" alt="<?= $footprint->name ?>" class="footprint" />
                                </div>
                                <h2><a href="/project/<?= $project->id ?>"><?= $this->text_truncate($this->ee($project->name), 80); ?></a></h2>
                                <p><a href="/user/profile/<?= $this->project->user->id ?>"><?= $this->text('regular-by') . ' ' . $this->project->user->name ?></a></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="footprint-action">
                    <a href="">Ver proyectos de huella ecológica <span class="icon glyphicon glyphicon glyphicon-menu-right" aria-hidden="true"></span></a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>