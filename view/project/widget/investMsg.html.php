<?php
use Goteo\Library\Text;

// aviso para el usuario, puede ser start->hola , ok->gracias o fail->lo sentimos

$user = $this['user'];
$name = $user->name ? $user->name : Text::get('project-invest-guest');

switch ($this['message']) {
    case 'start':
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
    <h2><img src="/image/<?php echo $user->avatar->id; ?>/50/50" /><span><?php echo $title; ?></span><br />
    <span class="message"><?php echo $message; ?></span></h2>
    

</div>