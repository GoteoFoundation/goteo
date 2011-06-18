<?php $errors = $this['errors'] ?>

<div id="project-steps">
            
            <fieldset>

                <legend><h3><?php echo Text::get('form-navigation_bar-header'); ?></h3></legend>

                <div class="steps">
                    
                    <span class="step first-off off<?php if ($this['step'] === 'userProfile') echo ' active' ?><?php if (!empty($errors['userProfile'])) echo ' error' ?>">
                        <button type="submit" name="view-step-userProfile" value="<?php echo Text::get('step-1'); ?>"><?php echo Text::get('step-1'); ?>
                        <?php if (!empty($errors['userProfile'])) echo ' <strong class="errors">', number_format(count($errors['userProfile'])), '</strong>' ?></button>                        
                    </span>
                    
                    <span class="step off-off off<?php if ($this['step'] === 'userPersonal') echo ' active' ?><?php if (!empty($errors['userPersonal'])) echo ' error' ?>">
                        <button type="submit" name="view-step-userPersonal" value="<?php echo Text::get('step-2'); ?>"><?php echo Text::get('step-2'); ?>
                        <?php if (!empty($errors['userPersonal'])) echo ' <strong class="errors">', number_format(count($errors['userPersonal'])), '</strong>' ?></button>
                    </span>
                    
                    <fieldset style="display: inline">
                        
                        <legend><?php echo Text::get('regular-new_project'); ?></legend>
                        
                        <span class="step off-on<?php if ($this['step'] === 'overview') echo ' active' ?><?php if (!empty($errors['overview'])) echo ' error' ?>">
                            <button type="submit" name="view-step-overview" value="<?php echo Text::get('step-3'); ?>"><?php echo Text::get('step-3'); ?>
                            <?php if (!empty($errors['overview'])) echo ' <strong class="errors">', number_format(count($errors['overview'])), '</strong>' ?></button>                            
                        </span>

                        <span class="step on-on<?php if ($this['step'] === 'costs') echo ' active' ?><?php if (!empty($errors['costs'])) echo ' error' ?>">
                            <button type="submit" name="view-step-costs" value="<?php echo Text::get('step-4'); ?>"><?php echo Text::get('step-4'); ?>
                            <?php if (!empty($errors['costs'])) echo ' <strong class="errors">', number_format(count($errors['costs'])), '</strong>' ?></button>                            
                        </span>

                        <span class="step on-on<?php if ($this['step'] === 'rewards') echo ' active' ?><?php if (!empty($errors['rewards'])) echo ' error' ?>">
                            <button type="submit" name="view-step-rewards" value="<?php echo Text::get('step-5'); ?>"><?php echo Text::get('step-5'); ?>
                            <?php if (!empty($errors['rewards'])) echo ' <strong class="errors">', number_format(count($errors['rewards'])), '</strong>' ?></button>                            
                        </span>

                        <span class="step on-off<?php if ($this['step'] === 'supports') echo ' active' ?><?php if (!empty($errors['supports'])) echo ' error' ?>">
                            <button type="submit" name="view-step-supports" value="<?php echo Text::get('step-6'); ?>"><?php echo Text::get('step-6'); ?>
                            <?php if (!empty($errors['supports'])) echo ' <strong class="errors">', number_format(count($errors['supports'])), '</strong>' ?></button>                            
                        </span>
                        
                    </fieldset>
                    
                    <span class="step off-last off<?php if ($this['step'] === 'preview') echo ' active' ?><?php if (!empty($errors['preview'])) echo ' error' ?>">
                        <button type="submit" name="view-step-preview" value="<?php echo Text::get('step-7'); ?>"><?php echo Text::get('step-7'); ?>
                        <?php if (!empty($errors['preview'])) echo ' <strong class="errors">', number_format(count($errors['preview'])), '</strong>' ?></button>                        
                    </span>

                </div>

            </fieldset>
        </div>