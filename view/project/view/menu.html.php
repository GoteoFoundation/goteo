<?php use Goteo\Library\Text; ?>
<div class="project-menu">
    <ul>
        <?php
        foreach (array(
            'home'        => Text::get('project-menu-home'),
            'needs'       => Text::get('project-menu-needs'),
            'supporters'  => Text::get('project-menu-supporters').' <span class="digits">'.$this['supporters'].'</span>',
            'messages'    => Text::get('project-menu-messages').' <span class="digits">'.$this['messages'].'</span>',
            'updates'     => Text::get('project-menu-updates').' <span class="digits">'.$this['updates'].'</span>'
        ) as $id => $show): ?>        
        <li class="<?php echo $id ?><?php if ($this['show'] == $id) echo ' show' ?>">
        	<a href="/project/<?php echo htmlspecialchars($this['project']->id) ?>/<?php echo $id ?>"><?php echo $show ?></a>
        </li>
        <?php endforeach ?>        
    </ul>
</div>
