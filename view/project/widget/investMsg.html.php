<?php
use Goteo\Library\Text,
    Goteo\Model;

// aviso para el usuario, puede ser start->hola , ok->gracias o fail->lo sentimos

$user = $this['user'];
if (!$user instanceof Model\User) {
    $name = Text::get('project-invest-guest');
    $avatar = Model\Image::get(1);
} else {
    $name = $user->name;
    $avatar = ($user->avatar instanceof Model\Image) ? $user->avatar : Model\Image::get(1);
}

switch ($this['message']) {
    case 'start':
        $title   = Text::get('regular-hello') . " $name";
        $message = Text::get('project-invest-start');
        break;
    case 'login':
        $title   = Text::get('regular-hello') . " $name";
        $message = Text::get('project-invest-start');
        break;
    case 'confirm':
        $title   = Text::get('regular-hello') . " $name";
        $message = Text::get('project-invest-start');
        break;
    case 'continue':
        $title   = Text::get('regular-hello') . " $name";
        $message = Text::get('project-invest-continue');
        break;
    case 'ok':
        $title   = Text::get('regular-thanks') . " {$name}!";
        $message = Text::get('project-invest-ok');
        break;
    case 'fail':
        $title   = Text::get('regular-sorry') . " {$name}";
        $message = Text::get('project-invest-fail');
        break;
}

$level = (int) $this['level'] ?: 3;

?>
<div class="widget invest-message">
    <h2><img src="<?php echo $avatar->getLink(50, 50, true); ?>" /><span><?php echo $title; ?></span><br />
    <span class="message"><?php echo $message; ?></span></h2>


</div>
