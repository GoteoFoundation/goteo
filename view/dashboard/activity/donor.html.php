<?php
use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\NormalForm;

$donation = $this['donation'];

if ($donation->country == 'spain') {
    $sel_spain = ' selected="selected"';
    $sel_other = '';
} else {
    $sel_spain = '';
    $sel_other = ' selected="selected"';
}

// en el save y en el get se gestiona:
//   si es españa, los dos primeros numeros del CP
//   si es extrajero, 99

switch ($this['action']) :
    case 'edit':
?>
<div class="widget">
    <h3><?php echo Text::get('dashboard-activity-donor-header'); ?></h3>
    <p><?php echo Text::get('dashboard-donor-edit_guide') ?></p>
</div>
    
<form method="post" action="/dashboard/activity/donor/save" class="project" >
    <input type="hidden" name="save" value="donation" />
    <input type="hidden" name="location" value="<?php echo $donation->location ?>" />

<?php echo new NormalForm(array(

    'level'         => 3,
    'method'        => 'post',
    'hint'          => '',
    'footer'        => array(
        'view-step-overview' => array(
            'type'  => 'submit',
            'label' => Text::get('form-apply-button'),
            'class' => 'next',
            'name'  => 'apply'
        )
    ),
    'elements'      => array(

        'name' => array(
            'title'     => Text::get('personal-field-donor_name').' *',
            'type'      => 'textbox',
            'class'     => '',
            'value'     => $donation->name
        ),
        'surname' => array(
            'title'     => Text::get('personal-field-donor_surname').' *',
            'type'      => 'textbox',
            'class'     => '',
            'value'     => $donation->surname
        ),
        'nif' => array(
            'title'     => Text::get('invest-address-nif-field').' *',
            'type'      => 'textbox',
            'class'     => '',
            'value'     => $donation->nif
        ),
        'address' => array(
            'title'     => Text::get('invest-address-address-field').' *',
            'type'      => 'textbox',
            'class'     => '',
            'value'     => $donation->address
        ),
        'zipcode' => array(
            'title'     => Text::get('invest-address-zipcode-field'.' *'),
            'type'      => 'textbox',
            'class'     => '',
            'value'     => $donation->zipcode
        ),
        'country' => array(
            'title'     => Text::get('donor-address-country-field').' *',
            'type'      => 'html',
            'html'     => '<select name="country" id="donor_country">
            <option value="spain"'.$sel_spain.'>SPAIN</option>
            <option value="other"'.$sel_other.'>OTHER</option>
            </select>'
        )

    )

));

?>
</form>
<?php
        break;

    case 'view':
?>
<div class="widget">
    <h3><?php echo Text::get('dashboard-activity-donor-header'); ?></h3>
    <p><?php echo Text::get('dashboard-donor-edit_guide') ?></p>
    <dl>
        <dt><?php echo Text::get('donor-field-amount') ?></dt>
        <dd><?php echo \amount_format($donation->amount) ?> &euro;</dd>
    </dl>
    <dl>
        <dt><?php echo Text::get('donor-field-numproj') ?></dt>
        <dd><?php echo $donation->numproj ?></dd>
    </dl>
    <p><?php
        foreach ($donation->dates as $invest) {
            echo "En fecha <strong>{$invest->date}</strong> un aporte de <strong>{$invest->amount} euros</strong> al proyecto <strong>{$invest->project}</strong><br />";
        } ?></p>
    <dl>
        <dt><?php echo Text::get('invest-address-name-field') ?></dt>
        <dd><?php echo $donation->name ?></dd>
    </dl>
    <dl>
        <dt><?php echo Text::get('invest-address-nif-field') ?></dt>
        <dd><?php echo $donation->nif ?></dd>
    </dl>
    <dl>
        <dt><?php echo Text::get('invest-address-address-field') ?></dt>
        <dd><?php echo $donation->address ?></dd>
    </dl>
    <dl>
        <dt><?php echo Text::get('invest-address-zipcode-field') ?></dt>
        <dd><?php echo $donation->zipcode ?></dd>
    </dl>
    <dl>
        <dt><?php echo Text::get('invest-address-location-field') ?></dt>
        <dd><?php echo $donation->location ?></dd>
    </dl>
    <dl>
        <dt><?php echo Text::get('invest-address-country-field') ?></dt>
        <dd><?php echo $donation->country ?></dd>
    </dl>

    <p>
      <?php if (!$donation->confirmed) : ?><a class="button" href="/dashboard/activity/donor/edit"><?php echo Text::get('dashboard-donor-edit_data'); ?></a><?php endif; ?>
      <?php if ($donation->edited && !$donation->confirmed) : ?><a class="button" href="/dashboard/activity/donor/confirm" <?php if (!$donation->confirmed) : ?>onclick="return confirm('<?php echo Text::get('dashboard-donor-confirm_data'); ?>')"<?php endif; ?> ><?php echo Text::get('dashboard-donor-confirm_button'); ?></a><?php endif; ?>
      <?php if ($donation->confirmed) : ?><a class="button" href="/dashboard/activity/donor/download" target="_blank"><?php echo Text::get('dashboard-donor-download_certificate'); ?></a><?php endif; ?>
    </p>
</div>
<?php       
        
    break;
    
endswitch;
