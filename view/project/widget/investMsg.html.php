<?php
// aviso para el usuario, puede ser start->hola , ok->gracias o fail->lo sentimos

$user = $this['user'];

switch ($this['message']) {
    case 'start':
        $title   = 'Hola ' . $user->name;
        $message = 'Estás a un paso de ser cofinanciador de este proyecto';
        break;
    case 'ok':
        $title   = 'Gracias ' . $user->name . '!';
        $message = 'Ya eres cofinanciador de este proyecto';
        break;
    case 'fail':
        $title   = 'Lo sentimos';
        $message = 'Algo ha fallado, por favor inténtalo de nuevo.';
        break;
}

$level = (int) $this['level'] ?: 3;

?>
<div class="widget project-summary">

    <div>
        <img src="/image/<?php echo $user->avatar->id; ?>/50/50" />
    </div>

    <div>
        <h<?php echo $level ?>><?php echo $title; ?></h<?php echo $level ?>>
        
        <p><?php echo $message; ?></p>
    </div>

</div>