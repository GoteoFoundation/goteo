
    <h3 class="col-md-offset-1 padding-bottom-6"><?= $this->text('invest-address-title') ?></h3>

<?php foreach(['name', 'address', 'zipcode', 'location'] as $part): ?>
    <div class="form-group<?= in_array($part, $this->a('invest_errors')) ? ' has-error' : '' ?>">
        <div class="col-md-10 col-md-offset-1">
            <input type="input" class="form-control" placeholder="<?= $this->text('invest-address-' . $part . '-field') ?>" name="invest[<?= $part ?>]" id="invest-<?= $part ?>" value="<?= $this->invest_address[$part] ?>" required>
        </div>
    </div>
<?php endforeach ?>

    <div class="form-group<?= in_array('country', $this->a('invest_errors')) ? ' has-error' : '' ?>">
        <div class="col-md-10 col-md-offset-1">
            <?= $this->html('input',
                    ['type' => 'select',
                    'name' => 'invest[country]',
                    'id' => 'invest-country',
                    'value' => strtoupper($this->invest_address['country']),
                    'value' => '',
                    'attribs' => [
                        'class' => 'form-control',
                        'placeholder' => $this->text('invest-address-country-field'),
                    ],
                    'options' => $this->list_countries()
                ]) ?>
        </div>
    </div>
