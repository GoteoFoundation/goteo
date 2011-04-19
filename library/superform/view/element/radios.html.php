<ul>
    <?php foreach ($this['options'] as $radio): ?>
    <li><?php echo $radio->getInnerHTML() ?></li>
    <?php endforeach ?>
</ul>