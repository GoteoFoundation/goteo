<div id="donate-methods" class="container donate-methods">
	<h2 class="text-center"><?= $this->text('donate-select-amount-title') ?></h2>
	<?= $this->insert('pool/partials/amount_box', [
			'description' => $this->text('donate-select-amount-description'),
			'button_text' => $this->text('landing-donor-button'),
			'form_action' => '/donate/payment',
			'amount'	  => 10
		])
	?>
	<p class="prologue">
		<?= $this->text('donate-select-amount-prologue') ?> <a data-toggle="modal" data-target="#reliefModal" href=""><?= $this->text('regular-here') ?></a>
	</p>
</div>

<!-- Modal -->
<div class="modal fade" id="reliefModal" tabindex="-1" role="dialog" aria-labelledby="poolModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="poolModalLabel"><?= $this->text('donate-modal-relief-title') ?></h4>
      </div>
      <div class="modal-body">
        <?= $this->text('donate-modal-relief-description') ?>
      </div>
    </div>
  </div>
</div>
