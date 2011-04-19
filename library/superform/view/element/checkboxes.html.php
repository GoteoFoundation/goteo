<ul>
    <?php foreach ($this['options'] as $checkbox): ?>
    <li><?php echo $checkbox->getInnerHTML() ?></li>
    <?php endforeach ?>
</ul>