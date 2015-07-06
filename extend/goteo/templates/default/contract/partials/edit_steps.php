<div id="project-steps">

    <fieldset>

        <legend><h3><?= $this->text('form-navigation_bar-header') ?></h3></legend>

        <div class="steps">

        <?php foreach ($this->steps as $step => $stepData) : ?>
            <span class="step <?php echo $stepData['class']; ?><?php if ($this->step === $step) echo ' active'; else echo ' activable'; ?>">
                <button type="submit" name="step" value="<?php echo $step; ?>"><?php echo $stepData['name']; ?>
                <strong class="number"><?php echo $stepData['num']; ?></strong></button>
            </span>
        <?php endforeach; ?>

        </div>

    </fieldset>
</div>
