<div class="section adventages" >
	<div class="tabbable full-width-tabs">
        <ul class="nav nav-tabs">
            <li class="donor active">
            	<a href="#tab-donor" data-toggle="tab">
	            		<img src="/assets/img/home/fill_3.png">
                        <?= $this->text('home-adventages-donor-title') ?>
	            </a>
            </li>
            <li class="owner">
                <a href="#tab-owner" data-toggle="tab">
            	   <img src="/assets/img/home/icono_impulsor.png">
                   <?= $this->text('home-adventages-owner-title') ?>
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane adventages-container active donor" id="tab-donor">
                <div class="container">
                	<div class="row details">
                		<div class="col-md-4 adventage">
                			<div class="title">
                				<img src="/assets/img/home/monedero.png" >
                				<span class="text">                				
                				    <?= $this->text('home-adventages-wallet-title') ?>
                				</span>
                			</div>
                			<div class="description">
                				<?= $this->text('home-adventages-wallet-description') ?>
                			</div>
                			<div class="action">
                				<a href="/calculadora-fiscal" >
	                				<?= $this->text('home-adventages-wallet-action') ?>
	                				<span class="icon glyphicon glyphicon glyphicon-menu-right" aria-hidden="true"></span>
                				</a>
                			</div>
                		</div>
                		<div class="col-md-4 adventage">
                			<div class="title">
                				<img src="/assets/img/home/certificados.png" >
                				<span class="text">                				
                				    <?= $this->text('home-adventages-certificates-title') ?>
                				</span>
                			</div>
                			<div class="description">
                				<?= $this->text('home-adventages-certificates-description') ?>
                			</div>
                			<div class="action">
                				<a href="/calculadora-fiscal" >
	                				<?= $this->text('home-adventages-certificates-action') ?>
	                				<span class="icon glyphicon glyphicon glyphicon-menu-right" aria-hidden="true"></span>
                				</a>
                			</div>
                		</div>
                		<div class="col-md-4 adventage">
                			<div class="title">
                				<img src="/assets/img/home/calculadora.png" >
                				<span class="text">                				
                				    <?= $this->text('home-adventages-calculator-title') ?>
                				</span>
                			</div>
                			<div class="description">
                				<?= $this->text('home-adventages-calculator-description') ?>
                			</div>
                			<div class="action">
                				<a href="/calculadora-fiscal" >
	                				<?= $this->text('home-adventages-calculator-action') ?>
	                				<span class="icon glyphicon glyphicon glyphicon-menu-right" aria-hidden="true"></span>
                				</a>
                			</div>
                		</div>
                	</div> <!-- end row -->
                <div class="row more-information">
                		<div class="col-md-12">
	                		<div class="text-center">
	                		<?= $this->text('home-adventages-donor-more-description') ?>
	                		</div>
	                		<div class="text-center action">
	                			<a href="/about#info12" >
			                		<?= $this->text('home-adventages-donor-more-action') ?>
			                		<span class="icon glyphicon glyphicon glyphicon-menu-right" aria-hidden="true">
		                			</span>
		                		</a>
	                		</div>
                		</div>
                	</div>
                </div>
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
            </div> <!-- end donor -->
            <div class="tab-pane adventages-container owner" id="tab-owner">
            	<div class="container">
                	<div class="row details">
                		<div class="col-md-3 adventage">
                			<div class="title">
                                <img src="/assets/img/home/calculadora.png" >
                                <span class="text">                             
                                    <?= $this->text('home-adventages-calculator-title') ?>
                                </span>
                            </div>
                            <div class="description">
                                <?= $this->text('home-adventages-calculator-description') ?>
                            </div>
                            <div class="action">
                                <a href="/calculadora-fiscal" >
                                    <?= $this->text('home-adventages-calculator-action') ?>
                                    <span class="icon glyphicon glyphicon glyphicon-menu-right" aria-hidden="true"></span>
                                </a>
                            </div>
                		</div>
                		<div class="col-md-3 adventage">
                			<div class="title">
                				<img src="/assets/img/home/calendar.png" >
                				<span class="text">                				
                				    <?= $this->text('home-adventages-flex-calendar-title') ?>
                				</span>
                			</div>
                			<div class="description">
                				<?= $this->text('home-adventages-flex-calendar-description') ?>
                			</div>
                			<div class="action">
                				<a href="/calculadora-fiscal" >
	                				<?= $this->text('home-adventages-regular-action') ?>
	                				<span class="icon glyphicon glyphicon glyphicon-menu-right" aria-hidden="true"></span>
                				</a>
                			</div>
                		</div>
                		<div class="col-md-3 adventage">
                			<div class="title">
                				<img src="/assets/img/home/fee.png" >
                				<span class="text">                				
                				<?= $this->text('home-adventages-fee-title') ?>
                				</span>
                			</div>
                			<div class="description">
                				<?= $this->text('home-adventages-fee-description') ?>
                			</div>
                			<div class="action">
                				<a href="/calculadora-fiscal" >
	                				<?= $this->text('home-adventages-regular-action') ?>
	                				<span class="icon glyphicon glyphicon glyphicon-menu-right" aria-hidden="true"></span>
                				</a>
                			</div>
                		</div>
                		<div class="col-md-3 adventage">
                			<div class="title">
                				<img src="/assets/img/home/matchfunding.png" >
                				<span class="text">                				
                				<?= $this->text('home-adventages-matchfunding-title') ?>
                				</span>
                			</div>
                			<div class="description">
                                <?= $this->text('home-adventages-matchfunding-description') ?>
                			</div>
                			<div class="action">
                				<a href="/calculadora-fiscal" >
	                				<?= $this->text('home-adventages-regular-action') ?>
	                				<span class="icon glyphicon glyphicon glyphicon-menu-right" aria-hidden="true"></span>
                				</a>
                			</div>
                		</div>
                	</div>

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
            </div>  <!-- end owner -->
        </div> 
    </div> <!-- /tabbable -->
</div>