<?php
use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\NormalForm;

$donation = $this['donation'];

switch ($this['action']) :
    case 'edit':
?>
<div class="widget">
    <h3><?php echo Text::get('dashboard-activity-donor-header'); ?></h3>
    <p><?php echo Text::get('dashboard-donor-edit_guide') ?></p>
</div>
    
<form method="post" action="/dashboard/activity/donor/save" class="project" >
    <input type="hidden" name="save" value="donation" />
    
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
            'title'     => Text::get('invest-address-name-field'),
            'type'      => 'textbox',
            'class'     => '',
            'value'     => $donation->name
        ),
        'nif' => array(
            'title'     => Text::get('invest-address-nif-field'),
            'type'      => 'textbox',
            'class'     => '',
            'value'     => $donation->nif
        ),
        'address' => array(
            'title'     => Text::get('invest-address-address-field'),
            'type'      => 'textbox',
            'class'     => '',
            'value'     => $donation->address
        ),
        'zipcode' => array(
            'title'     => Text::get('invest-address-zipcode-field'),
            'type'      => 'textbox',
            'class'     => '',
            'value'     => $donation->zipcode
        ),
        'location' => array(
            'title'     => Text::get('invest-address-location-field'),
            'type'      => 'textbox',
            'class'     => '',
            'value'     => $donation->location
        ),
        'country' => array(
            'title'     => Text::get('invest-address-country-field'),
            'type'      => 'textbox',
            'class'     => '',
            'value'     => $donation->country
        )

    )

));

?>
</form>
<?php break;

    case 'view':
?>
<div class="widget">
    <h3><?php echo Text::get('dashboard-menu-activity-donor'); ?></h3>
    <dl>
        <dt><?php echo Text::get('donor-field-amount') ?></dt>
        <dd><?php echo \amount_format($donation->amount) ?> &euro;</dd>
    </dl>
    <dl>
        <dt><?php echo Text::get('donor-field-numproj') ?></dt>
        <dd><?php echo $donation->numproj ?></dd>
    </dl>
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
      <?php if ($donation->edited) : ?><a class="button" href="/dashboard/activity/donor/download" onclick="return confirm('<?php echo Text::get('dashboard-donor-confirm_data'); ?>')" target="_blank"><?php echo Text::get('dashboard-donor-download_certificate'); ?></a><?php endif; ?>
    </p>
</div>
<?php       
        
    break;
    
endswitch;
