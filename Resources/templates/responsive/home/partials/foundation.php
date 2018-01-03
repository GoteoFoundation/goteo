<div class="section advantages foundation" id="foundation" >
    <div class="container advantages-container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="title">
                    <?= $this->text('home-foundation-title') ?>
                </h2>
            </div>
        </div>
        <div class="slider slider-fade" id="stories">

            <?php foreach($this->stories as $story): ?>
                <div class="row">
                    <div class="col-md-6">
                        <img class="img-responsive" src="<?= $story->image->getLink(600, 400, true) ?>" >
                    </div>
                    <div class="col-md-6">
                        <div class="info-container">
                            <div class="type-container">
                                <span class="type">
                                <?= $story->review ?>
                                </span>
                            </div>
                            <div class="description">
                                <div class="pull-left quote">
                                    <i class="fa fa-quote-left"></i>
                                </div>
                                <div class="pull-left text">
                                    <?= $story->description ?>
                                    <i class="fa fa-quote-right pull-right"></i>
                                </div>
                            </div>
                            <div class="author" >
                                <?= "- ".$story->title ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>

        <div class="row details" id="foundation-advantages">
            <div class="col-sm-12 col-md-4 adventage">
                <div class="title">
                	<img src="/assets/img/home/graph_2.png" >
                	<span class="text">
                	<?= $this->text('home-foundation-stats-title') ?>
                	</span>
                </div>
                <div class="description">
                	<?= $this->text('home-foundation-stats-description') ?>
                </div>

                <div class="more-description">
                    <?= $this->text('home-foundation-stats-more-description') ?>
                </div>
                <div class="action">
                	<a href="https://stats.goteo.org" >
	                   <?= $this->text('home-foundation-stats-action') ?>
	                   <span class="icon glyphicon glyphicon glyphicon-menu-right" aria-hidden="true"></span>
                	</a>
                </div>
            </div>
            <div class="col-sm-12 col-md-4 adventage">
                <div class="title">
                	<img src="/assets/img/home/api.png" >
                	<span class="text">
                	<?= $this->text('home-foundation-api-title') ?>
                	</span>
                </div>
                <div class="description">
                	<?= $this->text('home-foundation-api-description') ?>
                </div>
                <div class="more-description">
                   <?= $this->text('home-foundation-api-more-description') ?>
                </div>
                <div class="action">
                	<a href="https://developers.goteo.org" >
	                	<?= $this->text('home-foundation-api-action') ?>
	                	<span class="icon glyphicon glyphicon glyphicon-menu-right" aria-hidden="true"></span>
                	</a>
                </div>
            </div>
            <div class="col-sm-12 col-md-4 adventage">
                <div class="title">
                	<img src="/assets/img/home/goteo_logo.png" >
                	<span class="text">
                		<?= $this->text('home-foundation-goteo-values-title') ?>
                	</span>
                </div>
                <div class="description">
                	<?= $this->text('home-foundation-goteo-values-description') ?>
                </div>
                <div class="more-description">
                    <?= $this->text('home-foundation-goteo-values-more-description') ?>
                </div>
                <div class="action">
                	<a href="/about" >
	                	<?= $this->text('home-foundation-goteo-values-action') ?>
	                	 <span class="icon glyphicon glyphicon glyphicon-menu-right" aria-hidden="true"></span>
                	</a>
                </div>
            </div>
        </div> <!-- end row -->

    </div>
    <!-- Impact data -->

    <!-- Donor module -->
    <div class="fluid-container donor-container" >
        <div class="container">
            <div class="row">
                <div class="col-sm-12 col-md-5">
                    <img src="<?= $this->asset('img/home/team.png') ?>" class="img-responsive">
                </div>
                <div class="col-sm-9 col-md-5">
                    <div class="title">
                    <?= $this->text('home-foundation-team-title') ?>
                    </div>
                    <div class="description">
                        <?= $this->text('home-foundation-team-description') ?>
                    </div>
                </div>
                <div class="col-sm-3 col-md-2 col-button">
                    <a href="https://fundacion.goteo.org/donaciones/" target="_blank" class="btn btn-white">
                        <?= $this->text('home-foundation-team-action') ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- end Donor module -->

    <div class="fluid-container data-container" >
        <div class="container">
            <div class="impact-title">
                    <?= $this->text('home-advantages-impact-data-title') ?>
            </div>

            <div class="row impact-data">
                <div class="col-sm-4 col-md-4 item">
                    <span class="animate-number"><?= $this->stats->getAdvisedProjects() ?></span>
                    <div class="description">
                        <?= $this->text('home-adventages-impact-data-advised-projects') ?>
                    </div>
                </div>
                <div class="col-sm-4 col-md-4 item">
                    <span class="animate-number">1042</span>
                    <div class="description">
                        <?= $this->text('home-adventages-impact-data-workshops-participants') ?>
                    </div>
                </div>
                <div class="col-sm-4 col-md-4 item">
                    <span class="animate-number"><?= $this->stats->getFundedProjects() ?></span>
                    <div class="description">
                        <?= $this->text('home-adventages-impact-data-funded-projects') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
