<?php
use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Model\Project\Account;

$project = $this['project'];

if (!$project instanceof  Goteo\Model\Project) {
    return;
}

$accounts = Account::get($project->id);

?>
<!--
<div class="widget projects">
    <h2 class="title">Acuerdo</h2>
</div>
-->

<div class="widget projects">
    <h2 class="title">Cuentas bancarias del proyecto</h2>
<form method="post" action="/dashboard/projects/contract/save" >
    <input type="hidden" name="project" value="<?php echo $project->id; ?>" />

    <p>
        <label for="bank-account">Cuenta bancaria:</label><br />
        <input type="text" id="bank-account" name="bank" value="<?php echo $accounts->bank; ?>" style="width:350px;" />
    </p>

    <p>
        <label for="account-bank_owner">Titular de la cuenta bancaria:</label><br />
        <input type="text" id="account-bank_owner" name="bank_owner" value="<?php echo $accounts->bank_owner; ?>" style="width: 475px;"/>
    </p>

    <p>
        <label for="paypal-account">Cuenta PayPal:</label><br />
        <input type="text" id="paypal-account" name="paypal" value="<?php echo $accounts->paypal; ?>" style="width:350px;" />
    </p>

    <p>
        <label for="account-paypal_owner">Titular de la cuenta paypal:</label><br />
        <input type="text" id="account-paypal_owner" name="paypal_owner" value="<?php echo $accounts->paypal_owner; ?>" style="width: 475px;"/>
    </p>

    <input type="submit" name="save" value="<?php echo Text::get('form-apply-button') ?>" />
</form>
</div>
