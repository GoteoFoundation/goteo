<?php

use Goteo\Library\Text,
    Goteo\Library\SuperForm;

$errors = $this['errors'];
$personal = $this['personal'];
$this['level'] = 3;

?>
<form method="post" action="/dashboard/profile/personal" class="project" enctype="multipart/form-data">

<?php
echo new SuperForm(array(

    'level'         => $this['level'],
    'method'        => 'post',
    'title'         => Text::get('personal-main-header'),
    'hint'          => Text::get('guide-project-contract-information'),
    'footer'        => array(
        'view-step-overview' => array(
            'type'  => 'submit',
            'label' => Text::get('form-apply-button'),
            'class' => 'next',
            'name'  => 'save-userPersonal'
        )
    ),
    'elements'      => array(

        'contract_name' => array(
            'type'      => 'textbox',
            'required'  => true,
            'size'      => 20,
            'title'     => Text::get('personal-field-contract_name'),
            'hint'      => Text::get('tooltip-project-contract_name'),
            'errors'    => !empty($errors['contract_name']) ? array($errors['contract_name']) : array(),
            'value'     => $personal->contract_name
        ),

        'contract_nif' => array(
            'type'      => 'textbox',
            'required'  => true,
            'title'     => Text::get('personal-field-contract_nif'),
            'size'      => 9,
            'hint'      => Text::get('tooltip-project-contract_nif'),
            'errors'    => !empty($errors['contract_nif']) ? array($errors['contract_nif']) : array(),
            'value'     => $personal->contract_nif
        ),

        'phone' => array(
            'type'  => 'textbox',
            'required'  => true,
            'title' => Text::get('personal-field-phone'),
            'dize'  => 15,
            'hint'  => Text::get('tooltip-project-phone'),
            'errors'    => !empty($errors['phone']) ? array($errors['phone']) : array(),
            'value' => $personal->phone
        ),

        'address' => array(
            'type'  => 'textarea',
            'required'  => true,
            'title' => Text::get('personal-field-address'),
            'rows'  => 6,
            'cols'  => 40,
            'hint'  => Text::get('tooltip-project-address'),
            'errors'    => !empty($errors['address']) ? array($errors['address']) : array(),
            'value' => $personal->address
        ),

        'zipcode' => array(
            'type'  => 'textbox',
            'required'  => true,
            'title' => Text::get('personal-field-zipcode'),
            'size'  => 7,
            'hint'  => Text::get('tooltip-project-zipcode'),
            'errors'    => !empty($errors['zipcode']) ? array($errors['zipcode']) : array(),
            'value' => $personal->zipcode
        ),

        'location' => array(
            'type'  => 'textbox',
            'required'  => true,
            'title' => Text::get('personal-field-location'),
            'size'  => 25,
            'hint'  => Text::get('tooltip-project-location'),
            'errors'    => !empty($errors['location']) ? array($errors['location']) : array(),
            'value' => $personal->location
        ),

        'country' => array(
            'type'  => 'textbox',
            'required'  => true,
            'title' => Text::get('personal-field-country'),
            'size'  => 25,
            'hint'  => Text::get('tooltip-project-country'),
            'errors'    => !empty($errors['country']) ? array($errors['country']) : array(),
            'value' => $personal->country
        ),

    )

));

?>
</form>
