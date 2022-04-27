<?php

namespace Goteo\Library\Forms\Model;

use Goteo\Application\Lang;
use Goteo\Library\Forms\AbstractFormProcessor;
use Goteo\Library\Forms\FormProcessorInterface;
use Goteo\Library\Text;
use Goteo\Util\Form\Type\ChoiceType;
use Goteo\Util\Form\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormInterface;

class InvestAddressForm extends AbstractFormProcessor implements FormProcessorInterface {

    public function createForm(): InvestAddressForm
    {
        $model = $this->getModel();

        $this->getBuilder()
            ->add('legal_entity', ChoiceType::class,[
                'label' => 'invest-address-legal-entity-field',
                'attr' => [
                    'id' => 'fiscal-legal-entity',
                    'placeholder' => 'regular-choice-placeholder'
                ],
                'choices' => [
                    Text::get('donor-legal-entities-natural-person') => 'natural_person',
                    Text::get('donor-legal-entities-legal-person') => 'legal_person'
                ]
            ])
            ->add('name', TextType::class, [
                'label' => 'personal-field-donor_name',
                'attr' => [
                    'placeholder' => 'personal-field-donor_name'
                ],
                'required' => true
            ])
            ->add('surname', TextType::class, [
                'label' => 'personal-field-donor_surname',
                'attr' => [
                    'placeholder' => 'personal-field-donor_surname'
                ]
            ])->add('surname2', TextType::class, [
                'label' => 'personal-field-donor_surname2',
                'attr' => [
                    'placeholder' => 'personal-field-donor_surname2'
                ]
            ])
            ->add('gender', ChoiceType::class, [
                'choices' => [
                    'regular-male' => 'M',
                    'regular-female' => 'F',
                    'regular-other-gender' => 'O'
                ],
                'expanded' => true
            ])
            ->add('birthyear', ChoiceType::class, [
                'label' => 'invest-address-birthyear-field',
                'attr' => [
                    'placeholder' => 'invest-address-birthyear-field'
                ],
                'choices' => array_combine(range(date('Y') - 14, date('Y') - 100 ), range(date('Y') - 14, date('Y') - 100 ))
            ])
            ->add('legal_document_type', ChoiceType::class, [
                'label' => 'invest-address-legal-document-type-field',
                'attr' => [
                    'id' => 'fiscal-legal-document-type',
                    'placeholder' => 'invest-address-legal-document-type-field'
                ],
                'choices' => [
                    'cif' => Text::get('donor-legal-document-type-cif'),
                    'nif' => Text::get('donor-legal-document-type-nif'),
                    'nie' => Text::get('donor-legal-document-type-nie')
                ]
            ])
            ->add('nif', TextType::class, [
                'label' => 'invest-address-nif-field',
                'attr' => [
                    'placeholder' => 'invest-address-nif-field'
                ],
                'required' => true
            ])
            ->add('address', TextType::class, [
                'label' => 'invest-address-address-field',
                'required' => true,
                'attr' => [
                    'id' => 'fiscal-address',
                    'class' => 'form-control geo-autocomplete',
                    "data-geocoder-populate-address" => "#fiscal-address",
                    "data-geocoder-populate-city" => "#fiscal-location",
                    "data-geocoder-populate-region" => "#fiscal-region",
                    "data-geocoder-populate-zipcode" => "#fiscal-zipcode",
                    "data-geocoder-populate-country_code" => "#fiscal-country",
                    "data-geocoder-populate-latitude" => "#fiscal-latitude",
                    "data-geocoder-populate-longitude" => "#fiscal-longitude",
                    'placeholder' => 'invest-address-address-field'
                ]
            ])
            ->add('location', TextType::class, [
                'label' => 'invest-address-location-field',
                'attr' => [
                    'id' => 'fiscal-address',
                    'class' => 'form-control geo-autocomplete',
                    "data-geocoder-populate-address" => "#fiscal-address",
                    "data-geocoder-populate-city" => "#fiscal-location",
                    "data-geocoder-populate-region" => "#fiscal-region",
                    "data-geocoder-populate-zipcode" => "#fiscal-zipcode",
                    "data-geocoder-populate-country_code" => "#fiscal-country",
                    "data-geocoder-populate-latitude" => "#fiscal-latitude",
                    "data-geocoder-populate-longitude" => "#fiscal-longitude",
                    'placeholder' => 'invest-address-address-field'
                ],
                'required' => true
            ])
            ->add('zipcode', TextType::class, [
                'label' => 'invest-address-zipcode-field',
                'attr' => [
                    'id' => 'fiscal-zipcode',
                    'placeholder' => 'invest-address-zipcode-field'
                ],
                'required' => true
            ])
            ->add('region', TextType::class, [
                'label' => 'invest-address-country-field',
                'required' => true

            ])
            ->add('country', ChoiceType::class, [
                'label' => 'invest-address-country-field',
                'choices' => array_flip(Lang::listCountries()),
                'attr' => [
                    'id' => 'fiscal-country',
                    'placeholder' => 'invest-address-country-field',
                ],
                'data' => strtoupper($model->country)
            ])
            ->add('fiscal-latitude', HiddenType::class, [
                'data' => $model->latitude,
                'attr' => [
                    'id' => 'fiscal-latitude'
                ]
            ])
            ->add('fiscal-longitude', HiddenType::class, [
                'data' => $model->longitude,
                'attr' => [
                    'id' => 'fiscal-longitude'
                ]
            ])
        ;

        return $this;
    }

    public function save(FormInterface $form = null, $force_save = false)
    {
        $data = $form->getData();
        $model = $this->getModel();
        $model->rebuildData($data, array_keys($form->all()));
    }

}
