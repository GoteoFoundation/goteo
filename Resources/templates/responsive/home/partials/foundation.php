<div class="section adventages foundation" >
    <h2 class="title">
        <?= $this->text('home-foundation-title') ?>
    </h2>
    <div class="container adventages-container">
        <div class="row details">
            <div class="col-md-4 adventage">
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
            <div class="col-md-4 adventage">
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
                	<a href="https//developers.goteo.org" >
	                	<?= $this->text('home-foundation-api-action') ?>
	                	<span class="icon glyphicon glyphicon glyphicon-menu-right" aria-hidden="true"></span>
                	</a>
                </div>
            </div>
            <div class="col-md-4 adventage">
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

        <div class="row impact">
            <div class="col-md-12 title">
                <?= $this->text('home-adventages-impact-data-title') ?>
            </div>
        </div>

        <div class="row impact-data">
            <div class="col-md-2 col-md-offset-3 item">
                <span class="timer count-title count-number" data-to="4503000" data-speed="1500"></span>
                €
                <div class="description">
                    <?= $this->text('home-adventages-impact-data-money-label') ?>
                </div>
            </div>
            <div class="col-md-2 item">
                <span class="timer count-title count-number" data-to="76" data-speed="1500"></span>
                %
                <div class="description">
                    <?= $this->text('home-adventages-impact-data-success-projects-label') ?>
                </div>
            </div>
            <div class="col-md-2 item">
                <span class="timer count-title count-number" data-to="48" data-speed="1500"></span>
                €
                <div class="description">
                    <?= $this->text('home-adventages-impact-data-invest-avg-label') ?>
                </div>
            </div>
        </div>
</div>