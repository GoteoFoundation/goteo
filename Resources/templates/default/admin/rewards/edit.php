<?php $this->layout('admin/layout') ?>

<?php $this->section('admin-content') ?>
<?php

$invest = $this->invest;
$project = $this->project;
$user = $this->user;

$rewards = array();
foreach ($invest->rewards as $key => $data) {
    $rewards[$data->id] = $data->id;
}
?>
<div class="widget">
    <p>
        <strong>Proyecto:</strong> <?= $project->name ?> (<?= $this->status[$project->status] ?>)<br />
        <strong>Usuario: </strong><?= $user->name ?><br />
        <strong>Cantidad aportada: </strong><?= $invest->amount ?> &euro; <br />
    </p>


<form method="post" action="/admin/rewards/edit/<?= $invest->id ?>" >
    <h3>Recompensa</h3>
    <ul style="list-style: none;">

        <li>
            <label>
                <input class="individual_reward" type="checkbox" id="anonymous" name="anonymous" value="1" <?php if ($invest->anonymous) echo ' checked="checked"' ?>/>
                An&oacute;nimo
            </label>
        </li>
        <li><hr /></li>
        <li>
            <label>
                <input class="individual_reward" type="radio" id="no_reward" name="selected_reward" value="0" amount="0" <?php if (empty($rewards)) echo ' checked="checked"' ?>/>
                Ninguna recompensa.
            </label>
        </li>
        <!-- <span class="chkbox"></span> -->
    <?php foreach ($project->individual_rewards as $individual) : ?>
    <li class="<?= $individual->icon ?><?= ($individual->available() ? '' : ' disabled') ?>">

        <label>
            <input type="radio"<?= ($individual->available() ? '' : ' disabled="disabled"')?> name="selected_reward" id="reward_<?= $individual->id ?>" value="<?= $individual->id ?>" amount="<?= $individual->amount ?>" class="individual_reward" title="<?= $individual->reward ?>" <?php if (isset($rewards[$individual->id])) echo ' checked="checked"' ?>/>
            <?= $individual->reward . ' <strong>' .$individual->amount . ' &euro; </strong>' ?>
        </label>

    </li>
    <?php endforeach ?>
    </ul>


<?php
echo new \Goteo\Library\NormalForm(array(

    'level'         => 3,
    'method'        => 'post',
    'footer'        => array(
        'view-step-overview' => array(
            'type'  => 'submit',
            'label' => $this->text('form-apply-button'),
            'class' => 'next',
            'name'  => 'update'
        )
    ),
    'elements'      => array(

        'name' => array(
            'type'      => 'textbox',
            'size'      => 40,
            'title'     => $this->text('personal-field-contract_name'),
            'value'     => $invest->address->name
        ),

        'nif' => array(
            'type'      => 'textbox',
            'title'     => $this->text('personal-field-contract_nif'),
            'size'      => 15,
            'value'     => $invest->address->nif
        ),

        'address' => array(
            'type'  => 'textbox',
            'title' => $this->text('personal-field-address'),
            'size'  => 55,
            'value' => $invest->address->address
        ),

        'location' => array(
            'type'  => 'textbox',
            'title' => $this->text('personal-field-location'),
            'size'  => 55,
            'value' => $invest->address->location
        ),

        'zipcode' => array(
            'type'  => 'textbox',
            'title' => $this->text('personal-field-zipcode'),
            'size'  => 7,
            'value' => $invest->address->zipcode
        ),

        'country' => array(
            'type'  => 'textbox',
            'title' => $this->text('personal-field-country'),
            'size'  => 55,
            'value' => $invest->address->country
        ),


        'regalo' => array(
            'type'  => 'checkbox',
            'title' => $this->text('invest-address-friend-field'),
            'value' => '1',
            'checked' => $invest->address->regalo
        ),


        'namedest' => array(
            'type'  => 'textbox',
            'title' => $this->text('invest-address-namedest-field'),
            'value' => $invest->address->namedest
        ),


        'emaildest' => array(
            'type'  => 'textbox',
            'title' => $this->text('invest-address-maildest-field'),
            'value' => $invest->address->emaildest
        ),

    )

));

?>
</form>
</div>
<?php $this->replace() ?>
