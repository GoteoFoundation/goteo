<?php
use Goteo\Library\Text;

$data = $this['data'];

$filters = $_SESSION['mailing']['filters'];
$receivers = $_SESSION['mailing']['receivers'];
$users = $this['users'];

?>
<div class="widget">
    <p>La comunicación se ha enviado correctamente con este contenido:</p>
        <blockquote><?php echo $this['content'] ?></blockquote>
    
    <p><?php echo 'Buscábamos comunicarnos con ' . $_SESSION['mailing']['filters_txt']; ?> y finalmente hemos enviado a los siguientes destinatarios: </p>
        <blockquote><?php foreach ($users as $usr) {
                echo $receivers[$usr]->ok ? 'Enviado a ' : 'Fallo al enviar a ';
                echo '<strong>' .$receivers[$usr]->name . '</strong> ('.$receivers[$usr]->id.') al mail <strong>' . $receivers[$usr]->email . '</strong><br />';
        } ?></blockquote>
</div>

