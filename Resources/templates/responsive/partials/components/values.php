<?php
    $title = $this->title;
    $footprints = $this->footprints;
    $sdg_by_footprint = $this->sdg_by_footprint;
    $projects_by_footprint = $this->projects_by_footprint;
    $footprint_impact_data = $this->footprint_impact_data;
    $query = [];
    if ($this->channel)
        $query["channel"] = $this->channel->id;
?>

<div class="fluid-container data-container goteo-values">
    <div class="container">
        <div><h1 class="title text-center"><?= $title ?></h1></div>
        <div class="text-center footprint-tabs">
            <ul>
                <?php foreach($footprints as $index => $footprint): ?>
                    <li>
                        <a href="" data-footprint="<?= $footprint->id ?>" class="<?= ($index == 0)? "active" : '' ?>" ></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php foreach($footprints as $index => $footprint): ?>
            <div class="row <?= ($index != 0)? "hidden" : '' ?>" id="goteo-values-<?= $footprint->id ?>">
                <div class="col footprint-briefing">
                    <img src="<?= $this->asset("img/footprint/{$footprint->id}.svg") ?>" heigh="70" width="70" alt="<?= $footprint->name ?>" class="footprint" />
                    <p><span class="footprint-label"><?= $footprint->name ?></span></p>
                    <h2><?= $footprint->title ?></h2>
                    <p><?= $this->markdown($footprint->description) ?></p>
                    <h3><?= $this->t('home-footprint-values-related-sdgs') ?>:</h3>
                    <p><?= $this->t('regular-click-more') ?></p>
                    <ul>
                        <?php foreach($sdg_by_footprint[$footprint->id] as $sdg): ?>
                            <?php
                                $query["sdgs"] = $sdg->id;
                            ?>
                            <li><a href="/impact-discover?<?= http_build_query($query); ?>" target="_blank"><img src="<?= $this->asset("img/ods/ods{$sdg->id}.svg") ?>" width="75" height="75" alt="<?= $sdg->name ?>"/></a></li>
                        <?php endforeach; ?>
                        <?php unset($query["sdgs"]); ?>
                    </ul>
                </div>
                <div class="col footprint-info">
                    <div class="slider slider-footprint-data">
                        <?php foreach($footprint_impact_data[$footprint->id] as $impact_data): ?>
                            <div class="row">
                                <?php if ($impact_data->image): ?>
                                    <div class="col-md-4 col-xs-4">
                                        <picture>
                                            <source media="(min-width: 1200px)" srcset="<?= $impact_data->getImage()->getLink(200, 400, true) ?>">
                                            <source media="(min-width: 992px) and (max-width: 1199px)" srcset="<?= $impact_data->getImage()->getLink(100, 300, true) ?>">
                                            <source media="(min-width: 768px) and (max-width: 991px)" srcset="<?= $impact_data->getImage()->getLink(100, 500, true) ?>">
                                            <source media="(max-width: 480px)" srcset="<?= $impact_data->getImage()->getLink(100, 300, true) ?>">
                                            <img
                                                src="<?= $impact_data->getImage()->getLink(200, 350, true) ?>"
                                                alt="<?= $footprint->title ?>"
                                            >
                                        </picture>
                                    </div>
                                <?php endif; ?>
                                <div class="footprint-data-info col-md-8 col-xs-8">
                                    <h2><?= $impact_data->title ?></h2>
                                    <h3><span><?= $impact_data->data ?></span> <?= $impact_data->data_unit ?></h3>
                                    <p><?= $impact_data->description ?></p>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                    <div class="slider slider-footprint-projects">
                        <?php foreach($projects_by_footprint[$footprint->id] as $index => $project): ?>
                            <div class="footprint-project">
                                <img src="<?= $project->image->getLink(600, 416, true); ?>" class="bg-project" data-footprint=<?= $footprint->id ?>>
                                <div class="project-footprint">
                                    <img src="<?= $this->asset("img/footprint/{$footprint->id}.svg") ?>" height="70" width="70" alt="<?= $footprint->name ?>" class="footprint" />
                                </div>
                                <h2><a target="_blank" href="/project/<?= $project->id ?>"><?= $this->text_truncate($this->ee($project->name), 80); ?></a></h2>
                                <p><a href="/user/profile/<?= $project->user->id ?>"><?= $this->text('regular-by') . ' ' . $project->user->name ?></a></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php
                    $query["footprints"] = $footprint->id;
                ?>

                <div class="footprint-action">
                    <a href="/impact-discover?<?= http_build_query($query); ?>"><?= $this->t('home-footprint-values-see-projects') ?> <span class="icon glyphicon glyphicon glyphicon-menu-right" aria-hidden="true"></span></a>
                </div>

                <?php
                    unset($query["footprints"]);
                ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
