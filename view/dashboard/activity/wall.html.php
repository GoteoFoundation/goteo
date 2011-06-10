<?php
use Goteo\Core\View;
?>
<div class="widget">
    <h2 class="title">Mi muro</h2>
    <p><a href="/user/<?php echo $_SESSION['user']->id; ?>" target="_blank">Abrir la página pública de tu perfil</a></p>
</div>
