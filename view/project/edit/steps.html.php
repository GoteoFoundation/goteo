<?php

use Goteo\Library\Text;

$errors = $this['errors'] ?>

<div id="project-steps">
            
            <fieldset>

                <legend><h3><?php echo Text::get('form-navigation_bar-header'); ?></h3></legend>

                <div class="steps">
                    
                    <span class="step first-off off<?php if ($this['step'] === 'userProfile') echo ' active'; else echo ' activable'; ?>">
                        <button type="submit" name="view-step-userProfile" value="userProfile"><?php echo Text::get('step-1'); ?>
                        <strong class="number">1</strong></button>
                    </span>
                    
                    <span class="step off-off off<?php if ($this['step'] === 'userPersonal') echo ' active'; else echo ' activable'; ?>">
                        <button type="submit" name="view-step-userPersonal" value="userPersonal"><?php echo Text::get('step-2'); ?>
                        <strong class="number">2</strong></button>
                    </span>
                    
                    <fieldset style="display: inline">
                        
                        <legend><?php echo Text::get('regular-new_project'); ?></legend>
                        
                        <span class="step off-on<?php if ($this['step'] === 'overview') echo ' active'; else echo ' activable'; ?>">
                            <button type="submit" name="view-step-overview" value="overview"><?php echo Text::get('step-3'); ?>
                            <strong class="number">3</strong></button>                            
                        </span>

                        <span class="step on-on<?php if ($this['step'] === 'images') echo ' active'; else echo ' activable'; ?>">
                            <button type="submit" name="view-step-images" value="images"><?php echo Text::get('step-3b'); ?>
                            <strong class="number">3'</strong></button>
                        </span>

                        <span class="step on-on<?php if ($this['step'] === 'costs') echo ' active'; else echo ' activable'; ?>">
                            <button type="submit" name="view-step-costs" value="costs"><?php echo Text::get('step-4'); ?>
                            <strong class="number">4</strong></button>
                        </span>

                        <span class="step on-on<?php if ($this['step'] === 'rewards') echo ' active'; else echo ' activable'; ?>">
                            <button type="submit" name="view-step-rewards" value="rewards>"><?php echo Text::get('step-5'); ?>
                            <strong class="number">5</strong></button>                            
                        </span>

                        <span class="step on-off<?php if ($this['step'] === 'supports') echo ' active'; else echo ' activable'; ?>">
                            <button type="submit" name="view-step-supports" value="supports"><?php echo Text::get('step-6'); ?>
                            <strong class="number">6</strong></button>                            
                        </span>
                        
                    </fieldset>
                    
                    <span class="step off-last off<?php if ($this['step'] === 'preview') echo ' active'; else echo ' activable'; ?>">
                        <button type="submit" name="view-step-preview" value="preview"><?php echo Text::get('step-7'); ?>
                        <strong class="number">7</strong></button>                        
                    </span>

                </div>

            </fieldset>
        </div>