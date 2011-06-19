<?php use Goteo\Library\Text; ?>
<div class="project-menu">
    <ul>
        <?php
        foreach (array(
            'home'        => Text::get('project-menu-home'),
            'needs'       => Text::get('project-menu-needs'),
            'supporters'  => Text::get('project-menu-supporters').$this['supporters'],
            'messages'    => Text::get('project-menu-messages').$this['messages'],
            'updates'     => Text::get('project-menu-updates').$this['updates']
        ) as $id => $show): ?>        
        <li class="<?php echo $id ?>"><a href="/project/<?php echo htmlspecialchars($this['project']->id) ?>/<?php echo $id ?>"><?php if ($this['show'] === $id) echo '<strong>' ?><?php echo htmlspecialchars($show) ?><?php if ($this['show'] === $id) echo '</strong>' ?></a></li>        
        <?php endforeach ?>        
    </ul>
</div>
