<?php
$langs = array(
    'es'    => 'Español',
    'ca'    => 'Català',
    'en'    => 'English'
);
?>

    <div id="lang">
        <?php foreach ($langs as $code => $name): ?>
            <?php if ($code === $_SESSION['lang']): ?>
            <strong><?php echo htmlspecialchars($name) ?></strong>
            <?php else: ?>
            <a href="/?lang=<?php echo $code ?>"><?php echo htmlspecialchars($name) ?></a>
            <?php endif ?>
        <?php endforeach ?>            
    </div>