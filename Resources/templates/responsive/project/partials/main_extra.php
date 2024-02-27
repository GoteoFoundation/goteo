<?php
$project=$this->project;
$matchers=$this->matchers;

$share_url = $this->get_url() . '/project/' . $this->project->id;

$author_twitter = str_replace(
                        [
                            'https://',
                            'http://',
                            'www.',
                            'twitter.com/',
                            '#!/',
                            '@'
                        ], '', $project->user->twitter);
$author = !empty($author_twitter) ? ' '.$this->text('regular-by').' @'.$author_twitter.' ' : '';
$share_title = $this->ee($project->name) . $author;

$facebook_url = 'http://facebook.com/sharer.php?u=' . urlencode($share_url) . '&t=' . urlencode($share_title);
$twitter_url = 'http://twitter.com/intent/tweet?text=' . urlencode($share_title . ': ' . $share_url . ' #Goteo');

$langs = $project->getLangs();

?>

        <div class="col-md-8">
                <div class="row spacer-20">
                    <ul class="col-sm-6 tags-location" id="tags-location">
                        <?php if (!empty($project->cat_names)) : ?>
                            <li class="tags hidden-xs list-unstyled" id="tags">
                                <ul class="list-inline">
                                    <img class="tags" src="<?= SRC_URL . '/assets/img/project/tags.png' ?>" alt="<?= $this->t('regular-categories') ?>">
                                    <?php foreach ($project->cat_names as $key=>$value): ?>
                                        <li>
                                            <a href="/discover?category='<?= $key ?>'" class="tag"><?= htmlspecialchars($value) ?></a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                        <?php endif; ?>

                        <!--Location -->
                        <li class="location spacer-20 list-unstyled" id="location">
                            <img class="location" src="<?= SRC_URL . '/assets/img/project/location.png' ?>" alt="<?= $this->t('regular-location') ?>" >
                            <span class="tag"><?= $project->project_location ?></span>
                        </li>

                        <?php if (count($langs) > 1) : ?>
                            <li class="spacer-20 list-unstyled">
                                <ul class="project-langs hidden-xs list-inline">
                                    <span class="icon-globe glyphicon glyphicon-globe" aria-hidden="true"></span>
                                    <?php foreach($langs as $key=>$value): ?>
                                        <li><a href="<?= $this->lang_url($key) ?>" class="tag"><?= $value ?></a></li>
                                    <?php endforeach ?>
                                </ul>
                            </li>
                        <?php endif; ?>
                    </ul>
                    <div class="col-sm-6 share-project-container">
                        <div>
                            <?= $this->text('project-share-header') ?>
                        </div>
                        <ul class="row share-project list-inline">
                            <li class="col-xs-2 col-xs-offset-1 col-sm-offset-0">
                                <a href="<?= $facebook_url ?>">
                                    <img class="facebook" alt="share facebook" src="<?= SRC_URL . '/assets/img/project/facebook.png' ?>">
                                </a>
                            </li>
                            <li class="col-xs-2">
                                <a href="<?= $twitter_url ?>">
                                    <img class="twitter" alt="share twitter" src="<?= SRC_URL . '/assets/img/project/twitter.png' ?>">
                                </a>
                            </li>
                            <li class="col-xs-2 visible-xs visible-sm">
                                <a href="tg://msg?text=<?= urlencode($share_title).' '.$share_url ?>">
                                    <img class="telegram" alt="share telegram" src="<?= SRC_URL . '/assets/img/project/telegram.png' ?>">
                                </a>
                            </li>
                            <li class="col-xs-2 visible-xs">
                                <a href="whatsapp://send?text=<?= urlencode($share_title).' '.$share_url ?>" data-action="share/whatsapp/share" >
                                    <img class="whatsapp" alt="share whatsapp" src="<?= SRC_URL . '/assets/img/project/whatsapp.png' ?>">
                                </a>
                            </li>
                            <li class="col-xs-2">
                                <img id="show-link" class="link cursor-pointer" src="<?= SRC_URL . '/assets/img/project/link.png' ?>" alt="<?= $this->t('regular-share_this') ?>">
                            </li>
                            <li class="col-md-6 col-sm-4 hidden-xs">
                                <button class="btn btn-block grey" data-toggle="modal" data-target="#widgetModal">
                                    <span class="hidden-sm">
                                        <?= $this->text('project-spread-widget') ?>
                                    </span>
                                    <span class="visible-sm">
                                        <?= $this->text('dashboard-menu-projects-widgets') ?>
                                    </span>
                                </button>
                            </li>
                        </ul>
                        <div class="row no-margin spacer-10" id="link-box" style="display:none;">
                            <input type="text" class="form-control" value="<?= $share_url ?>" title="<?= $this->t('regular-url') ?>">
                        </div>

                        <!-- Call in sm version -->
                        <?php if($project->called): ?>
                            <a href="<?= $this->get_url() ?>/call/<?= $project->called->id ?>/projects" target="_blank">
                                <div class="call-info-container visible-sm">
                                    <div class="row call-info col-lg-10 col-md-11 col-sm-12">
                                        <div class="col-xs-2 no-padding" >
                                            <img width="35" src="<?= SRC_URL . '/assets/img/project/drop.svg' ?>" class="img-responsive" alt="">
                                            <div class="label-call" >
                                                <?= $this->text('node-side-sumcalls-header') ?>
                                            </div>
                                        </div>
                                        <div class="col-xs-10 info-default-call" >
                                            <div class="header-text">
                                                <?= $project->called->user->name.' '.$this->text('call-project-get') ?>
                                            </div>
                                            <div class="call-name">
                                                <?= $project->called->name ?>
                                            </div>
                                        </div>
                                        <div class="col-xs-10 info-hover-call display-none" >
                                            <div class="header-text">
                                                <?= $project->called->user->name.' '.$this->text('call-project-get') ?>
                                            </div>
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

                <?php if ($project->type = 'campaign'): ?>
                    <?= $this->insert('project/partials/responsive_meter.php', ['project' => $project ]) ?>
                <?php endif; ?>

                <div id="project-tabs-menu" class="row spacer project-menu hidden-xs">
                    <a href="/project/<?= $project->id ?>" class="pronto" data-pronto-target="#project-tabs" data-pronto-scroll-to="#project-tabs-menu">
                        <div class="home col-xs-4 text-center item <?= $this->show=='home' ? 'current' : '' ?>" id="home">
                            <img class="" src="<?= SRC_URL . '/assets/img/project/home.png' ?>" alt="">
                            <span class="label-item"><?= $this->text('project-menu-home') ?></span>
                        </div>
                    </a>
                    <a href="/project/<?= $project->id ?>/updates" class="pronto" data-pronto-target="#project-tabs" data-pronto-scroll-to="#project-tabs-menu">
                        <div class="updates col-xs-4 text-center item <?= $this->show=='updates' ? 'current' : '' ?>" id="updates">
                            <img class="" src="<?= SRC_URL . '/assets/img/project/news.png' ?>" alt="">
                            <span class="label-item"><?= $this->text('project-menu-news') ?></span>
                        </div>
                    </a>
                    <a href="/project/<?= $project->id ?>/participate" class="pronto" data-pronto-target="#project-tabs" data-pronto-scroll-to="#project-tabs-menu">
                        <div class="participate col-xs-4 text-center item <?= $this->show=='participate' ? 'current' : '' ?>" id="participate">
                            <img class="" src="<?= SRC_URL . '/assets/img/project/participate.png' ?>" alt="">
                            <span class="label-item"><?= $this->text('project-menu-participate') ?></span>
                        </div>
                    </a>
                </div>
            </div>

    <div class="col-md-4">
        <?php if($project->called): ?>
            <div class="row">
                <a href="<?= $this->get_url() ?>/call/<?php echo $project->called->id ?>/projects" target="_blank">
                    <div class="call-info-container hidden-sm hidden-xs">
                        <div class="row call-info col-lg-10 col-md-11 col-sm-12">
                            <div class="col-xs-2 no-padding" >
                                <img src="<?= SRC_URL . '/assets/img/project/drop.svg' ?>" class="img-responsive" alt="">
                            </div>
                            <div class="col-xs-10 info-default-call" >
                                <div class="header-text"><?= $project->called->user->name.' '.$this->text('call-project-get') ?></div>
                                <div class="call-name">
                                    <?= $project->called->name ?>
                                </div>
                            </div>
                            <div class="col-xs-10 info-hover-call display-none" >
                                <div class="header-text">
                                    <?= $project->called->user->name.' '.$this->text('call-project-get') ?>
                                </div>
                                <div class="call-name">
                                    <?= $this->text('project-call-got', amount_format($project->amount_call), $project->called->user->name) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        <?php elseif($matchers): ?>
            <div class="row">
                <div class="slider slider-matchers" id="matchers">
                    <?php foreach ($matchers as $matcher): ?>
                        <?php
                            $matcher_amount=$matcher->calculateProjectAmount($project->id);
                            $matcher_vars=$matcher->getVars();
                            $max_project= $matcher_vars['max_amount_per_project'] ?: $matcher_vars['donation_per_project'];
                            $progress=round(($matcher_amount/$max_project)*100);
                        ?>
                        <div class="matcher-info hidden-sm hidden-xs">
                            <div>
                                <img width="30" src="<?= $this->asset('img/project/drop.svg') ?>" class="matcher-logo" alt="<?= $this->t('regular-matcher') ?>">
                                <span class="matcher-label">
                                    <?= $this->text('matcher-label-project') ?>
                                </span>
                                <?php if($matcher->matchesRewards()):?>
                                    <?php
                                        $matchedRewards = $matcher->getMatchingRewards();
                                        $matcherRewardsNames = [];

                                        foreach($matchedRewards as $matchedReward) {
                                            $matcherRewardsNames[] = $matchedReward->getReward()->reward;
                                        }
                                    ?>
                                    <span style="float:right; margin-left: 5px;">
                                        <?= $this->t('matcher-limited-by-amount') ?>
                                    </span>
                                    <i class="fa fa-info-circle"
                                       style="float:right"
                                       data-html="true"
                                       data-container="body"
                                       data-toggle="tooltip"
                                       title=""
                                       data-original-title="<span style='font-size: 16px;'><?= $this->text('matcher-limited-by-amount-title', implode('<br>', $matcherRewardsNames)) ?></span>">
                                    </i>
                                <?php endif; ?>
                            </div>
                            <div class="matcher-description">
                                <div class="logo-container">
                                    <img src="<?= $matcher->getLogo()->getLink(150, 150, true) ?>" class="logo" alt="<?= $matcher->name ?>">
                                </div>
                                <div class="info-container">
                                    <div class="matcher-name">
                                        <?= $matcher->name ?>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar <?= $progress==100 ? 'progress-completed' : '' ?> " role="progressbar" aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?= $progress ?>%">
                                        </div>
                                    </div>

                                    <?php if($progress==100): ?>
                                        <div class="matcher-amount">
                                            <span class="completed">
                                                 <i class="fa fa-check-circle"></i>
                                                <?= $this->text('matcher-label-completed') ?>
                                            </span>
                                            <span class="figure">
                                                <?= '('.amount_format($matcher_amount).')' ?>
                                            </span>
                                        </div>
                                    <?php else: ?>
                                        <div class="matcher-amount">
                                            <?= $this->text('matcher-amount-project', ['%AMOUNT%' => amount_format($matcher_amount), '%PROJECT_AMOUNT%' => amount_format($max_project)] ) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($project->sign_url): ?>
            <div class="row">
                <div class="hidden-sm hidden-xs sign <?= !$project->called && empty($project->getMatchers()) ? 'spacer-40' : 'spacer-20' ?>">
                    <a href="<?= $project->sign_url ?>" target="_blank" class="btn btn-sign col-sm-10 text-uppercase">
                        <?= $project->sign_url_action ?>
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($this->channels) || isset($project->node)):?>
            <div class="hidden-sm hidden-xs channel spacer-bottom-20 <?= ($project->called || $matchers || $project->sign_url) ? 'spacer-20' : '' ?>">
                <?= $this->insert('project/partials/channel_slider.php', ['channels' => $this->channels]) ?>
            </div>
        <?php endif; ?>
    </div>

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
                   <ul class="panel-body list-unstyled">
                        <?php foreach ($this->individual_rewards as $individual) : ?>
                            <li class="side-widget">
                                <h3 class="amount"><?= $this->text('regular-investing').' '.amount_format($individual->amount); ?></h3>
                                <div class="text-bold spacer-20"><?= $individual->reward ?></div>
                                <div class="spacer-20"><?= $this->markdown($individual->description) ?></div>
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
                            </li>
                        <?php endforeach ?>
                    </ul>
                   <!-- End panel body -->
                </div>
            </div>

            <div class="row spacer project-menu visible-xs">
                <a href="/project/<?= $project->id ?>" class="pronto" data-pronto-target="#project-tabs" data-pronto-scroll-to="#project-tabs-menu">
                    <div class="home col-xs-4 text-center item <?= $this->show=='home' ? 'current' : '' ?>" id="home">
                        <img class="" src="<?= SRC_URL . '/assets/img/project/home.png' ?>" alt="">
                        <span class="label-item"><?= $this->text('project-menu-home') ?></span>
                    </div>
                </a>
                <a href="/project/<?= $project->id ?>/updates" class="pronto" data-pronto-target="#project-tabs" data-pronto-scroll-to="#project-tabs-menu">
                    <div class="updates col-xs-4 text-center item <?= $this->show=='updates' ? 'current' : '' ?>" id="updates">
                        <img class="" src="<?= SRC_URL . '/assets/img/project/news.png' ?>" alt="">
                        <span class="label-item"><?= $this->text('project-menu-news') ?></span>
                    </div>
                </a>
                <a href="/project/<?= $project->id ?>/participate" class="pronto" data-pronto-target="#project-tabs" data-pronto-scroll-to="#project-tabs-menu">
                    <div class="participate col-xs-4 text-center item <?= $this->show=='participate' ? 'current' : '' ?>" id="participate">
                        <img class="" src="<?= SRC_URL . '/assets/img/project/participate.png' ?>" alt="">
                        <span class="label-item"><?= $this->text('project-menu-participate') ?></span>
                    </div>
                </a>
            </div>
        </div>
        <!-- end tags and share info -->
    </div>
