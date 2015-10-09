
    <h3 class="col-md-offset-1 padding-bottom-6">Asegúrate de que recibes tu recompensa</h3>

    <div class="form-group">
        <div class="col-md-10 col-md-offset-1">
            <input type="input" class="form-control" placeholder="<?= $this->text('invest-address-name-field') ?>" name="invest[name]" id="invest-name" value="<?= $this->invest_address['name'] ?>" required>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-10 col-md-offset-1">
            <input type="input" class="form-control" placeholder="<?= $this->text('invest-address-address-field') ?>" name="invest[address]" id="invest-address" value="<?= $this->invest_address['address'] ?>" required>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-10 col-md-offset-1">
            <input type="input" class="form-control" placeholder="<?= $this->text('invest-address-zipcode-field') ?>" name="invest[zipcode]" id="invest-zipcode" value="<?= $this->invest_address['zipcode'] ?>" required>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-10 col-md-offset-1">
            <input type="input" class="form-control" placeholder="<?= $this->text('invest-address-location-field') ?>" name="invest[location]" id="invest-location" value="<?= $this->invest_address['location'] ?>" required>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-10 col-md-offset-1">
            <input type="input" class="form-control" placeholder="<?= $this->text('invest-address-country-field') ?>" name="invest[country]" id="invest-country" value="<?= $this->invest_address['country'] ?>" required>
        </div>
    </div>
