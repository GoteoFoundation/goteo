        <div id="dashboard-menu">
            <ul>
            <?php foreach ($this['menu'] as $section=>$item) : ?>
                <li class="section<?php if ($section == $this['section']) echo ' active'; ?>">
                    <a class="section" href="/dashboard/<?php echo $section; ?>"><?php echo $item['label']; ?></a>
                    <ul>
                    <?php foreach ($item['options'] as $option=>$label) : ?>
                        <li class="option<?php if ($option == $this['option']) echo ' active'; ?>">
                            <a href="/dashboard/<?php echo $section; ?>/<?php echo $option; ?>"><?php echo $label; ?></a>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                </li>
            <?php endforeach; ?>
            </ul>
        </div>

