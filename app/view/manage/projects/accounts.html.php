<?php

use Goteo\Library\Text,
    Goteo\Model,
    Goteo\Core\Redirection,
    Goteo\Application\Message;

$project = $vars['project'];

if (!$project instanceof Model\Project) {
    Message::Error('Instancia de proyecto corrupta');
    throw new Redirection('/manage/projects');
}

$accounts = $vars['accounts'];
?>
<div class="widget">
    <form method="post" action="/manage/projects" >
        <input type="hidden" name="id" value="<?php echo $project->id ?>" />

    <p>
        <label for="account-bank">Cuenta bancaria:</label><br />
        <input type="text" id="account-bank" name="bank" value="<?php echo $accounts->bank; ?>" style="width: 475px;"/>
    </p>

    <p>
        <label for="account-paypal">Cuenta PayPal:</label><br />
        <input type="text" id="account-paypal" name="paypal" value="<?php echo $accounts->paypal; ?>" style="width: 475px;"/>
    </p>

        <input type="submit" name="save-accounts" value="Guardar" />

    </form>
</div>
