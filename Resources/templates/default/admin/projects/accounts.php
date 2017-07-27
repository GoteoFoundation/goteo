<?php $this->layout('admin/projects/edit_layout') ?>

<?php $this->section('admin-project-content') ?>

    <p>Es necesario que un proyecto tenga una cuenta PayPal para ejecutar los cargos. La cuenta bancaria es solamente para tener toda la información en el mismo entorno pero no se utiliza en ningún proceso de este sistema.</p>

    <form method="post" action="/admin/projects/accounts/<?= $this->project->id ?>" >

    <p>
        <label for="account-bank">Cuenta bancaria:</label><br />
        <input type="text" id="account-bank" name="bank" value="<?= $this->accounts->bank; ?>" style="width: 475px;"/>
    </p>

    <p>
        <label for="account-bank_owner">Titular de la cuenta bancaria:</label><br />
        <input type="text" id="account-bank_owner" name="bank_owner" value="<?= $this->accounts->bank_owner; ?>" style="width: 475px;"/>
    </p>

    <p>
        <label for="account-paypal">Cuenta PayPal:</label><br />
        <input type="text" id="account-paypal" name="paypal" value="<?= $this->accounts->paypal; ?>" style="width: 475px;"/>
    </p>

    <p>
        <label for="account-paypal_owner">Titular de la cuenta PayPal:</label><br />
        <input type="text" id="account-paypal_owner" name="paypal_owner" value="<?= $this->accounts->paypal_owner; ?>" style="width: 475px;"/>
    </p>

    <p>
        <label for="account-skip_login">
        <input type="checkbox" id="account-skip_login" name="skip_login" value="1" <?= $this->accounts->skip_login ? 'checked="checked"' : '' ?>/>
        Permitir aportes sin registro
        </label>
    </p>

        <input type="submit" name="save-accounts" value="Guardar" />

    </form>


<?php $this->replace() ?>
