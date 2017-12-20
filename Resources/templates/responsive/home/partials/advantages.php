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
                		<div class="col-md-4 adventage">
                			<div class="title">
                				<img src="/assets/img/home/monedero.png" >
                				<span class="text">                				
                				    <?= $this->text('home-adventages-savetheworld-title') ?>
                				</span>
                			</div>
                			<div class="description">
                				<?= $this->text('home-adventages-savetheworld-description') ?>
                			</div>
                			<div class="action">
                				<a href="/calculadora-fiscal" >
	                				<?= $this->text('home-adventages-savetheworld-action') ?>
	                				<span class="icon glyphicon glyphicon glyphicon-menu-right" aria-hidden="true"></span>
                				</a>
                			</div>
                		</div>
                		<div class="col-md-4 adventage">
                			<div class="title">
                				<img src="/assets/img/home/certificados.png" >
                				<span class="text">                				
                				    <?= $this->text('home-advantages-certificates-title') ?>
                				</span>
                			</div>
                			<div class="description">
                				<?= $this->text('home-advantages-certificates-description') ?>
                			</div>
                			<div class="action">
                				<a href="/calculadora-fiscal" >
	                				<?= $this->text('home-advantages-certificates-action') ?>
	                				<span class="icon glyphicon glyphicon glyphicon-menu-right" aria-hidden="true"></span>
                				</a>
                			</div>
                		</div>
                		<div class="col-md-4 adventage">
                			<div class="title">
                				<img src="/assets/img/home/calculadora.png" >
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
                        <div class="col-md-2 col-md-offset-3 item">
                            <?= amount_format($this->stats->getTotalUsers(), 0, true) ?>
                            <div class="description">
                              <?= $this->text('home-adventages-impact-data-users-number') ?>
                            </div>
                        </div>
                        <div class="col-md-2 item">
                            <?= amount_format($this->stats->getSucessfulPercentage(), 1, true).'%' ?>
                            <div class="description">
                              <?= $this->text('home-advantages-impact-data-success-projects-label') ?>
                            </div>
                        </div>
                        <div class="col-md-2 item">
                             <?= amount_format($this->stats->getTotalMoneyFunded(), 0, false) ?>
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
                		<div class="col-md-3 adventage">
                			<div class="title">
                                <img src="/assets/img/home/calculadora.png" >
                                <span class="text">                             
                                    <?= $this->text('home-advantages-calculator-owner-title') ?>
                                </span>
                            </div>
                            <div class="description">
                                <?= $this->text('home-advantages-calculator-owner-description') ?>
                            </div>
                            <div class="action">
                                <a href="/calculadora-fiscal" >
                                    <?= $this->text('home-advantages-calculator-owner-action') ?>
                                    <span class="icon glyphicon glyphicon glyphicon-menu-right" aria-hidden="true"></span>
                                </a>
                            </div>
                		</div>
                		<div class="col-md-3 adventage">
                			<div class="title">
                				<img src="/assets/img/home/calendar.png" >
                				<span class="text">                				
                				    <?= $this->text('home-advantages-flex-calendar-title') ?>
                				</span>
                			</div>
                			<div class="description">
                				<?= $this->text('home-advantages-flex-calendar-description') ?>
                			</div>
                			<div class="action">
                				<a href="/calculadora-fiscal" >
	                				<?= $this->text('home-advantages-regular-action') ?>
	                				<span class="icon glyphicon glyphicon glyphicon-menu-right" aria-hidden="true"></span>
                				</a>
                			</div>
                		</div>
                		<div class="col-md-3 adventage">
                			<div class="title">
                				<img src="/assets/img/home/fee.png" >
                				<span class="text">                				
                				<?= $this->text('home-advantages-fee-title') ?>
                				</span>
                			</div>
                			<div class="description">
                				<?= $this->text('home-advantages-fee-description') ?>
                			</div>
                			<div class="action">
                				<a href="/calculadora-fiscal" >
	                				<?= $this->text('home-advantages-regular-action') ?>
	                				<span class="icon glyphicon glyphicon glyphicon-menu-right" aria-hidden="true"></span>
                				</a>
                			</div>
                		</div>
                		<div class="col-md-3 adventage">
                			<div class="title">
                				<img src="/assets/img/home/matchfunding.png" >
                				<span class="text">                				
                				<?= $this->text('home-advantages-matchfunding-title') ?>
                				</span>
                			</div>
                			<div class="description">
                                <?= $this->text('home-advantages-matchfunding-description') ?>
                			</div>
                			<div class="action">
                				<a href="/calculadora-fiscal" >
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
                        <div class="col-md-2 col-md-offset-3 item">
                            <span>
                            <?= amount_format($this->stats->getTotalMoneyFunded(), 0, false) ?>
                            <div class="description">
                              <?= $this->text('home-advantages-impact-data-money-label') ?>
                            </div>
                        </div>
                        <div class="col-md-2 item">
                            <?= amount_format($this->stats->getSucessfulPercentage(), 1, true).'%' ?>
                            <div class="description">
                                <?= $this->text('home-advantages-impact-data-success-projects-label') ?>
                            </div>
                        </div>
                        <div class="col-md-2 item">
                            <?= amount_format($this->stats->getTotalInvestAverage(), 1, false) ?>
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