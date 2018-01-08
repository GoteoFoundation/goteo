<div class="section advantages" id="advantages" >
	<div class="tabbable full-width-tabs">
        <ul class="nav nav-tabs">
            <li class="donor active">
            	<a href="#tab-donor" data-toggle="tab">
	            		<img src="/assets/img/home/fill_3.png">
                        <?= $this->text('home-advantages-donor-title') ?>
	            </a>
            </li>
            <li class="owner">
                <a href="#tab-owner" data-toggle="tab">
            	   <img src="/assets/img/home/icono_impulsor.png">
                   <?= $this->text('home-advantages-owner-title') ?>
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane advantages-container active donor" id="tab-donor">
                <div class="container">
                	<div class="row details">
                		<div class="col-sm-12 col-md-4 adventage">
                			<div class="title">
                				<span class="icon icon-save-the-world icon-3x"></span>
                				<span class="text">
                				    <?= $this->text('home-adventages-savetheworld-title') ?>
                				</span>
                			</div>
                			<div class="description">
                				<?= $this->text('home-adventages-savetheworld-description') ?>
                			</div>
                		</div>
                		<div class="col-sm-12 col-md-4 adventage">
                			<div class="title">
                                <span class="icon icon-home-certificate icon-3x"></span>
                				<span class="text">
                				    <?= $this->text('home-advantages-certificates-title') ?>
                				</span>
                			</div>
                			<div class="description">
                				<?= $this->text('home-advantages-certificates-description') ?>
                			</div>
                			<div class="action">
                				<a data-toggle="modal" data-target="#WalletVideoModal" href="#" >
	                				<?= $this->text('home-advantages-regular-action') ?>
	                				<span class="icon glyphicon glyphicon glyphicon-menu-right" aria-hidden="true"></span>
                				</a>
                			</div>
                		</div>
                		<div class="col-sm-12 col-md-4 adventage">
                			<div class="title">
                				<span class="icon icon-calculator icon-3x"></span>
                				<span class="text">
                				    <?= $this->text('home-advantages-calculator-title') ?>
                				</span>
                			</div>
                			<div class="description">
                				<?= $this->text('home-advantages-calculator-description') ?>
                			</div>
                			<div class="action">
                				<a href="/calculadora-fiscal" >
	                				<?= $this->text('home-advantages-calculator-action') ?>
	                				<span class="icon glyphicon glyphicon glyphicon-menu-right" aria-hidden="true"></span>
                				</a>
                			</div>
                		</div>
                	</div> <!-- end row -->

                    <div class="impact-title">
                            <?= $this->text('home-advantages-impact-data-title') ?>
                    </div>
                    <div class="row impact-data">
                        <div class="col-sm-4 col-md-4 item">
                            <span class="animate-number"><?= amount_format($this->stats->totalUsers(), 0, true) ?></span>
                            <div class="description">
                              <?= $this->text('home-adventages-impact-data-users-number') ?>
                            </div>
                        </div>
                        <div class="col-sm-4 col-md-4 item">
                            <span class="animate-number"><?= amount_format($this->stats->sucessfulPercentage(), 1, true).'%' ?></span>
                            <div class="description">
                              <?= $this->text('home-advantages-impact-data-success-projects-label') ?>
                            </div>
                        </div>
                        <div class="col-sm-4 col-md-4 item">
                             <span class="animate-number"><?= amount_format($this->stats->totalMoneyFunded(), 0, false) ?></span>
                            <div class="description">
                              <?= $this->text('home-advantages-impact-data-money-label') ?>
                            </div>
                        </div>
                    </div>

                </div> <!-- end container -->

            </div> <!-- end donor -->
            <div class="tab-pane advantages-container owner" id="tab-owner">
            	<div class="container">

                	<div class="row details">
                		<div class="col-sm-12 col-md-3 adventage">
                			<div class="title">
                                <span class="icon icon-projects icon-3x"></span>
                                <span class="text">
                                    <?= $this->text('home-adventages-dashboard-title') ?>
                                </span>
                            </div>
                            <div class="description">
                                <?= $this->text('home-adventages-dashboard-description') ?>
                            </div>
                            <div class="action">
                                <a data-toggle="modal" data-target="#DashboardVideoModal" href="#" >
                                    <?= $this->text('home-advantages-regular-action') ?>
                                    <span class="icon glyphicon glyphicon glyphicon-menu-right" aria-hidden="true"></span>
                                </a>
                            </div>
                		</div>
                		<div class="col-sm-12 col-md-3 adventage">
                			<div class="title">
                				<img src="/assets/img/home/calendar.png" >
                				<span class="text">
                				    <?= $this->text('home-advantages-flex-calendar-title') ?>
                				</span>
                			</div>
                			<div class="description">
                				<?= $this->text('home-advantages-flex-calendar-description') ?>
                			</div>
                		</div>
                		<div class="col-sm-12 col-md-3 adventage">
                			<div class="title">
                				<img src="/assets/img/home/fee.png" >
                				<span class="text">
                				<?= $this->text('home-advantages-fee-title') ?>
                				</span>
                			</div>
                			<div class="description">
                				<?= $this->text('home-advantages-fee-description') ?>
                			</div>
                		</div>
                		<div class="col-sm-12 col-md-3 adventage">
                			<div class="title">
                				<span class="icon icon-call icon-3x"></span>
                				<span class="text">
                				<?= $this->text('home-advantages-matchfunding-title') ?>
                				</span>
                			</div>
                			<div class="description">
                                <?= $this->text('home-advantages-matchfunding-description') ?>
                			</div>
                			<div class="action">
                				<a href="#matchfunding" >
	                				<?= $this->text('home-advantages-regular-action') ?>
	                				<span class="icon glyphicon glyphicon glyphicon-menu-right" aria-hidden="true"></span>
                				</a>
                			</div>
                		</div>
                	</div>

                    <div class="impact-title">
                            <?= $this->text('home-advantages-impact-data-title') ?>
                    </div>
                    <div class="row impact-data">
                        <div class="col-sm-4 col-md-4 item">
                            <span class="animate-number"><?= amount_format($this->stats->totalMoneyFunded(), 0, false) ?></span>
                            <div class="description">
                              <?= $this->text('home-advantages-impact-data-money-label') ?>
                            </div>
                        </div>
                        <div class="col-sm-4 col-md-4 item">
                            <span class="animate-number"><?= amount_format($this->stats->sucessfulPercentage(), 1, true).'%' ?></span>
                            <div class="description">
                                <?= $this->text('home-advantages-impact-data-success-projects-label') ?>
                            </div>
                        </div>
                        <div class="col-sm-4 col-md-4 item">
                            <span class="animate-number"><?= amount_format($this->stats->totalInvestAverage(), 1, false) ?></span>
                            <div class="description">
                              <?= $this->text('home-advantages-impact-data-invest-avg-label') ?>
                            </div>
                        </div>
                    </div>

                </div>

            </div>  <!-- end owner -->
        </div>
    </div> <!-- /tabbable -->
</div>
