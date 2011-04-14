            <div id="project-steps">
                
                <h3>Ir a</h3>
                
                <?php if (isset($this['steps'])): ?>
                
                <ol>
                    
                    <?php foreach ($this['steps'] as $id => $step): ?>                    
                    <li class="step<?php if (!empty($step['offtopic'])) echo ' offtopic' ?>"><button type="submit" name="view-step-<?php echo htmlspecialchars($id) ?>" value="<?php echo htmlspecialchars($step['name']) ?>"><?php echo htmlspecialchars($step['name']) ?></button></li>
                    <?php endforeach ?>
                </ol>
                
                <?php endif ?>
            </div>