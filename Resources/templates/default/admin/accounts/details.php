<?php

$invest = $this->invest;
$project = $this->project;
$calls = $this->calls;
$droped = $this->droped;
$user = $this->user;
$methods = $this->methods;
$rewards = $invest->rewards;
$location = $this->location;
array_walk($rewards, function (&$reward) { $reward = $reward->reward; });

?>

<?php $this->layout('admin/layout') ?>

<?php $this->section('admin-content') ?>

<a href="/admin/accounts/" class="button"><?= $this->text('admin-back') ?></a>

<div class="widget">
    <h3><?= $this->text('admin-account-detail') ?></h3>
    <table class="table">
    <tr>
        <td><strong><?= $this->text('admin-project') ?></strong></td>
        <?php if($project): ?>
            <td>
                <?php echo $project->name ?> (<?php echo $this->projectStatus[$project->status] ?>)
            </td>
            <td>[<a href="/admin/accounts/converttopool/<?= $invest->id ?>" onclick="return confirm('<?= $this->ee($this->text('admin-account-convert-to-pool-confirm'), 'js') ?>')"><?= $this->text('admin-account-convert-to-pool') ?></a>]</td>
        <?php else: ?>
            <td>
                <span class="label label-info"><?= $this->text('invest-pool-method') ?></span>
            </td>
            <td>&nbsp;</td>
        <?php endif ?>
    </tr>
    <tr>
        <td><strong><?= $this->text('admin-user') ?></strong></td>
        <td>
            <?php if($this->is_module_admin('users', $invest->node)): ?>
                <a href="/admin/users/manage/<?= $user->id ?>"><?= $user->id ?> [<?= $user->name ?> / <?= $user->email ?>]</a>
            <?php else: ?>
                <?= $user->id ?> [<?= $user->name ?> / <?= $user->email ?>]
            <?php endif ?>
        </td>
        <td>
            <?php if($this->is_module_admin('users', $invest->node)): ?>
                <?= $this->insert('admin/partials/typeahead_form', [
                                                                    'id' => 'change_user_input',
                                                                    'hidden' => true
                                                                    ]) ?>
                [<a href="#change_user" id="change_user"><?= $this->text('admin-account-change-user') ?></a>]<br>
            <?php else: ?>
                &nbsp;
            <?php endif ?>
        </td>
    </tr>
    <tr>
        <td><?= $this->text('admin-account-amount') ?>:</td>
        <td><?php echo $invest->amount ?> &euro;
            <?php
                if (!empty($invest->campaign))
                    echo 'Campaña: ' . $campaign->name;
            ?>
        </td>
        <td>&nbsp;</td>
    </tr>

    <tr>
        <td><?= $this->text('admin-status') ?>:</td>
        <td><?= $this->percent_span(100 * ($invest->status + 1)/2, 0, $this->investStatus[$invest->status]) ?>
            <?php if ($invest->issue) echo ' <span style="font-weight:bold; color:red;">INCIDENCIA!<span>'; ?>

        </td>
        <td>

            <?php if ($invest->issue) : ?>
            <a href="/admin/accounts/solve/<?php echo $invest->id ?>" onclick="return confirm('Esta incidencia se dará por resuelta: se va a cancelar el preaproval, el aporte pasará a ser de tipo Cash y en estado Cobrado por goteo, seguimos?')" class="button"><?= $this->text('admin-account-issue-solved') ?></a><br>
            <?php endif; ?>

            <?php if ($this->poolable) : ?>
            <a href="/admin/accounts/refundpool/<?php echo $invest->id ?>" onclick="return confirm('<?= $this->ee($this->text('admin-account-refund-to-pool-confirm'), 'js') ?>')" class="button"><?= $this->text('admin-account-refund-to-pool') ?></a>
            <?php endif; ?>

            <?php if ( $this->refundable) : ?>
            <a href="/admin/accounts/refunduser/<?php echo $invest->id ?>" onclick="return confirm('<?= $this->ee($this->text('admin-account-refund-to-user-confirm'), 'js') ?>')" class="button"><?= $this->text('admin-account-refund-to-user') ?></a><br>
            <?php endif; ?>

            [<a href="/admin/accounts/update/<?php echo $invest->id ?>" onclick="return confirm('<?= $this->ee($this->text('admin-account-modify-status-confirm'), 'js') ?>')"><?= $this->text('admin-account-modify-status') ?></a>]
        </td>
    </tr>

    <tr>
        <td><?= $this->text('admin-account-invest-date') ?>:</td>
        <td><?php echo $invest->invested . '  '; ?>
            <?php
                if (!empty($invest->charged))
                    echo "<br>\nCargo ejecutado el: " . $invest->charged;

                if (!empty($invest->returned))
                    echo "<br>\nDinero devuelto el: " . $invest->returned;
            ?>
        </td>
        <td>&nbsp;</td>
    </tr>

    <?php if($project): ?>
    <tr>
        <td><?= $this->text('admin-account-donation') ?>:</td>
        <td>
            <?php
            if($invest->resign) {
                echo "SI<br />Donativo de: {$invest->address->name} [{$invest->address->nif}]";
            }
            else {
                echo "NO";
            }
            ?>
        </td>
        <td>[<a href="/admin/accounts/switchresign/<?php echo $invest->id ?>" onclick="return confirm('<?= $this->ee($this->text('admin-account-switch-donation-confirm'), 'js') ?>')"><?= $this->text('admin-account-switch-donation') ?></a>]</td>
    </tr>
    <?php endif ?>

    <tr>
        <td><?= $this->text('admin-account-pool-on-fail') ?>:</td>
        <td>
            <?= ($invest->pool) ?  $this->text('admin-YES') : $this->text('admin-NO')  ?>
        </td>
        <td><?php if(
                    (!$invest->pool || !$invest->isOnPool())
                    &&
                    ($project && $project->status == \Goteo\Model\Project::STATUS_IN_CAMPAIGN)
                    ): ?>
                [<a href="/admin/accounts/switchpool/<?php echo $invest->id ?>" onclick="return confirm('<?= $this->ee($this->text('admin-account-switch-pool-confirm'), 'js') ?>')"><?= $this->text('admin-account-switch-pool') ?></a>]
            <?php endif ?>
        </td>
    </tr>

    <tr>
        <td><?= $this->text('admin-account-payment-method') ?>:</td>
        <td><?php echo $methods[$invest->method] . '   '; ?>
            <?php
                if (!empty($invest->campaign))
                    echo '<br />Capital riego';

                if (!empty($invest->anonymous))
                    echo '<br />Aporte anónimo';

                if (!empty($invest->admin))
                    echo '<br />Manual generado por admin: '.$invest->admin;
            ?>
        </td>
        <td>&nbsp;</td>
    </tr>

    <tr>
        <td><?= $this->text('admin-account-transaction') ?>:</td>
        <td><?php
                if (!empty($invest->preapproval)) {
                    echo 'Preapproval: '.$invest->preapproval . '   ';
                }

                if (!empty($invest->payment)) {
                    echo "<br>\nPayment: ".$invest->payment . '   ';
                }

                if (!empty($invest->transaction)) {
                    echo "<br>\nTransaction: ".$invest->transaction . '   ';
                }
            ?>
        </td>
        <td>&nbsp;</td>
    </tr>

    <?php if (!$invest->resign && $project) : ?>
    <tr>
        <td><?= $this->text('admin-account-rewards') ?>:</td>
        <td>
            <?php echo implode(', ', $rewards); ?>
        </td>
        <td><a href="/admin/rewards/edit/<?php echo $invest->id ?>" class="button"><?= $this->text('admin-account-edit-reward') ?></a></td>
    </tr>
    <?php endif; ?>

    <tr>
        <td><?= $this->text('admin-address') ?>:</td>
        <td>
            <?php
            $address = $invest->address->address;
            if($invest->address->location) $address .= ', ' . $invest->address->location;
            if($invest->address->zipcode) $address .= ', ' . $invest->address->zipcode;
            if($invest->address->country) $address .= ', ' . $invest->address->country;
            echo $address;
            ?>
        </td>
        <td>&nbsp;</td>
    </tr>

    </table>

<?php

    if($location) {
        echo $this->insert('partials/utils/map_canvas', ['latitude' => $location->latitude,
                                                         'longitude' => $location->longitude,
                                                         'content' => $invest->getUser()->name."<br>$address"]);
    } elseif($address) {
        echo $this->insert('partials/utils/map_canvas', [
            // By passing the address, a geocoding will be attempted
            'address' => $address,
            // this will save automatically into the InvestLocation table
            'geoType' => 'invest', 'geoItem' => $invest->id,
            'content' => $invest->getUser()->name."<br>$address"]);
    }
?>

    <?php if ($invest->method == 'paypal') : ?>
        <?php if ($this->get_query('full') != 'show') : ?>
        <p>
            <a href="/admin/accounts/details/<?php echo $invest->id; ?>?full=show">Mostrar detalles técnicos</a>
        </p>
        <?php endif; ?>

        <?php if (!empty($invest->transaction)) : ?>
        <dl>
            <dt><strong>Detalles de la devolución:</strong></dt>
            <dd>Hay que ir al panel de paypal para ver los detalles de una devolución</dd>
        </dl>
        <?php endif ?>
    <?php elseif ($invest->method == 'tpv') : ?>
        <p>Hay que ir al panel del banco para ver los detalles de los aportes mediante TPV.</p>
    <?php endif ?>

    <?php if (!empty($droped)) : ?>
    <h3>Capital riego asociado</h3>
    <dl>
        <dt>Convocatoria:</dt>
        <dd><?php echo $calls[$droped->call] ?></dd>
    </dl>
    <a href="/admin/invests/details/<?php echo $droped->id ?>" target="_blank">Ver aporte completo de riego</a>
    <?php endif; ?>

</div>

<div class="widget">
    <h3>Log</h3>
    <?php foreach ($invest->getDetails() as $log)  {
        echo "{$log->date} : {$log->log} ({$log->type})<br />";
    } ?>
</div>

<?php

if ($this->get_query('full') == 'show' && $invest->method == 'paypal') :
    $paypal = new \Goteo\Library\Paypal($invest);
    $errors = array();

?>
<div class="widget">
    <h3>Detalles técnicos de la transaccion paypal</h3>
    <?php
    // detalles de la ejecución del preapproval
    $details = $paypal->paymentDetails();
    ?>
    <dl>
        <dt><strong>Detalles del pago:</strong></dt>
        <dd><?php echo \trace($details); ?></dd>
    </dl>

    <?php
    if ($paypal->getErrors()) {
        echo '<div>'.implode('<br />', $paypal->getErrors()).'</div>';
    }
    ?>
</div>
<?php endif; ?>

<?php $this->replace() ?>


<?php $this->section('footer') ?>
<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
$(function(){
    $.typeahead({
        input: "#change_user_input",
        // order: "asc",
        dynamic:true,
        hint:true,
        searchOnFocus:true,
        accent:true,
        emptyTemplate: 'No result for "{{query}}"',
        display: ["id", "name", "email"],
        template: '<span>' +
            '<span class="avatar">' +
                '<img src="/img/tinyc/{{avatar}}">' +
            '</span> ' +
            '<span class="name">{{name}}</span> ' +
            '<span class="info">({{id}}, {{email}})</span>' +
        '</span>',
        source: {
            list: {
                url: [{
                        url: "/api/users",
                        data: {
                            q: "{{query}}",
                        }
                    }, 'list']
            }
        },
        callback: {
            onClickAfter: function (node, a, item, event) {
                event.preventDefault();
                var r = confirm("<?= $this->ee($this->text('admin-account-change-user-confirm'), 'js') ?> " + item.name + "\n\n<?= $this->ee($this->text('regular-continue-question'), 'js') ?>");
                if (r == true) {
                    location.href = '/admin/accounts/changeuser/<?= $invest->id ?>?user=' + item.id;
                }

                // $('#change_user_input').closest('form').hide();
                // $('#change_user').show();
            }
        },
        debug: true
    });
    $('#main').on('click', '#change_user', function(e){
        console.log('click', e);
        e.preventDefault();
        $('#change_user_input').closest('form').show();
        $('#change_user_input').val('');
        $('#change_user_input').select();
        $(this).hide();
        $('#main').one('blur', '#change_user_input', function(e){
            console.log('blur', e);
            setTimeout(function(){
                $('#change_user_input').closest('form').hide();
                $('#change_user').show();
            }, 100);
        });
    });
});

// @license-end
</script>
<?php $this->append() ?>
