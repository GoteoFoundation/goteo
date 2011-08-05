<?php
use Goteo\Library\Lang;

$langs = Lang::getAll(true);
?>

    <div id="lang">
        <?php foreach ($langs as $lang): ?>
            <?php if ($lang->id == LANG): ?>
            <strong><?php echo htmlspecialchars($lang->short) ?></strong>
            <?php else: ?>
            <a href="?lang=<?php echo $lang->id ?>"><?php echo htmlspecialchars($lang->short) ?></a>
            <?php endif ?>
        <?php endforeach ?>            
    </div>