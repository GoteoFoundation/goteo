<?php
use Goteo\Core\View,
    Goteo\Model\Project\Account;

$accounts = Account::get($_SESSION['project']->id);

?>
<div class="widget projects">
    <h2 class="title">Acuerdo</h2>
</div>

<div class="widget projects">
    <h2 class="title">Cuentas bancarias del proyecto</h2>
<form method="post" action="/dashboard/projects/contract/save" >
    <input type="hidden" name="project" value="<?php echo $_SESSION['project']->id; ?>" />
<p>
    <label for="bank-account">Cuenta bancaria:</label><br />
    <input type="text" id="bank-account" name="bank" value="<?php echo $accounts->bank; ?>" style="width:350px;" />
</p>

<p>
    <label for="paypal-account">Cuenta PayPal:</label><br />
    <input type="text" id="paypal-account" name="paypal" value="<?php echo $accounts->paypal; ?>" style="width:350px;" />
</p>

    <input type="submit" name="save" value="Guardar cambios" />
</form>
</div>
