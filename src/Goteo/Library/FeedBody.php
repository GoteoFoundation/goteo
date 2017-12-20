<?php
/*
* This file is part of the Goteo Package.
*
* (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
*
* For the full copyright and license information, please view the README.md
* and LICENSE files that was distributed with this source code.
*/
namespace Goteo\Library;

use Goteo\Library\Text;

class FeedBody {
    static public $page = array(
        'user' => '/user/profile/',
        'project' => '/project/',
        'call' => '/call/',
        'matcher' => '/matcher/',
        'drop' => SITE_URL,
        'blog' => '/blog/',
        'news' => '/news/',
        'relevant' => '',
        'comment' => '/blog/',
        'update-comment' => '/project/',
        'message' => '/project/',
        'system' => '/admin/',
        'update' => '/project/',
        'translate' => '/translate/',
        'money' => '/admin/accounts/details/'
    );

    static public $color = array(
        'user' => 'blue',
        'project' => 'light-blue',
        'call' => 'light-blue',
        'matcher' => 'light-blue',
        'blog' => 'grey',
        'news' => 'grey',
        'money' => 'violet',
        'drop' => 'violet',
        'relevant' => 'red',
        'comment' => 'green',
        'update-comment' => 'grey',
        'message' => 'green',
        'system' => 'grey',
        'update' => 'grey',
        'translate' => 'grey'
    );

    protected $type;
    protected $label;
    protected $id;
    protected $vars;

    public function __construct($type = null,  $id = null, $label = 'label', array $vars = []) {
        $this->type = $type;
        $this->label = $label;
        $this->id = $id;
        $this->vars = $vars;
    }

    public function render() {

        $vars = $this->vars;
        foreach($vars as $key => $item) {
            if($item instanceOf self) {
                $vars[$key] = $item->render();
            }
        }
        $txt = Text::lang($this->label, null, $vars);

        // print_r($this);die;

        if($this->type) {
            $txt = self::item($this->type, $txt, $this->id);
        }
        return $txt;
    }

    /**
     *  Genera codigo html para enlace o texto dentro de feed
     *
     */
    public static function item ($type = 'system', $label = 'label', $id = null) {

        // si llega id es un enlace
        if (isset($id)) {
            return '<a href="'.self::$page[$type].$id.'" class="'.self::$color[$type].'" target="_blank">'.$label.'</a>';
        } else {
            return '<span class="'.self::$color[$type].'">'.$label.'</span>';
        }


    }

    public function getVar($var) {
        return $this->vars[$var];
    }
}
