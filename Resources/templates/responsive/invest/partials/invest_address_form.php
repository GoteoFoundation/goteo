<h3 class="clear-both col-md-offset-1 padding-bottom-2 clear-both"><?= $this->text('invest-address-title') ?></h3>


<?php foreach(['name', 'address', 'zipcode', 'location'] as $part): ?>

    <div class="form-group<?= in_array($part, $this->a('invest_errors')) ? ' has-error' : '' ?>">
        <label for="invest-<?= $part ?>" class="col-md-10 col-md-offset-1"><?= $this->text('invest-address-' . $part . '-field') ?></label>
        <div class="col-md-7 col-md-offset-1">
            <input type="input" class="form-control" placeholder="<?= $this->text('invest-address-' . $part . '-field') ?>" name="invest[<?= $part ?>]" id="invest-<?= $part ?>" value="<?= $this->a('invest_address')[$part] ?>" required>
        </div>
    </div>

<?php endforeach ?>

    <div class="form-group<?= in_array('country', $this->a('invest_errors')) ? ' has-error' : '' ?>">
        <label for="invest-country" class="col-md-10 col-md-offset-1"><?= $this->text('invest-address-country-field') ?></label>
        <div class="col-md-7 col-md-offset-1">
            <?= $this->html('input',
                    ['type' => 'select',
                    'name' => 'invest[country]',
                    'id' => 'invest-country',
                    'value' => strtoupper($this->a('invest_address')['country']),
                    'attribs' => [
                        'class' => 'form-control',
                        'placeholder' => $this->text('invest-address-country-field'),
                    ],
                    'options' => $this->list_countries()
                ]) ?>
        </div>
    </div>
