<?php

use Goteo\Core\View;


$this->layout('contract/edit');


$contract = $this->contract;
$step = $this->step;

$errors = $contract->errors[$step] ?: array();
$okeys  = $contract->okeys[$step] ?: array();
// print_r($errors);die;

$this->section('contract-edit-step');

echo \Goteo\Library\SuperForm::get(array(

    'level'         => 3,
    'method'        => 'post',
    'title'         => $this->text('contract-step-promoter'),
    'hint'          => $this->text('guide-contract-promoter'),
    'elements'      => array(
        'process_promoter' => array (
            'type' => 'hidden',
            'value' => 'promoter'
        ),

        /* Radio Tipo de persona */
        'contract_entity-radioset' => array(
            'type'      => 'group',
            'title'     => $this->text('personal-field-contract_entity'),
            'hint'     => $this->text('tooltip-project-contract_entity'),
            'children'  => array(
                'type-person' =>  array(
                    'name'  => 'type',
                    'value' => 0,
                    'type'  => 'radio',
                    'class' => 'inline',
                    'label' => $this->text('contract-entity-option_0'),
                    'checked' => $contract->type == 0,
                ),
                'type-association' =>  array(
                    'name'  => 'type',
                    'value' => 1,
                    'type'  => 'radio',
                    'class' => 'inline',
                    'label' => $this->text('contract-entity-option_1'),
                    'checked' => $contract->type == 1,
                ),
                'type-bussines' =>  array(
                    'name'  => 'type',
                    'value' => 2,
                    'type'  => 'radio',
                    'class' => 'inline',
                    'label' => $this->text('contract-entity-option_2'),
                    'checked' => $contract->type == 2,
                )
            )
        ),

        'name' => array(
            'type'      => 'textbox',
            'class'     => 'inline',
            'required'  => true,
            'title'     => $this->text('personal-field-contract_name'),
            'hint'     => $this->text('tooltip-project-contract_name'),
            'errors'    => !empty($errors['name']) ? array($errors['name']) : array(),
            'ok'        => !empty($okeys['name']) ? array($okeys['name']) : array(),
            'value'     => $contract->name
        ),

        'nif' => array(
            'type'      => 'textbox',
            'class'     => 'inline',
            'required'  => true,
            'title'     => $this->text('personal-field-contract_nif'),
            'hint'     => $this->text('tooltip-project-contract_nif'),
            'errors'    => !empty($errors['nif']) ? array($errors['nif']) : array(),
            'ok'        => !empty($okeys['nif']) ? array($okeys['nif']) : array(),
            'value'     => $contract->nif
        ),

        'birthdate'  => array(
            'type'      => 'datebox',
            'required'  => true,
            'size'      => 8,
            'title'     => $this->text('personal-field-contract_birthdate'),
            'hint'      => $this->text('tooltip-project-contract_birthdate'),
            'errors'    => !empty($errors['birthdate']) ? array($errors['birthdate']) : array(),
            'ok'        => !empty($okeys['birthdate']) ? array($okeys['birthdate']) : array(),
            'value'     => $contract->birthdate
        ),

        /* Domicilio fiscal */
        'fiscaladdr' => array(
            'type'      => 'group',
            'title'     => $this->text('personal-field-main_address'),
            'children'  => array(

                'address' => array(
                    'type'      => 'textbox',
                    'class'     => 'inline',
                    'required'  => true,
                    'title'     => $this->text('personal-field-address'),
                    'errors'    => !empty($errors['address']) ? array($errors['address']) : array(),
                    'ok'        => !empty($okeys['address']) ? array($okeys['address']) : array(),
                    'value'     => $contract->address
                ),

                'location' => array(
                    'type'      => 'textbox',
                    'class'     => 'inline',
                    'required'  => true,
                    'title'     => $this->text('contract-field-location'),
                    'errors'    => !empty($errors['location']) ? array($errors['location']) : array(),
                    'ok'        => !empty($okeys['location']) ? array($okeys['location']) : array(),
                    'value'     => $contract->location
                ),

                'region' => array(
                    'type'      => 'textbox',
                    'class'     => 'inline',
                    'required'  => true,
                    'title'     => $this->text('personal-field-region'),
                    'errors'    => !empty($errors['region']) ? array($errors['region']) : array(),
                    'ok'        => !empty($okeys['region']) ? array($okeys['region']) : array(),
                    'value'     => $contract->region
                ),

                'zipcode' => array(
                    'type'      => 'textbox',
                    'class'     => 'inline',
                    'required'  => true,
                    'title'     => $this->text('personal-field-zipcode'),
                    'size'      => 7,
                    'errors'    => !empty($errors['zipcode']) ? array($errors['zipcode']) : array(),
                    'ok'        => !empty($okeys['zipcode']) ? array($okeys['zipcode']) : array(),
                    'value'     => $contract->zipcode
                ),

                'country' => array(
                    'type'      => 'textbox',
                    'class'     => 'inline',
                    'required'  => true,
                    'title'     => $this->text('personal-field-country'),
                    'errors'    => !empty($errors['country']) ? array($errors['country']) : array(),
                    'ok'        => !empty($okeys['country']) ? array($okeys['country']) : array(),
                    'value'     => $contract->country
                )
            )
        ),

        'footer' => array(
            'type'      => 'group',
            'children'  => array(
                'errors' => array(
                    'title' => $this->text('form-footer-errors_title'),
                    'content'  => $this->insert('contract/partials/errors', [ 'contract' => $contract, 'step' => $step ])
                ),
                'buttons'  => array(
                    'type'  => 'group',
                    'children' => array(
                        'next' => array(
                            'type'  => 'Button',
                            'buttontype'  => 'submit',
                            'name'  => 'step',
                            'value'  => 'entity',
                            'label' => $this->text('form-next-button'),
                            'class' => 'next'
                        )
                    )
                )
            )

        )

    )

));

$this->replace();
