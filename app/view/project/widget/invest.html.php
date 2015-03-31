<?php

use Goteo\Core\View,
    Goteo\Library\Worth,
    Goteo\Model\User,
    Goteo\Model\Invest,
    Goteo\Model\Call,
    Goteo\Library\Text,
    Goteo\Model\License,
    Goteo\Library\Currency;

$project = $this['project'];

// cantidad de aporte
if (isset($_SESSION['invest-amount'])) {
    $amount = $_SESSION['invest-amount'];
    unset($_SESSION['invest-amount']);
} elseif (!empty($_GET['amount'])) {
    $amount = str_replace(array(',', '.'), '', $_GET['amount']);
} else {
    $amount = 0;
}

// verificar si puede obtener riego
if ($project->called instanceof Call && $project->called->dropable) {
    $call = $project->called;
    $rest = $call->rest;
    $maxdrop = Call\Project::setMaxdrop($project);

    // a ver si este usuario ya ha regado este proyecto
    if ($_SESSION['user'] instanceof User) {
        $allready = $call->getSupporters(true, $_SESSION['user']->id, $project->id);
    } else {
        $allready = false;
    }
    $allowpp = false;
} else {
    $call = null;

    $allowpp = $project->allowpp;
}

$personal = $this['personal'];
$step = $this['step'];

$level = (int) $this['level'] ?: 3;

$worthcracy = Worth::getAll();

$licenses = array();

foreach (License::getAll() as $l) {
    $licenses[$l->id] = $l;
}

$action = ($step == 'start') ? '/user/login' : '/invest/' . $project->id;

$select_currency=Currency::$currencies[$_SESSION['currency']]['html'];
?>
<div class="widget project-invest project-invest-amount">
    <h<?php echo $level ?> class="title"><?php echo Text::get('invest-amount') ?></h<?php echo $level ?>>

    <form method="post" action="<?php echo SEC_URL.$action; ?>">
    <div style="position:relative;">

        <label><input type="text" id="amount"  name="amount" class="amount" value="<?php echo $amount ?>" /><?php echo Text::get('invest-amount-tooltip') ?></label>
        <span class="symbol"><?php echo $select_currency; ?></span>
    </div>
</div>

<?php if ($call && !empty($call->amount)) : ?>
<div class="widget project-invest project-called">
<?php if ($allready > 0) : ?>
    <p><?php echo Text::html('invest-called-allready', $call->name) ?></p>
<?php elseif ($project->amount_call >= $call->maxproj) : ?>
    <p><?php echo Text::html('invest-called-maxproj', $call->name) ?></p>
<?php elseif ($rest > 0) : ?>
    <input type="hidden" id="rest" name="rest" value="<?php echo $rest ?>" />
    <p><?php echo Text::html('call-splash-invest_explain_this', $call->user->name) ?><br /><?php echo Text::html('invest-called-maxdrop', \amount_format($maxdrop)) ?></p>
    <p><?php echo Text::html('invest-called-rest', \amount_format($rest), $call->name) ?></p>
<?php else: ?>
    <p><?php echo Text::html('invest-called-nodrop', $call->name) ?></p>
<?php endif; ?>
</div>
<?php endif; ?>


<div class="widget project-invest project-invest-individual_rewards">
    <h<?php echo $level ?> class="beak"><?php echo Text::get('invest-individual-header') ?></h<?php echo $level ?>>

    <div class="individual">
        <h<?php echo $level+1 ?> class="title"><?php echo Text::get('project-rewards-individual_reward-title'); ?></h<?php echo $level+1 ?>>
        <ul>
            <li><label class="resign"><input class="individual_reward" type="radio" id="resign_reward" name="selected_reward" value="0" amount="0"/><?php echo Text::get('invest-resign') ?></label></li>
            <!-- <span class="chkbox"></span> -->
        <?php foreach ($project->individual_rewards as $individual) : ?>
        <li class="<?php echo $individual->icon ?><?php if ($individual->none) echo ' disabled' ?>">

            <label class="amount" for="reward_<?php echo $individual->id; ?>">
                <input type="radio" name="selected_reward" id="reward_<?php echo $individual->id; ?>" value="<?php echo $individual->id; ?>" amount="<?php echo str_replace(array(',', '.'), '', \amount_format($individual->amount,0,true)); ?>" class="individual_reward" title="<?php echo htmlspecialchars($individual->reward) ?>" <?php if ($individual->none) echo 'disabled="disabled"' ?>/>
                <span class="amount"><?php echo \amount_format($individual->amount); ?></span>
            <!-- <span class="chkbox"></span> -->
            <h<?php echo $level + 2 ?> class="name"><?php echo htmlspecialchars($individual->reward) ?></h<?php echo $level + 2 ?>>
            <div id="reward_<?php echo $individual->id; ?>">
                <p><?php echo htmlspecialchars($individual->description)?></p>
                    <?php if ($individual->none) : // no quedan ?>
                    <span class="left"><?php echo Text::get('invest-reward-none') ?></span>
                    <?php elseif (!empty($individual->units)) : // unidades limitadas ?>
                    <strong><?php echo Text::get('project-rewards-individual_reward-limited'); ?></strong><br />
                    <?php $units = ($individual->units - $individual->taken); // resto
                    echo Text::html('project-rewards-individual_reward-units_left', $units); ?><br />
                <?php endif; ?>
            </div>
            </label>

        </li>
        <?php endforeach ?>
        </ul>
    </div>

</div>

<?php
// si es el primer paso, mostramos el botón para ir a login
if ($step == 'start') : ?>
<div class="widget project-invest method">
    <h<?php echo $level ?> class="beak"><?php echo Text::get('user-login-required-to_invest') ?></h<?php echo $level ?>>

    <div class="buttons">
        <button type="submit" class="button green" name="go-login" value=""><?php echo Text::get('imperative-register'); ?></button>
    </div>

    <div class="reminder"><?php echo Text::get('invest-alert-investing') ?> <span class="amount-reminder"><?php echo $select_currency; ?></span><span id="amount-reminder"><?php echo $amount; ?></span></div>
    <div class="reminder"><?php echo Text::html('faq-payment-method'); ?></div>

    <?php if ($_SESSION['currency'] != Currency::DEFAULT_CURRENCY ) : ?>
        <div class="reminder"><?php echo Text::html('currency-alert', \amount_format($amount, 0, true, true) ); ?></div>
    <?php endif; ?>

</div>
<?php else : ?>
<a name="continue"></a>
<div class="widget project-invest address">
    <h<?php echo $level ?> class="beak" id="address-header"><?php echo Text::get('invest-address-header') ?></h<?php echo $level ?>>
    <table>
        <tr>
            <td>
                <label for="fullname"><?php echo Text::get('invest-address-name-field') ?></label><br />
                <input type="text" id="fullname" name="fullname" value="<?php echo $personal->contract_name; ?>" />
            </td>
            <td><?php /* Para ocultar el campo nif:  id="donation-data" style="display:none;"  */ ?>
                <label for="nif"><?php echo Text::get('invest-address-nif-field') ?></label><br />
                <input type="text" id="nif" name="nif" value="<?php echo $personal->contract_nif; ?>" />
            </td>
        </tr>
        <tr>
            <td>
                <label for="address"><?php echo Text::get('invest-address-address-field') ?></label><br />
                <input type="text" id="address" name="address" value="<?php echo $personal->address; ?>" />
            </td>
            <td>
                <label for="zipcode"><?php echo Text::get('invest-address-zipcode-field') ?></label><br />
                <input type="text" id="zipcode" name="zipcode" value="<?php echo $personal->zipcode; ?>" />
            </td>
        </tr>
        <tr>
            <td>
                <label for="location"><?php echo Text::get('invest-address-location-field') ?></label><br />
                <input type="text" id="location" name="location" value="<?php echo $personal->location; ?>" />
            </td>
            <td>
                <label for="country"><?php echo Text::get('invest-address-country-field') ?></label><br />
                <input type="text" id="country" name="country" value="<?php echo $personal->country; ?>" />
            </td>
        </tr>
    </table>

    <p>
        <label><input type="checkbox" name="anonymous" value="1" /><span class="chkbox"></span><?php echo Text::get('invest-anonymous') ?></label>
    </p>

    <p>
        <label><input type="checkbox" name="pool" value="1" /><span class="chkbox"></span><?php echo Text::get('invest-pool') ?></label>
    </p>
</div>


<div class="widget project-invest method">
    <h<?php echo $level ?> class="title"><?php //echo Text::get('project-invest-continue') ?>Realiza tu aporte</h<?php echo $level ?>>
    <input type="hidden" id="paymethod"  />
    <input type="hidden" id="pool" value="<?php echo $this['pool']; ?>"  />

    <div class="reminder reminder-signed"><?php echo Text::get('invest-alert-investing') ?> <span class="amount-reminder"><?php echo $select_currency; ?></span><span id="amount-reminder"><?php echo $amount ?></span>
    <?php   if (!$allowpp) : 
                echo Text::html('invest-paypal_disabled'); 
            endif;
            if ($_SESSION['currency'] != Currency::DEFAULT_CURRENCY ) :
                echo '<div>'.Text::html('currency-alert', \amount_format($amount, 3, true, true) ).'</div>';
            endif;
    ?>
    </div>

    <div class="buttons">
        <div class="method"><input type="radio" name="method" id="tpv-method" checked="checked" value="tpv"><label for="tpv-method" class="label-method"><span class="method-text">Tarjeta<span><img class="img-method" src="/view/css/button/logos_tarjetas.png" /></label></div>
        <?php if ($allowpp) : ?>
            <div class="method"><input type="radio" name="method" id="paypal-method" value="paypal"><label for="paypal-method" class="label-method"><span class="method-text">Paypal<span><img class="img-method" src="/view/css/button/paypal-logo.png" /></label></div>
        <?php 
        endif;
        if ($this['pool'] > 0) : ?>
            <div class="method" style="margin-top:5px;"><input type="radio" name="method" id="pool-method" value="pool"><label for="pool-method" class="label-method"><span class="method-text">Monedero virtual<span class="pool-info" class="pool-info"><img class="img-method" height="28" src="/view/css/dashboard/monedero.svg" /><span style="margin-left:15px;"><?php echo \amount_format($this['pool']); ?> disponibles</span></label></div>
        <?php 
        endif;
        if (\GOTEO_ENV  != 'real') : // permitimos aportes en cash para testeo ?>
        <div class="method" style="margin-top:10px;"><input type="radio" name="method" id="cash-method" value="cash"><label for="cash-method" class="label-method"><span class="method-text">Cash<span></label></div>
        <?php endif; ?>

        <button type="submit" style="margin-top:30px;" class="process button green" id="button-general">Aportar</button>
    </div>
<br />

</div>
<?php endif; ?>

</form>

<?php //echo View::get('project/widget/worth.html.php', array('worthcracy' => $worthcracy, 'level' => $_SESSION['user']->worth)) ?>

<!--
<a name="commons"></a>
<div class="widget project-invest">
    <h<?php echo $level ?> class="beak"><?php echo Text::get('invest-social-header') ?></h<?php echo $level ?>>

    <div class="social">
        <h<?php echo $level + 1 ?> class="title"><?php echo Text::get('project-rewards-social_reward-title'); ?></h<?php echo $level + 1 ?>>
        <ul>
        <?php foreach ($project->social_rewards as $social) : ?>
            <li class="<?php echo $social->icon ?>">
                <h<?php echo $level + 2 ?> class="name"><?php echo htmlspecialchars($social->reward) ?></h<?php echo $level + 2 ?>
                <p><?php echo htmlspecialchars($social->description)?></p>
                <?php if (!empty($social->license) && array_key_exists($social->license, $licenses)): ?>
                <div class="license <?php echo htmlspecialchars($social->license) ?>">
                    <h<?php echo $level + 3 ?>><?php echo Text::get('regular-license'); ?></h<?php echo $level + 3 ?>>
                    <a href="<?php echo htmlspecialchars($licenses[$social->license]->url) ?>" target="_blank">
                        <strong><?php echo htmlspecialchars($licenses[$social->license]->name) ?></strong>

                    <?php if (!empty($licenses[$social->license]->description)): ?>
                    <p><?php echo htmlspecialchars($licenses[$social->license]->description) ?></p>
                    <?php endif ?>
                    </a>
                </div>
                <?php endif ?>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
</div>
-->

<script type="text/javascript">

    $(function () {

        var update = function () {

            var $reward = null;
            var val = parseFloat($('#amount').val());
            var pool = $('#pool').val();

            pool_greater(val,pool);

            $('div.widget.project-invest-individual_rewards input.individual_reward').each(function (i, cb) {
               var $cb = $(cb);
               var $selector='div#'+$cb.attr('id');
               $cb.closest('li').removeClass('chosed');
               $($selector).hide();
               // importe de esta recompensa
               var rval = parseFloat($cb.attr('amount'));
               if (rval > 0 && rval <= val) {
                   // si aun quedan
                   if ($cb.attr('disabled') != 'disabled') {
                       // nos quedamos con esta y seguimos
                       $reward = $cb;
                       alert("nueva recompensa");
                   }
               }

               if ($reward != null) {
                 $reward.click();
                 $reward.closest('li').addClass('chosed');
               } else {
                 $('#resign_reward').click();
                 $('#resign_reward').closest('li').addClass('chosed');
               }
            });
        };

        var reset_reward = function (chosen) {

            $('div.widget.project-invest-individual_rewards input.individual_reward').each(function (i, cb) {
               var $cb = $(cb);
               var $selector='div#'+$cb.attr('id');
               $cb.closest('li').removeClass('chosed');
               $($selector).hide();

               if ($cb.attr('id') == chosen) {
                 $cb.closest('li').addClass('chosed');
                 $($selector).show();
               }
            });
        };

        // funcion comparar valores
        var greater = function (a, b) {
            if (parseFloat(a) > parseFloat(b)) {
                return true;
            } else {
                return false;
            }
        };

        var pool_greater = function (amount, pool) {

            if(amount>pool)
            {
                $('#pool-method').attr('disabled',true);
                $('#pool-method').addClass('disabled');
            }
            else
            {
                $('#pool-method').attr('disabled',false);
                $('#pool-method').removeClass('disabled');
            }
        };

        // funcion resetear inpput de cantidad
        var reset_amount = function (preset) {
            $('#amount').val(preset);
            update();
        };

        // funcion resetear copy de cantidad
        var reset_reminder = function (rawamount) {
            var amount = parseFloat(rawamount);
            var pool = $('#pool').val();
            pool_greater(amount,pool);
            
            var rate = parseFloat('<?php echo Currency::rate();?>');
            if (isNaN(amount)) {
                amount = 0;
            }
            if (isNaN(rate)) {
                rate = 1;
            }
            var converted = parseFloat(amount / rate);
            converted = converted.toFixed(3);

            $('#amount').val(amount);
            $('#amount-reminder').html(amount);
            $('#reminder_conversion').html(converted);

            
           

        };

/* Actualizar el copy */
        $('#amount').bind('paste', function () {reset_reminder($('#amount').val());update()});

        $('#amount').change(function () {reset_reminder($('#amount').val());update()});


/* Si estan marcando o quitando el renuncio */
        $(':radio').bind('change', function () {
            var curr = $('#amount').val();
            var a = $(this).attr('amount');
            var i = $(this).attr('id');

            <?php if ($step == 'start') : ?>
                reset_reward(i);
            <?php else : ?>
            // si es renuncio
            if ($('#resign_reward').attr('checked') == 'checked') {
                $("#address-header").html('<?php echo Text::slash('invest-donation-header') ?>');
                /*$("#donation-data").show();*/
                reset_reward(i);
            } else {
                $("#address-header").html('<?php echo Text::slash('invest-address-header') ?>');
                /*$("#donation-data").hide();*/
                reset_reward(i);
            }
            <?php endif; ?>

            if (greater(a, curr)) {
                reset_reminder(a);
            }
        });

/* Verificacion, no tenemos en cuenta el paso porque solo son los botones de pago en el paso confirm */
        $('button.process').click(function () {

            var amount = $('#amount').val();
            var rest = $('#rest').val();
            var rate = parseFloat('<?php echo Currency::rate();?>');
            if (isNaN(rate)) {
                rate = 1;
            }
            var converted = parseFloat(amount / rate);
            converted = converted.toFixed(3);


            if (parseFloat(amount) == 0 || isNaN(amount)) {
                alert('<?php echo Text::slash('invest-amount-error') ?>');
                $('#amount').focus();
                return false;
            }

            /* Renuncias pero no has puesto tu NIF para desgravar el donativo */
            if ($('#resign_reward').attr('checked') == 'checked') {
                if ($('#nif').val() == '' && !confirm('<?php echo Text::slash('invest-alert-renounce') ?>')) {
                    $('#nif').focus();
                    return false;
                }
            } else {
                var reward = '';
                var chosen = 0;
                /* No has marcado ninguna recompensa, renuncias? */
                $('input.individual_reward').each(function (i, cb) {
                   var prize = $(this).attr('amount');
                   if (greater(prize, 0) && $(this).attr('checked') == 'checked') {
                       reward = $(this).attr('title');
                       chosen = prize;
                   }
                });

               if (greater(chosen, amount)) {
                   alert('<?php echo Text::slash('invest-alert-lackamount') ?>');
                   return false;
               }

               if (reward == '') { 
                    if (confirm('<?php echo Text::slash('invest-alert-noreward') ?>')) {
                        if (confirm('<?php echo Text::slash('invest-alert-noreward_renounce') ?>')) {
                            $("#address-header").html('<?php echo Text::slash('invest-donation-header') ?>');
                            /*$("#donation-data").show();*/
                            $('#resign_reward').click();
                            $('#nif').focus();
                            return false;
                        }
                    } else {
                        $('#nif').focus();
                        return false;
                    }
                } 
            }

            if (rest > 0 && greater(amount, rest)) {
                if (!confirm('<?php echo Text::slash('invest-alert-lackdrop') ?> '+rest+' <?php $select_currency; ?>, ok?')) {
                    return false;
                }
            }

            var currency = '<?php echo $_SESSION['currency']; ?>';
            var def_currency = '<?php echo Currency::DEFAULT_CURRENCY; ?>';
            var confirm_msg = '<?php echo Text::slash('invest-alert-investing') ?> '+amount+' '+currency;

            if ( currency != def_currency) {
                confirm_msg += ' = '+converted+' '+def_currency;
            }

            if ($('#resign_reward').attr('checked') != 'checked')
                confirm_msg += ' \n'+'<?php echo Text::slash('invest-alert-rewards') ?> '+reward+' ok?';

            return confirm(confirm_msg);
        });

/* Seteo inicial por url */
        reset_amount('<?php echo $amount ?>');

    });

</script>
