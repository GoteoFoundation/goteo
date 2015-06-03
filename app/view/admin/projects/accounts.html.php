<?php

use Goteo\Library\Text,
    Goteo\Model,
    Goteo\Core\Redirection,
    Goteo\Application\Message;

$project = $vars['project'];

if (!$project instanceof Model\Project) {
    Message::Error('Instancia de proyecto corrupta');
    throw new Redirection('/admin/projects');
}

$accounts = $vars['accounts'];
?>
<div class="widget">
    <p>Es necesario que un proyecto tenga una cuenta PayPal para ejecutar los cargos. La cuenta bancaria es solamente para tener toda la información en el mismo entorno pero no se utiliza en ningún proceso de este sistema.</p>

    <form method="post" action="/admin/projects" >
        <input type="hidden" name="id" value="<?php echo $project->id ?>" />

    <p>
        <label for="account-bank">Cuenta bancaria:</label><br />
        <input type="text" id="account-bank" name="bank" value="<?php echo $accounts->bank; ?>" style="width: 475px;"/>
    </p>

    <p>
        <label for="account-bank_owner">Titular de la cuenta bancaria:</label><br />
        <input type="text" id="account-bank_owner" name="bank_owner" value="<?php echo $accounts->bank_owner; ?>" style="width: 475px;"/>
    </p>

    <p>
        <label for="account-paypal">Cuenta PayPal:</label><br />
        <input type="text" id="account-paypal" name="paypal" value="<?php echo $accounts->paypal; ?>" style="width: 475px;"/>
    </p>

    <p>
        <label for="account-paypal_owner">Titular de la cuenta PayPal:</label><br />
        <input type="text" id="account-paypal_owner" name="paypal_owner" value="<?php echo $accounts->paypal_owner; ?>" style="width: 475px;"/>
    </p>

        <input type="submit" name="save-accounts" value="Guardar" />

    </form>
</div>
