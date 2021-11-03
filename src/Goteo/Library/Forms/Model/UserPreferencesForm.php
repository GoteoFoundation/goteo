<?php

/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Library\Forms\Model;

use Goteo\Application\Currency;
use Goteo\Application\Lang;
use Goteo\Application\Message;
use Goteo\Application\Session;
use Goteo\Model\User;
use Goteo\Library\Forms\AbstractFormProcessor;
use Goteo\Library\Text;
use Goteo\Util\Form\Type\BooleanType;
use Goteo\Util\Form\Type\ChoiceType;
use Goteo\Util\Form\Type\SubmitType;
use Symfony\Component\Form\FormInterface;;

class UserPreferencesForm extends AbstractFormProcessor {

    public function getConstraints($field): array
    {
        $constraints = [];
    }

    private function getLanguagesChoices(): array
    {
        return array_flip(Lang::listAll());
    }

    private function getCurrenciesChoices(): array
    {
        return array_flip(Currency::listAll('name'));
    }


    public function createForm() {
        $user = $this->getModel();
        $builder = $this->getBuilder();

        $userPreferences = $this->getDefaults(false);
        $preferredComLanguage = $userPreferences[User::PREFERENCE_COMMUNICATION_LANGUAGE];
        $preferredCurrency = $userPreferences[User::PREFERENCE_CURRENCY];

        foreach(User::BOOLEAN_PREFERENCES as $booleanPreference) {
            $userPreferences[$booleanPreference] = (bool) $userPreferences[$booleanPreference];
        }

        $builder
            ->add(User::PREFERENCE_COMMUNICATION_LANGUAGE, ChoiceType::class, [
                'label' => 'user-preferences-comlang',
                'choices' => $this->getLanguagesChoices(),
                'data' => $preferredComLanguage
            ]);

        $currencies = $this->getCurrenciesChoices();
        if(count($currencies) > 1) {
            $builder->add(User::PREFERENCE_CURRENCY, ChoiceType::class, [
                'label' => 'user-preferences-currency',
                'choices' => $currencies,
                'data' => $preferredCurrency
            ]);
        }

        foreach(User::BOOLEAN_PREFERENCES as $booleanPreference) {
            $builder
                ->add($booleanPreference, BooleanType::class, [
                    'label' => 'user-preferences-' . $booleanPreference,
                    'color' => 'cyan',
                    'required' => false
                ]);
        }

        $builder->add('submit', SubmitType::class, []);
    
        return $this;
    }

    public function save(FormInterface $form = null, $force_save = false) {
        if(!$form) $form = $this->getBuilder()->getForm();
        if(!$form->isValid() && !$force_save) throw new FormModelException(Text::get('form-has-errors'));

        $errors = [];
        $data = $form->getData();
        $user = $this->getModel();

        if (User::setPreferences($user, $data, $errors)) {
            Session::store(User::PREFERENCE_CURRENCY, $data['currency']);
            Message::info(Text::get('user-prefer-saved'));
        } else {
            Message::error(Text::get('form-sent-error', implode(', ',$errors)));
        }

        return $this;
    }
}
