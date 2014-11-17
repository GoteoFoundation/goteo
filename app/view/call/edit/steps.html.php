<?php

use Goteo\Library\Text;


/*
 * No hay más paso dos
                    <span class="step off-off off<?php if ($this['step'] === 'userPersonal') echo ' active'; else echo ' activable'; ?>">
                        <button type="submit" name="view-step-userPersonal" value="<?php echo Text::get('step-2'); ?>"><?php echo Text::get('step-2'); ?>
                        <strong class="number">2</strong></button>
                    </span>
 */
?>
<div id="project-steps">
            
            <fieldset>

                <legend><h3><?php echo Text::get('form-navigation_bar-header'); ?></h3></legend>

                <div class="steps">
                    
                    <a href="/call/edit/<?php echo $this['id_call']; ?>/userProfile">
                    <span class="step first-off off<?php if ($this['step'] === 'userProfile') echo ' active'; else echo ' activable'; ?>">
                        <button type="button" name="view-step-userProfile" value="userProfile"><?php echo Text::get('step-1'); ?>
                        <strong class="number"></strong></button>
                    </span>
                    </a>

                    <a href="/call/edit/<?php echo $this['id_call']; ?>/overview">
                    <span class="step on-on<?php if ($this['step'] === 'overview') echo ' active'; else echo ' activable'; ?>">
                        <button type="button" name="view-step-overview" value="overview"><?php echo Text::get('step-3'); ?>
                        <strong class="number"></strong></button>
                    </span>
                    </a>

                    <a href="/call/edit/<?php echo $this['id_call']; ?>/supports">
                    <span class="step on-on<?php if ($this['step'] === 'supports') echo ' active'; else echo ' activable'; ?>">
                        <button type="button" name="view-step-supports" value="supports"><?php echo Text::get('call-step-3'); ?>
                        <strong class="number"></strong></button>
                    </span>
                    </a>

                    <a href="/call/edit/<?php echo $this['id_call']; ?>/preview">
                    <span class="step off-last off<?php if ($this['step'] === 'preview') echo ' active'; else echo ' activable'; ?>">
                        <button type="button" name="view-step-preview" value="preview"><?php echo Text::get('step-7'); ?>
                        <strong class="number"></strong></button>
                    </span>
                    </a>

                </div>

            </fieldset>
        </div>