<div class="project-menu">
    <ul>
        <?php
        foreach (array(
            'home'        => 'Proyecto',
            'needs'       => 'Necesidades',
            'supporters'  => "Cofinanciadores{$this['supporters']}",
            'messages'    => "Mensajes{$this['messages']}"
        ) as $id => $show): ?>        
        <li class="<?php echo $id ?>"><a href="/project/<?php echo htmlspecialchars($this['project']->id) ?>/<?php echo $id ?>"><?php if ($this['show'] === $id) echo '<strong>' ?><?php echo htmlspecialchars($show) ?><?php if ($this['show'] === $id) echo '</strong>' ?></a></li>        
        <?php endforeach ?>        
    </ul>
</div>
