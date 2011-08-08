<?php
use Goteo\Library\Lang;

$langs = Lang::getAll(true);
?>

    <ul class="lang">
        <?php foreach ($langs as $lang): ?>
            <?php if ($lang->id == LANG) continue; ?>
            <li >
            <a href="?lang=<?php echo $lang->id ?>"><?php echo htmlspecialchars($lang->short) ?></a>
            </li>          
        <?php endforeach ?>            
    </ul>