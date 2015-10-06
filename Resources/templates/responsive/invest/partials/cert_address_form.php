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
