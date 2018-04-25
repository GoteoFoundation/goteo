<?php $this->layout('project/layout') ?>
<?php $this->section('main-content') ?>

<?php
    $project = $this->project;
    $social_rewards = $this->social_rewards;
    $bonus_rewards = $this->bonus_rewards;
 ?>

<h2 class="green-title">
        <?= $this->text('project-about-title') ?>
        </h2>
        <div class="row">
            <div class="about-description col-md-11">
            <?= $project->subtitle ?>
            </div>
        </div>

        <div class="panel-group" id="accordion">
            <div class="panel panel-default widget">
                <a class="accordion-toggle collapsed" data-toggle="collapse" data-target="#collapseOne">
                    <div class="panel-heading standard-padding">
                        <h2 class="panel-title green-title" >
                            <?= $this->text('project-show-needs') ?>
                            <span class="icon glyphicon glyphicon glyphicon-menu-down" aria-hidden="true"></span>
                        </h2>
                    </div>
                </a>

                <div id="collapseOne" class="panel-collapse collapse">
                  <div class="panel-body">


                    <?php foreach ($this->costs as $type => $list): ?>
                        <table class="footable table needs">
                             <thead>
                                <tr>
                                  <th data-type="html">
                                    <img src="<?= SRC_URL . '/assets/img/project/needs/'.$type.'.png' ?> ">
                                    <span class="type"><?= $this->types[$type] ?></span>
                                  </th>
                                  <th class="text-center" data-type="html" data-breakpoints="xs"><?= $this->text('project-view-metter-minimum') ?></th>
                                  <th class="text-center" data-type="html" data-breakpoints="xs"><?= $this->text('project-view-metter-optimum') ?></th>
                                </tr>
                             </thead>
                              <tbody>
                            <?php foreach ($list as $cost): ?>
                                <tr class="<?= $cost->min ? 'bg-required' : 'bg-optional' ?>" >
                                  <td>
                                      <strong><?= $cost->name ?></strong>
                                      <div><?= $cost->description ?></div>
                                  </td>
                                  <td class="text-center text-nowrap"><span class="required"><?= amount_format($cost->min) ?></span></td>
                                  <td class="text-center text-nowrap"><?= !$cost->min ? amount_format($cost->opt) : '' ?></td>
                                </tr>
                                <?php if(end($this->costs)==$list&&end($list)==$cost): ?>
                                    <tr>
                                        <td class="text-right" data-type="html">
                                            <strong><?= $this->text('project-costs-total') ?></strong>
                                        </td>

                                        <td class="text-center text-nowrap" data-type="html" data-breakpoints="xs">
                                            <span class="required"><?= amount_format($project->mincost) ?></span>
                                        </td>
                                        <td class="text-center text-nowrap" data-type="html" data-breakpoints="xs">
                                            <?= amount_format($project->maxcost) ?>
                                        </td>
                                    </tr>
                                <?php endif ?>
                            <?php endforeach ?>
                              </tbody>

                        </table>
                    <?php endforeach ?>
                    <!--
                    <table class="footable table needs">
                         <thead>
                            <tr>
                              <th data-type="html">
                                <span class="type"><?= $this->text('project-costs-total') ?></span>
                              </th>
                              <th class="text-center" data-type="html" data-breakpoints="xs"><?= $this->text('project-view-metter-minimum') ?></th>
                              <th class="text-center" data-type="html" data-breakpoints="xs"><?= $this->text('project-view-metter-optimum') ?></th>
                            </tr>
                         </thead>
                          <tbody>
                            <tr>
                              <td>
                                  <strong></strong>
                                  <div></div>
                              </td>
                              <td class="text-center"><span class="required"><?= amount_format($project->mincost) ?></span></td>
                              <td class="text-center"><?= amount_format($project->maxcost) ?></td>
                            </tr>
                          </tbody>
                    </table>
                    -->

                    <div class="row no-margin legend no-margin">
                            <div class="circle required pull-left">

                            </div>
                            <div class="title pull-left">
                            <?= $this->text('costs-field-required_cost-yes') ?>
                            </div>

                            <div class="circle extra pull-left">

                            </div>
                            <div class="title pull-left">
                            <?= $this->text('costs-field-required_cost-no') ?>
                            </div>
                    </div>

                    <div class="chart-costs"></div>

                    <div class="row">
                         <div id="reset-chart" class="chart-reset-button text-center col-xs-4 col-xs-offset-4 col-md-2 col-md-offset-5">
                            <button class="btn btn-default" type="button" id="reset-button"> <span class="glyphicon glyphicon-repeat"> </span> <?= $this->text('project-chart-costs-reset') ?></button>
                        </div>
                    </div>

                  </div>
                </div>
            </div>

          <div class="panel panel-default widget">
            <a class="accordion-toggle" data-toggle="collapse" data-target="#collapseTwo">
                <div class="panel-heading standard-padding">
                    <h2 class="panel-title green-title">
                        <?= $this->text('project-general-information') ?>
                        <span class="icon glyphicon glyphicon glyphicon-menu-up pull-right" aria-hidden="true"></span>
                    </h2>
                </div>
            </a>
            <div id="collapseTwo" class="panel-collapse collapse in">
              <div class="panel-body">

                <div class="general-text">
                    <?php /* nl2br($this->text_url_link($project->description)) */ ?>
                    <?= $this->markdown($project->description) ?>
                </div>

                <!-- carousel slider -->

                <div id="infoCarousel" class="carousel slide spacer-20" data-ride="carousel" data-interval="false">

                 <!-- Indicators -->
                  <ol class="carousel-indicators">
                    <?php for($slide=0;$slide<count($project->gallery);$slide++): ?>
                    <li data-target="#infoCarousel" data-slide-to="<?= $slide?>" <?= !$slide ? 'class="active"' : '' ?> ></li>
                    <?php endfor ?>
                  </ol>

                 <!-- Wrapper for slides -->
                <div class="carousel-inner" role="listbox">
                    <?php foreach($project->gallery as $key => $image): ?>
                        <div class="item <?= !$key ? 'active' : '' ?>">
                            <img src="<?= $image->imageData->getLink(700, 700) ?>" class="img-responsive">
                        </div>
                    <?php endforeach ?>
                </div>

                 <!-- Left and right controls -->
                 <a class="left carousel-control" href="#infoCarousel" role="button" data-slide="prev">
                    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                 </a>
                 <a class="right carousel-control" href="#infoCarousel" role="button" data-slide="next">
                    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                 </a>

                </div>

                <!-- About -->

                <h2 class="pink-title " >
                    <span class="anchor-mark" id="about" >
                    <?= $this->text('overview-field-about') ?>
                    </span>
                </h2>
                <div class="spacer-20 general-text">
                <?php /* nl2br($this->text_url_link($project->about)) */ ?>
                <?= $this->markdown($project->about) ?>
                </div>

                <?php foreach($project->secGallery['about'] as $image): ?>
                    <img src="<?= $image->imageData->getLink(700, 0) ?>" class="spacer-5 img-responsive">
                <?php endforeach ?>

                <h2 class="pink-title spacer" >
                    <span class="anchor-mark" id="motivation">
                    <?= $this->text('overview-field-motivation') ?>
                    </span>
                </h2>
                <div class="spacer-20 general-text">
                <?php /* nl2br($this->text_url_link($project->motivation)) */ ?>
                <?= $this->markdown($project->motivation) ?>
                </div>

                <?php foreach($project->secGallery['motivation'] as $image): ?>
                    <img src="<?= $image->imageData->getLink(700, 0) ?>" class="spacer-5 img-responsive">
                <?php endforeach ?>

                <?php /* Goal is being deprecated */ if($project->goal): ?>
                    <h2 class="pink-title spacer" >
                        <span class="anchor-mark" id="goal">
                        <?= $this->text('overview-field-goal') ?>
                        </span>
                    </h2>
                    <div class="spacer-20 general-text">
                    <?php /* nl2br($this->text_url_link($project->goal)) */ ?>
                    <?= $this->markdown($project->goal) ?>
                    </div>
                <?php endif ?>

                <?php foreach($project->secGallery['goal'] as $image): ?>
                    <img src="<?= $image->imageData->getLink(700, 0) ?>" class="spacer-5 img-responsive">
                <?php endforeach ?>

                <h2 class="pink-title spacer-20" >
                    <span class="anchor-mark" id="related">
                    <?= $this->text('overview-field-related') ?>
                    </span>
                    <?php if (!empty($project->video->url)): ?>
                        <div class="embed-responsive embed-responsive-16by9 spacer-20" >
                        <?= $project->video->getEmbedCode(); ?>
                        </div>
                    <?php endif ?>
                </h2>
                <div class="spacer-20 general-text">
                <?php /* nl2br($this->text_url_link($project->related)) */ ?>
                <?= $this->markdown($project->related) ?>
                </div>

                <?php foreach($project->secGallery['related'] as $image): ?>
                    <img src="<?= $image->imageData->getLink(700, 0) ?>" class="spacer-5 img-responsive">
                <?php endforeach ?>

                <div id="go-top" class="row btn-up">
                    <div class="col-md-offset-5 col-md-3 col-sm-offset-4 col-sm-4 text-center">
                        <button class="btn btn-block dark-grey"><?= $this->text('project-go-up') ?></button></div>
                    </div>
                </div>

            </div>
            <!-- end body -->

          </div>

        </div>

        <!-- End general information -->

            <div class="panel panel-default widget">
                <a class="accordion-toggle" data-toggle="collapse" data-target="#collapse3">
                    <div class="panel-heading">
                        <h2 class="panel-title green-title" >
                            <?= $this->text('project-social-commitment-title') ?>
                            <span class="icon glyphicon glyphicon-menu-down pull-right" aria-hidden="true"></span>
                        </h2>
                        <span class="anchor-mark" id="social-rewards" >
                        </span>
                    </div>
                </a>
                <div id="collapse3" class="panel-collapse collapse in">
                   <div class="panel-body">

                        <?php if($project->social_commitmentData): ?>

                            <div class="row social-commitment">
                                <div class="col-sm-2">
                                    <img class="img-responsive social-img" src="<?= $project->social_commitmentData->image->getLink(60, 60, false) ?>">
                                <h3 class="title text-center">
                                    <?= $project->social_commitmentData->name ?>
                                </h3>
                                </div>
                                <div class="col-sm-10 description">
                                    <?= $project->social_commitment_description ?>
                                </div>
                            </div>

                        <?php endif ?>

                        <?php if($social_rewards || $bonus_rewards): ?>
                            <h2 class="social-reward-title"><?= $this->text('project-share-materials') ?></h2>
                            <ul class="list-unstyled social-rewards" >
                                <?php foreach ($social_rewards as $social): ?>
                                <li class="social-reward">
                                    <h3 class="title"><?= $social->reward ?></h3>
                                    <div class="description"><?= $social->description ?></div>
                                     <!-- Social reward link -->
                                    <?php if ($social->url) : ?>
                                     <div class="row spacer-10">
                                        <div class="col-md-3 col-sm-4">
                                            <a href="<?= $social->url ?>" target="_blank" title="<?= $this->text('social_reward-access_title') ?>"><button class="btn btn-block green"><?= $this->text('social_reward-access') ?></button></a>
                                        </div>
                                     </div>
                                    <?php endif ?>

                                    <!-- License -->
                                    <?php if (!empty($social->license) && array_key_exists($social->license, $this->licenses)): ?>
                                    <h3 class="title">
                                    <?= $this->licenses[$social->license]->name ?>
                                    </h3>
                                    <div class="row">
                                        <div class="col-sm-2 license-img">
                                            <img class="img-responsive" src="<?= SRC_URL . '/assets/img/project/license/'.$social->license.'.png' ?> ">
                                        </div>
                                        <div class="col-sm-10 description">
                                        <?= $this->licenses[$social->license]->description ?>
                                        </div>
                                    </div>
                                    <?php endif ?>
                                </li>
                                <?php endforeach ?>

                                <?php foreach ($bonus_rewards as $social): ?>
                                <li class="social-reward">
                                    <h3 class="title"><?= $social->reward ?></h3>
                                    <div class="description"><?= $social->description ?></div>
                                     <!-- Social reward link -->
                                    <?php if ($social->url) : ?>
                                     <div class="row spacer-10">
                                        <div class="col-md-3 col-sm-4">
                                            <a href="<?= $social->url ?>" target="_blank" title="<?= $this->text('social_reward-access_title') ?>"><button class="btn btn-block green"><?= $this->text('social_reward-access') ?></button></a>
                                        </div>
                                     </div>
                                    <?php endif ?>

                                    <!-- License -->
                                    <?php if (!empty($social->license) && array_key_exists($social->license, $this->licenses)): ?>
                                    <h3 class="title">
                                    <?= $this->licenses[$social->license]->name ?>
                                    </h3>
                                    <div class="row">
                                        <div class="col-xs-2 license-img">
                                            <img class="img-responsive" src="<?= SRC_URL . '/assets/img/project/license/'.$social->license.'.png' ?> ">
                                        </div>
                                        <div class="col-xs-10 description">
                                        <?= $this->licenses[$social->license]->description ?>
                                        </div>
                                    </div>
                                    <?php endif ?>
                                </li>
                                <?php endforeach ?>
                            </ul>

                        <?php endif ?>
                   </div>
                </div>

            </div>

<?php $this->replace() ?>

