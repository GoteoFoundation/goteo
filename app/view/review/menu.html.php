        <div id="review-menu">
            <ul>
            <?php foreach ($vars['menu'] as $section=>$item) : ?>
                <li class="section<?php if ($section == $vars['section']) echo ' current'; ?>">
                    <a href="/review/<?php echo $section; ?>"><?php echo $item['label']; ?></a>
                    <ul>
                    <?php foreach ($item['options'] as $option=>$label) : ?>
                        <li class="option<?php if ($section == $vars['section'] && $option == $vars['option']) echo ' current'; ?>">
                            <a href="/review/<?php echo $section; ?>/<?php echo $option; ?>"><?php echo $label; ?></a>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                </li>
            <?php endforeach; ?>
            </ul>
        </div>

