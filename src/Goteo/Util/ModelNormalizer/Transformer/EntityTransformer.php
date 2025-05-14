<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */
namespace Goteo\Util\ModelNormalizer\Transformer;

use Goteo\Model\Image;

abstract class EntityTransformer extends AbstractTransformer {
    protected $model;
    protected $user;
    protected $keys = ['id', 'name'];

    public function __construct($model, array $keys = null) {
        $this->model = $model;
        if($keys) {
            $this->setKeys($keys);
        }
    }

    public function getRawValue($key) {
        if ($key == 'id')
            return $this->model->getId();

        if ($key == 'active')
            return $this->model->isActive();

        if ($key == 'actions')
            return null;

        $method = 'get' . ucfirst($key);

        return $this->model->$method();
    }


    public function getModel() {
        return $this->model;
    }

    /**
     * Return an API url to modifiy the property (or empty if doesn't exist)
     * @param  [type] $prop [description]
     * @return [type]       [description]
     */
    public function getApiProperty($prop) {
        return '/api/' . $this->getModelName(). '/' . $this->model->getId() . "/property/$prop";
    }

    public function getId() {
        return $this->model->getId();
    }

    public function getName() {
        return $this->model->getName ? $this->model->getName() : $this->model->getTitle();
    }

    public function getImage() {
        return Image::get($this->model->getImage())->getLink(64, 64, true);
    }

    public function getIcon() {
        return $this->model->getIcon()->getLink(64, 64, true);
    }

    public function getTitle() {
        return $this->model->getTitle();
    }

    public function getSubTitle() {
        return $this->model->getSubtitle();
    }

    public function getDate() {
        return $this->model->getDate() ? \date_formater($this->model->getDate()) : \date_formater($this->model->getDateIn());
    }

    public function getActions() {
        if(!$u = $this->getUser()) return [];
        $ret = ['edit' => '/admin/' . $this->getModelName() . '/edit/' . $this->model->getId()];
        return $ret;
    }

    public function getLangs() {
        return $this->model->getLangsAvailable();
    }

    public function getActive() {
        return $this->model->isActive();
    }

    public function getOrder() {
        return $this->model->getOrder();
    }

    public function getCity() {
        return $this->model->getCity();
    }


}
