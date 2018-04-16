<?php $project=$this->project; ?>

<?php

$share_url = $this->get_url() . '/project/' . $this->project->id;

$author_twitter = str_replace(
                        array(
                            'https://',
                            'http://',
                            'www.',
                            'twitter.com/',
                            '#!/',
                            '@'
                        ), '', $project->user->twitter);
$author = !empty($author_twitter) ? ' '.$this->text('regular-by').' @'.$author_twitter.' ' : '';
$share_title = $project->name . $author;

$facebook_url = 'http://facebook.com/sharer.php?u=' . urlencode($share_url) . '&t=' . urlencode($share_title);
$twitter_url = 'http://twitter.com/intent/tweet?text=' . urlencode($share_title . ': ' . $share_url . ' #Goteo');

$langs = $project->getLangs();

?>

        <div class="col-md-8">
                <div class="row spacer-20">
                    <div class="col-sm-6 tags-location" id="tags-location">

                        <?php if (!empty($project->cat_names)) : ?>

                        <div class="tags hidden-xs" id="tags">
                            <img class="tags" src="<?= SRC_URL . '/assets/img/project/tags.png' ?>">
                            <?php $sep = '';
                            foreach ($project->cat_names as $key=>$value) :
                                echo $sep.'<a href="/discover?category=' . $key . '" class="tag">'.htmlspecialchars($value).'</a>';
                                $sep = ', ';
                            endforeach; ?>
                        </div>

                        <?php endif; ?>

                        <!--Location -->
                        <div class="location spacer-20" id="location">
                            <div class="pull-left location-container">
                                <img class="location" src="<?= SRC_URL . '/assets/img/project/location.png' ?>">
                                <span class="tag"><?= $project->project_location ?></span>
                            </div>
                        </div>

                        <?php if (count($langs) > 1) : ?>

                        <div class="project-langs hidden-xs">
                            <span class="icon-globe glyphicon glyphicon-globe" aria-hidden="true"></span>
                            <?php $sep = '';
                            foreach ($langs as $key=>$value) :
                                echo $sep.'<a href="' . $this->lang_url($key) . '" class="tag">' . $value . '</a>';
                                $sep = ', ';
                            endforeach; ?>
                        </div>

                        <?php endif; ?>
                    </div>
                    <div class="col-sm-6 share-project-container">
                        <div>
                        <?= $this->text('project-share-header') ?>
                        </div>
                        <div class="row share-project">
                            <div class="col-xs-2 col-xs-offset-1 col-sm-offset-0">
                                <a href="<?= $facebook_url ?>">
                                    <img class="facebook" src="<?= SRC_URL . '/assets/img/project/facebook.png' ?>">
                                </a>
                            </div>
                            <div class="col-xs-2">
                                <a href="<?= $twitter_url ?>">
                                    <img class="twitter" src="<?= SRC_URL . '/assets/img/project/twitter.png' ?>">
                                </a>
                            </div>
                            <div class="col-xs-2 visible-xs visible-sm">
                                <a href="tg://msg?text=<?= urlencode($share_title).' '.$share_url ?>">
                                    <img class="telegram" src="<?= SRC_URL . '/assets/img/project/telegram.png' ?>">
                                </a>
                            </div>
                            <div class="col-xs-2 visible-xs">
                                <a href="whatsapp://send?text=<?= urlencode($share_title).' '.$share_url ?>" data-action="share/whatsapp/share" >
                                    <img class="whatsapp" src="<?= SRC_URL . '/assets/img/project/whatsapp.png' ?>">
                                </a>
                            </div>
                            <div class="col-xs-2">
                                <img id="show-link" class="link cursor-pointer" src="<?= SRC_URL . '/assets/img/project/link.png' ?>">
                            </div>
                            <div class="col-md-6 col-sm-4 hidden-xs">
                                <a data-toggle="modal" data-target="#widgetModal" href="" >
                                    <button class="btn btn-block grey">
                                        <span class="hidden-sm">
                                        <?= $this->text('project-spread-widget') ?>
                                        </span>
                                        <span class="visible-sm">
                                        <?= $this->text('dashboard-menu-projects-widgets') ?>
                                        </span>
                                    </button>
                                </a>
                            </div>
                        </div>
                        <div class="row no-margin spacer-10" id="link-box" style="display:none;">
                            <input type="text" class="form-control" value="<?= $share_url ?>" >
                        </div>

                        <!-- Call in sm version -->
                        <?php if($project->called): ?>
                            <a href="<?= $this->get_url() ?>/call/<?= $project->called->id ?>/projects" target="_blank">
                                <div class="call-info-container visible-sm">
                                    <div class="row call-info col-lg-10 col-md-11 col-sm-12">
                                        <div class="col-xs-2 no-padding" >
                                            <img src="<?= SRC_URL . '/assets/img/project/drop.svg' ?>" class="img-responsive">
                                            <div class="label-call" >
                                            <?= $this->text('node-side-sumcalls-header') ?>
                                            </div>
                                        </div>
                                        <div class="col-xs-10 info-default-call" >
                                            <div class="header-text"><?= $project->called->user->name.' '.$this->text('call-project-get') ?></div>
                                            <div class="call-name">
                                                <?= $project->called->name ?>
                                            </div>
                                        </div>
                                        <div class="col-xs-10 info-hover-call display-none" >
                                            <div class="header-text"><?= $project->called->user->name.' '.$this->text('call-project-get') ?></div>
                                            <div class="call-name">
                                            <?= $this->text('project-call-got', amount_format($project->amount_call), $project->called->user->name) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        <?php endif; ?>
                        <!-- end call in sm version -->

                    </div>
                </div>

                <?= $this->insert('project/partials/responsive_meter.php', ['project' => $project ]) ?>

                <div id="project-tabs-menu" class="row spacer project-menu hidden-xs">
                    <a href="/project/<?= $project->id ?>" class="pronto" data-pronto-target="#project-tabs" data-pronto-scroll-to="#project-tabs-menu">
                        <div class="home col-xs-4 text-center item <?= $this->show=='home' ? 'current' : '' ?>" id="home">
                                <img class="" src="<?= SRC_URL . '/assets/img/project/home.png' ?>">
                                <span class="label-item"><?= $this->text('project-menu-home') ?></span>
                        </div>
                    </a>
                    <a href="/project/<?= $project->id ?>/updates" class="pronto" data-pronto-target="#project-tabs" data-pronto-scroll-to="#project-tabs-menu">
                        <div class="updates col-xs-4 text-center item <?= $this->show=='updates' ? 'current' : '' ?>" id="updates">
                                <img class="" src="<?= SRC_URL . '/assets/img/project/news.png' ?>">
                                <span class="label-item"><?= $this->text('project-menu-news') ?></span>
                        </div>
                    </a>
                    <a href="/project/<?= $project->id ?>/participate" class="pronto" data-pronto-target="#project-tabs" data-pronto-scroll-to="#project-tabs-menu">
                        <div class="participate col-xs-4 text-center item <?= $this->show=='participate' ? 'current' : '' ?>" id="participate">
                                <img class="" src="<?= SRC_URL . '/assets/img/project/participate.png' ?>">
                                <span class="label-item"><?= $this->text('project-menu-participate') ?></span>
                        </div>
                    </a>
                </div>
            </div>

            <?php if($project->called): ?>
            <a href="<?= $this->get_url() ?>/call/<?php echo $project->called->id ?>/projects" target="_blank">
                <div class="col-sm-4 call-info-container hidden-sm hidden-xs">
                    <div class="row call-info col-lg-10 col-md-11 col-sm-12">
                        <div class="col-xs-2 no-padding" >
                            <img src="<?= SRC_URL . '/assets/img/project/drop.svg' ?>" class="img-responsive">
                        </div>
                        <div class="col-xs-10 info-default-call" >
                            <div class="header-text"><?= $project->called->user->name.' '.$this->text('call-project-get') ?></div>
                            <div class="call-name">
                                <?= $project->called->name ?>
                            </div>
                        </div>
                        <div class="col-xs-10 info-hover-call display-none" >
                            <div class="header-text"><?= $project->called->user->name.' '.$this->text('call-project-get') ?></div>
                            <div class="call-name">
                            <?= $this->text('project-call-got', amount_format($project->amount_call), $project->called->user->name) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
            <?php endif; ?>

            <?php if ($project->node !== $this->get_config('current_node')&&($project->nodeData->active)) : ?>

                    <div class="col-sm-4 hidden-sm hidden-xs channel" style="<?= !$project->called ? 'margin-top: 55px;' : '' ?>">
                        <span class="channel-label">
                            <img src="/assets/img/project/channel.svg" width="20"> <?= $this->text('regular-channel') ?>
                        </span>
                        <a href="<?= $project->nodeData->url ?>">
                            <button class="btn" style="<?= $project->nodeData->owner_background ? 'background-color: '.$project->nodeData->owner_background :  '' ?>" >
                            <?= $project->nodeData->name ?>
                            </button>
                        </a>
                    </div>
            <?php endif; ?>

             <div class="panel panel-default widget rewards rewards-collapsed visible-xs">
                <a class="accordion-toggle collapsed" data-toggle="collapse" data-target="#collapseRewards">
                    <div class="panel-heading">
                        <h2 class="panel-title green-title" >
                            <?= $this->text('project-rewards-side-title') ?>
                            <span class="icon glyphicon glyphicon-menu-down pull-right" aria-hidden="true"></span>
                        </h2>
                    </div>
                </a>
                <div id="collapseRewards" class="panel-collapse collapse">
                   <div class="panel-body">

                        <?php foreach ($this->individual_rewards as $individual) : ?>
                        <div class="side-widget">

                            <div class="amount"><?= $this->text('regular-investing').' '.amount_format($individual->amount); ?></div>
                            <div class="text-bold spacer-20"><?= $individual->reward ?></div>
                            <div class="spacer-20"><?= $this->text_url_link($individual->description) ?></div>

                            <div class="investors">
                                <?= '> '.sprintf("%02d", $individual->taken).' '.$this->text('project-view-metter-investors') ?>
                            </div>
                            <?php if ($project->status ==3 && !$individual->none) : ?>

                                <div class="row spacer-5">
                                    <div class="col-sm-6">
                                        <?php if($individual->none): ?>
                                        <a href="<?= '/invest/'.$project->id.'/payment?amount='.$individual->amount ?>"><button class="btn btn-block side-pink"><?= $this->text('landing-donor-button') ?></button></a>
                                        <?php else: ?>
                                        <a href="<?= '/invest/'.$project->id.'/payment?reward='.$individual->id ?>"><button class="btn btn-block side-pink"><?= $this->text('regular-getit') ?></button></a>
                                        <?php endif; ?>
                                    </div>
                                </div>

                            <?php endif; ?>

                        </div>
                        <?php endforeach ?>


                    </div>
                   <!-- End panel body -->
                </div>

            </div>



            <div class="row spacer project-menu visible-xs">
                    <a href="/project/<?= $project->id ?>" class="pronto" data-pronto-target="#project-tabs" data-pronto-scroll-to="#project-tabs-menu">
                        <div class="home col-xs-4 text-center item <?= $this->show=='home' ? 'current' : '' ?>" id="home">
                                <img class="" src="<?= SRC_URL . '/assets/img/project/home.png' ?>">
                                <span class="label-item"><?= $this->text('project-menu-home') ?></span>
                        </div>
                    </a>
                    <a href="/project/<?= $project->id ?>/updates" class="pronto" data-pronto-target="#project-tabs" data-pronto-scroll-to="#project-tabs-menu">
                        <div class="updates col-xs-4 text-center item <?= $this->show=='updates' ? 'current' : '' ?>" id="updates">
                                <img class="" src="<?= SRC_URL . '/assets/img/project/news.png' ?>">
                                <span class="label-item"><?= $this->text('project-menu-news') ?></span>
                        </div>
                    </a>
                    <a href="/project/<?= $project->id ?>/participate" class="pronto" data-pronto-target="#project-tabs" data-pronto-scroll-to="#project-tabs-menu">
                        <div class="participate col-xs-4 text-center item <?= $this->show=='participate' ? 'current' : '' ?>" id="participate">
                                <img class="" src="<?= SRC_URL . '/assets/img/project/participate.png' ?>">
                                <span class="label-item"><?= $this->text('project-menu-participate') ?></span>
                        </div>
                    </a>
                </div>
        </div>
        <!-- end tags and share info -->
    </div>
