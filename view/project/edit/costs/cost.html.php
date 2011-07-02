<?php $cost = $this['data']['cost'] ?>

<div class="cost <?php echo $cost->type ?>">
    
    
    <div class="title"><strong><?php echo htmlspecialchars($cost->cost) ?></strong></div>
    
    <div class="description"><?php echo htmlspecialchars($cost->description) ?></div>
    
    <div class="amount"></div>
    
    <div class="from"></div>
    <div class="until"></div>
    
    <div class="required"></div>
    
    <input type="submit" class="edit" name="cost-<?php echo $cost->id ?>-edit" value="Editar" />    
    <input type="submit" class="remove" name="cost-<?php echo $cost->id ?>-remove" value="Quitar" />
    
</div>

    

    