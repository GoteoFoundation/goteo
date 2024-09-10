<?php

$project=$this->project;

if($this->is_pronto()):
    echo json_encode([
        'title' => $this->project->name,
        'content' => $this->supply('main-content')
        ]);
    return;
endif;

if($project->image)
        $meta_img=$project->image->getLink(700, 700, false, true);
elseif($project->gallery[0]){
    if($project->secGallery['play-video'][0])
        $meta_img=$this->project->secGallery['play-video'][0]->imageData->getLink(780, 478, false, true);
    else
        $meta_img=$project->gallery[0]->imageData->getLink(700, 700, false, true);
}


$this->layout('layout', [
    'bodyClass' => 'project',
    'title' => $this->project->name,
    'meta_description' => $this->ee($this->project->subtitle),
    'tw_image' => $meta_img,
    'og_image' => $meta_img
    ]);


$this->section('lang-metas');
    $langs = $project->getLangs();
    if (count($langs) > 1) {
        foreach($langs as $l => $lang) {
            if($l == $this->lang_current()) continue;
            echo  "\n\t" . '<link rel="alternate" href="' . $this->lang_url($l) .'" hreflang="' . $l . '" />';
        }
    }
$this->replace();

$this->section('head');

?>


<link rel="stylesheet" type="text/css" href="<?= SRC_URL ?>/assets/vendor/slick-carousel/slick/slick.css"/>
<link rel="stylesheet" type="text/css" href="<?= SRC_URL ?>/assets/vendor/slick-carousel/slick/slick-theme.css"/>


<?php

$this->append();


$this->section('sidebar-header');
    echo $this->insert('project/widgets/micro', ['project' => $project, 'admin' => $this->admin]);
$this->replace();

$this->supply('announcements', $this->insert("partials/components/announcements"));

$this->section('content');

?>

<div class="container-fluid main-info">
	<div class="container-fluid">
		<div class="row header text-center">
			<h1 class="project-title"><?= $this->markdown($this->ee($project->name)) ?></h1>
			<div class="project-by"><a href="/user/<?= $project->owner ?>"><?= $project->user->name ?></a></div>
		</div>

        <div class="row">
            <div class="col-sm-8">
                <?= $this->insert('project/partials/media', ['project' => $project ]) ?>
            </div>
            <div class="col-sm-4">
                <?= $this->insert('project/partials/meter', ['project' => $project ]) ?>
            </div>
        </div>

		<!-- Tags and share info -->
		<div class="row">
		    <?= $this->insert('project/partials/main_extra', ['project' => $project, 'matchers' => $this->matchers ]) ?>
		</div>
</div>

<!-- End container fluid -->

<div class="container-fluid section">
    <?php if ($project->isImpactCalcActive()):?>
        <div class="impact-calculator-details row">
            <?= $this->insert('project/partials/impact_by_footprint', ['footprints' => $this->footprints ]) ?>
            <?= $this->insert('project/partials/calculator') ?>
            <?= $this->insert('project/partials/sdgs') ?>
        </div>
    <?php endif; ?>

    <!-- show rewards of type patreon if active -->
    <?php if ($project->isPermanent()):?>
        <div class="row">
            <?= $this->insert('project/partials/highlighted_rewards') ?>
        </div>
    <?php endif; ?>

	<div class="col-sm-8 section-content" id="project-tabs">
	    <?= $this->supply('main-content') ?>
	</div>

	<!-- end Panel group -->

	<div class="col-sm-4 side">
	    <?= $this->insert('project/partials/side', ['project' => $project]) ?>
	</div>
	<!-- end side -->
</div>

<?= $this->insert('project/partials/related_projects') ?>

<!-- sticky menu -->
<div class="sticky-menu" data-offset-top="880" data-spy="affix">
	<div class="container-fluid">
		<div class="row">
			<a href="/project/<?= $project->id ?>" class="pronto" data-pronto-target="#project-tabs" data-pronto-scroll-to="#project-tabs-menu">
				<div class="home col-sm-2 hidden-xs sticky-item <?= $this->show=='home' ? 'current' : '' ?>">
					<img class="" src="<?= SRC_URL . '/assets/img/project/home.png' ?>" alt="">
		            <span class="label-sticky-item"><?= $this->text('project-menu-home') ?></span>
				</div>
			</a>
			<a href="/project/<?= $project->id ?>/updates"  class="pronto" data-pronto-target="#project-tabs" data-pronto-scroll-to="#project-tabs-menu">
				<div class="updates col-sm-2 hidden-xs sticky-item <?= $this->show=='updates' ? 'current' : '' ?>">
					<img class="" src="<?= SRC_URL . '/assets/img/project/news.png' ?>" alt="">
	                <span class="label-sticky-item"><?= $this->text('project-menu-news') ?></span>
				</div>
			</a>
			<a href="/project/<?= $project->id ?>/participate" class="pronto" data-pronto-target="#project-tabs" data-pronto-scroll-to="#project-tabs-menu">
				<div class="participate col-sm-2 hidden-xs sticky-item <?= $this->show=='participate' ? 'current' : '' ?>">
					<img class="" src="<?= SRC_URL . '/assets/img/project/participate.png' ?>" alt="">
	                <span class="label-sticky-item"><?= $this->text('project-menu-participate') ?></span>
				</div>
			</a>

            <div class="col-xs-6 col-sm-3 col-md-2 col-md-offset-2 col-xs-offset-1 sticky-button">
                <?php if($project->inCampaign()): ?>
                    <a href="/invest/<?= $project->id ?>"><button class="btn btn-block side-pink"><?= $this->text('project-regular-support') ?></button></a>
                <?php endif ?>
            </div>

            <?php if(!$this->get_user() ): ?>
        		<a href="/project/favourite/<?= $project->id ?>">
    		<?php endif ?>
	            <div class="pull-left text-right favourite <?= $this->get_user()&&$this->get_user()->isFavouriteProject($project->id) ? 'active' : '' ?>" >
	                <span class="heart-icon glyphicon glyphicon-heart" aria-hidden="true"></span>
	                <span> <?= $this->text('project-view-metter-favourite') ?></span>
	            </div>
            <?php if(!$this->get_user() ): ?>
        		</a>
    		<?php endif ?>
		</div>
	</div>
</div>

<!-- Modal -->
<div class="modal fade" id="widgetModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><?= $this->text('project-spread-pre_widget') ?></h4>
      </div>
      <div class="modal-body">
        <div class="row">
        	<div class="col-sm-6">
        	    <?= $this->raw('widget_code') ?>
        	</div>
        	<div class="col-sm-6">
     			<textarea class="widget-code" onclick="this.focus();this.select()" readonly="readonly" title="<?= $this->t('project-spread-widget') ?>">
                    <?= $this->widget_code ?>
                </textarea>
        	</div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php $this->replace() ?>


<?php $this->section('footer') ?>


<?= $this->insert('project/partials/chart_costs.php', ['project' => $project]) ?>

<?= $this->insert('project/partials/chart_amount.php', ['project' => $project]) ?>

<?= $this->insert('project/partials/javascript') ?>

<?= $this->insert('partials/facebook_pixel', ['pixel' => $this->project->facebook_pixel]) ?>

<?php $this->append() ?>
