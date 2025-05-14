<?php

namespace Goteo\Library\Forms;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

abstract class EntityFormProcessor extends AbstractFormProcessor {
    protected $entity;

    public function __construct(FormBuilderInterface $builder, $entity, array $options = []) {

        $this->setBuilder($builder);
        $this->setEntity($entity);
        $this->setOptions($options);
    }

    public function setEntity($entity): EntityFormProcessor
    {
        $this->entity = $entity;
        return $this;
    }

    public function getEntity() {
        return $this->entity;
    }
}