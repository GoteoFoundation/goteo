<?php
use Goteo\Library\Text;

// aviso para el usuario, puede ser start->hola , ok->gracias o fail->lo sentimos

$user = $vars['user'];
if (!$user instanceof Model\User) {
    $name = '';
    $avatarhtml = '';
} else {
    $name = $user->name;
    $avatar = $user->avatar;
    $avatarhtml = '<img src="'.$avatar->getLink(50, 50, true).'" />';
}

switch ($vars['message']) {
    case 'start':
        $title   = Text::get('project-invest-guest');
        $message = Text::get('project-invest-start');
        break;
    case 'login':
        $title   = Text::get('regular-hello') . " $name";
        $message = Text::get('project-invest-login');
        break;
    case 'confirm':
        $title   = Text::get('regular-hello') . " $name";
        $message = Text::get('project-invest-confirm');
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

$level = (int) $vars['level'] ?: 3;

?>
<div class="widget invest-message">
    <h2><?php echo $avatarhtml; ?><span><?php echo $title; ?></span><br />
    <span class="message"><?php echo $message; ?></span></h2>


</div>
