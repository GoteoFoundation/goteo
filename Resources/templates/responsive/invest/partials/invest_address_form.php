<h3 class="clear-both padding-bottom-2 clear-both"><?= $this->text('invest-address-title') ?></h3>


<?php foreach(['name', 'address', 'location', 'zipcode'] as $part): ?>

    <div class="form-group<?= in_array($part, $this->a('invest_errors')) ? ' has-error' : '' ?>">
        <label for="invest-<?= $part ?>"><?= $this->text('invest-address-' . $part . '-field') ?></label>
        <div class="input-wrap">
            <input type="text" class="form-control<?php
            if($part == 'address') {
                echo ' geo-autocomplete" data-geocoder-populate-address="#invest-address" data-geocoder-populate-city="#invest-location" data-geocoder-populate-region="#invest-region" data-geocoder-populate-zipcode="#invest-zipcode" data-geocoder-populate-country_code="#invest-country" data-geocoder-populate-latitude="#invest-latitude" data-geocoder-populate-longitude="#invest-longitude';
            }
            if($part == 'location') {
                echo ' geo-autocomplete" data-geocoder-populate-address="#invest-address" data-geocoder-populate-city="#invest-location" data-geocoder-populate-region="#invest-region" data-geocoder-populate-zipcode="#invest-zipcode" data-geocoder-populate-country_code="#invest-country" data-geocoder-populate-latitude="#invest-latitude" data-geocoder-populate-longitude="#invest-longitude';
            }
            ?>" placeholder="<?= $this->text('invest-address-' . $part . '-field') ?>" name="invest[<?= $part ?>]" id="invest-<?= $part ?>" value="<?= $this->a('invest_address')[$part] ?>" required>
        </div>
    </div>

<?php endforeach ?>

    <div class="form-group<?= in_array('country', $this->a('invest_errors')) ? ' has-error' : '' ?>">
        <label for="invest-country"><?= $this->text('invest-address-country-field') ?></label>
        <div class="input-wrap">
            <?= $this->html('input',
                    ['type' => 'select',
                    'name' => 'invest[country]',
                    'value' => strtoupper($this->a('invest_address')['country']),
                    'attribs' => [
                        'id' => 'invest-country',
                        'class' => 'form-control',
                        'placeholder' => $this->text('invest-address-country-field'),
                    ],
                    'options' => $this->list_countries()
                ]) ?>
        </div>
    </div>

    <input type="hidden" id="invest-region" name="invest[region]" value="<?= $this->a('invest_address')['region'] ?>">
    <input type="hidden" id="invest-latitude" name="invest[latitude]" value="<?= $this->a('invest_address')['latitude'] ?>">
    <input type="hidden" id="invest-longitude" name="invest[longitude]" value="<?= $this->a('invest_address')['longitude'] ?>">
