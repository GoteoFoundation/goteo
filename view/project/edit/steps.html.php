            <div id="project-steps">
                
                <h3>Ir a</h3>
                
                <?php if (isset($this['steps'])): ?>
                
                <ol>
                    
                    <?php foreach ($this['steps'] as $id => $step): ?>                    
                    <li class="step<?php if (!empty($step['offtopic'])) echo ' offtopic' ?>"><input type="submit" name="view-step-<?php echo htmlspecialchars($id) ?>" value="<?php echo htmlspecialchars($step['name']) ?>" /></li>
                    <?php endforeach ?>
                </ol>
                
                <?php endif ?>
            </div>