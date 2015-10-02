<?php

$this->layout('layout', [
    'bodyClass' => '',
    'title' => 'Make sure :: Goteo.org',
    'meta_description' => $this->text('meta-description-discover')
    ]);

$this->section('content');


?>
<div class="container">

	<div class="row row-form">
			<div class="panel panel-default make-sure">
				<div class="panel-body">
					<div class="alert alert-success col-md-10 col-md-offset-1" role="alert">
        			<?= $this->text('invest-success-make-sure',$this->project->name) ?>
      				</div>

                    <?= $this->supply('sub-header', $this->get_session('sub-header')) ?>

					<h3 class="col-md-offset-1 padding-bottom-6">Asegúrate de que recibes tu recompensa</h3>

					<form class="form-horizontal" id="make-sure-form" role="form" method="POST" action="">

						<div class="form-group">
							<div class="col-md-10 col-md-offset-1">
								<input type="input" class="form-control" placeholder="<?= $this->text('invest-address-name-field') ?>" name="reward-fullname" id="reward-fullname" required>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-10 col-md-offset-1">
								<input type="input" class="form-control" placeholder="<?= $this->text('invest-address-address-field') ?>" name="reward-address" value="" id="reward-address" required>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-10 col-md-offset-1">
								<input type="input" class="form-control" placeholder="<?= $this->text('invest-address-zipcode-field') ?>" name="reward-zipcode" value="" id="reward-zipcode" required>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-10 col-md-offset-1">
								<input type="input" class="form-control" placeholder="<?= $this->text('invest-address-location-field') ?>" name="reward-location" id="reward-location" required>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-10 col-md-offset-1">
								<input type="input" class="form-control" placeholder="<?= $this->text('invest-address-country-field') ?>" name="reward-country" id="reward-country" required>
							</div>
						</div>

						<hr class="make-sure">

						

							<h3 class="col-md-offset-1">Asegúrate que puedes desgravar tu recompensa</h3>

							<div class="form-group">
								<div class="col-md-10 col-md-offset-1">
									<div class="checkbox">
										<label>
											<input type="checkbox" name="fiscalrefused" id="fiscalrefused">
												<p>
												No quiero un certificado de donación. <a data-toggle="modal" data-target="#myModal" href="">Más información.</a>.
												</p>
										</label>
									</div>
								</div>
							</div>

							<div id="fiscal-information">

							<div class="form-group">
								<div class="col-md-10 col-md-offset-1">
									<input type="input" class="form-control" placeholder="<?= $this->text('invest-address-name-field') ?>" name="fiscal-fullname" id="fiscal-fullname" value="" required>
								</div>
							</div>

							<div class="form-group">
								<div class="col-md-10 col-md-offset-1">
									<input type="input" class="form-control" placeholder="<?= $this->text('invest-address-nif-field') ?>" name="fiscal-nif" value="" id="fiscal-nif" required>
								</div>
							</div>

							<div class="form-group">
								<div class="col-md-10 col-md-offset-1">
									<input type="input" class="form-control" placeholder="<?= $this->text('invest-address-address-field') ?>" name="fiscal-address" value="" id="fiscal-address" required>
								</div>
							</div>

							<div class="form-group">
								<div class="col-md-10 col-md-offset-1">
									<input type="input" class="form-control" placeholder="<?= $this->text('invest-address-zipcode-field') ?>" name="fiscal-zipcode" value="" id="fiscal-zipcode" required>
								</div>
							</div>

							<div class="form-group">
								<div class="col-md-10 col-md-offset-1">
									<input type="input" class="form-control" placeholder="<?= $this->text('invest-address-location-field') ?>" name="fiscal-location" id="fiscal-location" required>
								</div>
							</div>

							<div class="form-group">
								<div class="col-md-10 col-md-offset-1">
									<input type="input" class="form-control" placeholder="<?= $this->text('invest-address-country-field') ?>" name="fiscal-country" id="fiscal-country" required>
								</div>
							</div>

						</div>
						<!-- End fiscal information -->

						<hr>

						<div class="form-group">
							<div class="col-md-10 col-md-offset-1">
								<button type="submit" class="btn btn-block btn-success">Guardar</button>
							</div>
						</div>

					</form>


				</div>
			</div>
	</div>
</div>

<?php $this->replace() ?>

<?php $this->section('footer') ?>
<script type="text/javascript">

	$("#fiscalrefused").change(function(){
    if($(this).is(":checked")){
		$("#fiscal-information").hide();
		$("[id^='fiscal-']").each(function(){
			tmpID=$(this).attr("id");
			$('#'+tmpID).attr('required',false);
		})
    }else{
		$("#fiscal-information").show();
		$("[id^='fiscal-']").each(function(){
			tmpID=$(this).attr("id");
			$('#'+tmpID).attr('required',true);
		})
      }
	})

	$("[id^='fiscal-']").each(function(){
			tmpID=$(this).attr("id");
			if( !$(this).val() )
				$('#'+tmpID).addClass( "empty" );
	})

	$("[id^='reward-']").change(function(){
			data=$(this).attr("id");

			tmpID = data.split('reward-');
			tmpValue=$.trim($(this).val());

			if ( $( "#fiscal-"+tmpID[1] ).hasClass( "empty" ) )
				$("#fiscal-"+tmpID[1]).val(tmpValue);

	})

	$("[id^='fiscal-']").change(function(){
			tmpID=$(this).attr("id");

			$('#'+tmpID).removeClass( "empty" );
	})

	/*$("#rewardtofiscal").change(function(){
    if($(this).is(":checked")){
		$("[id^='fiscal-']").each(function(){
			data=$(this).attr("id")
			tmpID = data.split('fiscal-');
			$(this).val($("#reward-"+tmpID[1]).val())
		})
    }else{
		$("[id^='fiscal-']").each(function(){
			$(this).val("")
		})
      }
})*/

</script>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Información fiscal</h4>
      </div>
      <div class="modal-body">
        Como ya debes saber, aportar en Goteo no sólo hace posibles un montón de proyectos que nos benefician a todxs, sino que ¡además desgrava! <p>Al realizar una aportación a un proyecto, y gracias al incremento en los tipos de deducción aplicables desde 2015 para las donaciones puras y simples que se realicen en favor de la Fundación Goteo, como entidad acogida al régimen fiscal especial de la Ley 49/2002, de 23 de diciembre.</p>
      </div>
    </div>
  </div>
</div>
<?php $this->append() ?>
