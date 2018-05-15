<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Util\Foil\Extension;

use Foil\Contracts\ExtensionInterface;

use Goteo\Core\Model as CoreModel;
use Goteo\Model;
use Goteo\Util\ModelNormalizer\ModelNormalizer;

class ModelsData implements ExtensionInterface
{

    private $args;

    public function setup(array $args = [])
    {
        $this->args = $args;
    }

    public function provideFilters()
    {
        return [];
    }

    public function provideFunctions()
    {
        return [
          'page' => [$this, 'page'],
          'model_static' => [$this, 'model_static'],
          'model_list_entries' => [$this, 'model_list_entries'],
        ];
    }
    public function page($var)
    {
        return Model\Page::get($var);
    }

    public function model_static()
    {
        $args = func_get_args();
        $model = ucfirst(array_shift($args));
        $func = array_shift($args);
        if(!is_array($args)) $args = [];
        $class = "Goteo\Model\\$model";
        if(!class_exists($class)) $class = "Goteo\Library\\$model";
        if(!class_exists($class)) throw new \LogicException("Class [$model] not found, nor in Models or Library");
        if(!method_exists($class, $func)) throw new \LogicException("Method [$func] not found in class [$class]");
        $res = call_user_func_array([$class, $func], $args);
        // print_r($res);die;
        return $res;

    }

    /**
     * [model_list_entries description]
     * @param  [type] $list [description]
     * @return [type]       [description]
     */
    public function model_list_entries($list) {
        $array = [];
        if(!is_array($list)) return $array;

        foreach($list as $key => $ob) {
            $item = [];
            if($ob instanceOf CoreModel) {
                $normalizer = new ModelNormalizer($ob);
                $array[] = $normalizer->get();
            }
        }
        return $array;
    }

}
