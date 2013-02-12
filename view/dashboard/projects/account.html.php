<?php
use Goteo\Core\View,
    Goteo\Library\Text;

$project = $this['project'];
$account = $this['account'];
?>
<div class="widget projects">
    <h2 class="title"><?php echo Text::get('contract-accounts_title') ?></h2>
<form method="post" action="/dashboard/projects/account/save" >
    <input type="hidden" name="project" value="<?php echo $project->id; ?>" />

    <p>
        <label for="bank-account"><?php echo Text::get('contract-bank_account') ?></label><br />
        <input type="text" id="bank-account" name="bank" value="<?php echo $account->bank; ?>" style="width:350px;" />
    </p>

    <p>
        <label for="account-bank_owner"><?php echo Text::get('contract-bank_owner') ?></label><br />
        <input type="text" id="account-bank_owner" name="bank_owner" value="<?php echo $account->bank_owner; ?>" style="width: 475px;"/>
    </p>

    <p>
        <label for="paypal-account"><?php echo Text::get('contract-paypal_account') ?></label><br />
        <input type="text" id="paypal-account" name="paypal" value="<?php echo $account->paypal; ?>" style="width:350px;" />
    </p>

    <p>
        <label for="account-paypal_owner"><?php echo Text::get('contract-paypal_owner') ?></label><br />
        <input type="text" id="account-paypal_owner" name="paypal_owner" value="<?php echo $account->paypal_owner; ?>" style="width: 475px;"/>
    </p>

    <input type="submit" name="save" value="<?php echo Text::get('form-apply-button') ?>" />
</form>
</div>
