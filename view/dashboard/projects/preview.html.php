<?php
use Goteo\Core\View;

echo new View ('view/dashboard/projects/selector.html.php', $this);
?>
<div class="widget">
    <h2 class="title">Página pública</h2>
    <p><a href="/project/<?php echo $_SESSION['project']->id; ?>" target="_blank">Abrir la página pública de '<?php echo $_SESSION['project']->name; ?>'</a></p>
</div>
