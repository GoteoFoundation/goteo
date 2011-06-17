<?php

use Goteo\Core\View,
    Goteo\Library\Worth,
    Goteo\Model\User\Interest;

$bodyClass = 'user-profile';
include 'view/prologue.html.php';
include 'view/header.html.php';

$user = $this['user'];
$worthcracy = Worth::getAll();

$interests = Interest::getAll();

?>

        <div id="sub-header">
            <div>
                <h2><img src="/image/<?php echo $user->avatar->id; ?>/75/75" /> <?php echo Texg::get('profile-name-header'); ?><br /><em><?php echo $user->name; ?></em></h2>
            </div>
        </div>

        <div id="main">
            
            <?php echo new View('view/worth/base.html.php', array('worthcracy' => $worthcracy, 'type' => 'main', 'level' => $user->worth)); ?>
                                    
            <div class="about">
                <h4><?php echo Texg::get('profile-about-header'); ?></h4>
                <p><?php echo $user->about ?></p>
                <hr />
                <h4><?php echo Texg::get('profile-interests-header'); ?></h4>
                <p><?php
                $c = 0;
                foreach ($user->interests as $interest) {
                    if ($c > 0) echo ', ';
                    echo $interests[$interest];
                    $c++;
                } ?></p>
                <hr />
                <h4><?php echo Texg::get('profile-keywords-header'); ?></h4>
                <p><?php echo $user->keywords; ?></p>
                <hr />
                <dl>

                    <?php if (!empty($user->webs)): ?>
                    <dt class="links"><?php echo Texg::get('profile-webs-header'); ?></dt>
                    <dd class="links">
                        <ul>
                            <?php foreach ($user->webs as $link): ?>
                            <li><a href="<?php echo htmlspecialchars($link->url) ?>" target="_blank"><?php echo htmlspecialchars($link->url) ?></a></li>
                            <?php endforeach ?>
                        </ul>
                    </dd>
                    <?php endif ?>

                    <?php if (isset($user->location)): ?>
                    <dt class="location"><?php echo Texg::get('profile-location-header'); ?></dt>
                    <dd class="location"><?php echo Text::GmapsLink($user->location); ?></dd>
                    <?php endif ?>

                </dl>

            </div>


            
            <?php if (isset($user->facebook) || isset($user->linkedin) || isset($user->twitter)): ?>            
            <div class="social">
                <h2 class="title"><?php echo Texg::get('profile-social-header'); ?></h2>
                <ul>
                    <?php if (isset($user->facebook)): ?>
                    <li class="facebook"><a href="<?php echo htmlspecialchars($user->facebook) ?>"><?php echo Texg::get('regular-facebook'); ?></a></li>
                    <?php endif ?>
                    <?php if (isset($user->twitter)): ?>
                    <li class="twitter"><a href="<?php echo htmlspecialchars($user->twitter) ?>"><?php echo Texg::get('regular-twitter'); ?></a></li>
                    <?php endif ?>
                    <?php if (isset($user->identica)): ?>
                    <li class="identica"><a href="<?php echo htmlspecialchars($user->identica) ?>"><?php echo Texg::get('regular-identica'); ?></a></li>
                    <?php endif ?>
                    <?php if (isset($user->linkedin)): ?>
                    <li class="linkedin"><a href="<?php echo htmlspecialchars($user->linkedin) ?>"><?php echo Texg::get('regular-linkedin'); ?></a></li>
                    <?php endif ?>
                </ul>                
            </div>            
            <?php endif ?>

            <div class="widget projects">
                <h2 class="title"><?php echo Texg::get('profile-invest_on-header'); ?></h2>
                <?php foreach ($this['invested'] as $project) : ?>
                    <div>
                        <?php
                        // es instancia del proyecto
                        echo new View('view/project/widget/project.html.php', array(
                            'project'   => $project
                        )); ?>
                    </div>
                <?php endforeach; ?>
    		</div>

            <div class="widget projects">
                <h2 class="title"><?php echo Texg::get('profile--header'); ?></h2>
                <?php foreach ($this['projects'] as $project) : ?>
                    <div>
                        <?php
                        // es instancia del proyecto
                        echo new View('view/project/widget/project.html.php', array(
                            'project'   => $project
                        )); ?>
                    </div>
                <?php endforeach; ?>
    		</div>

            <hr />
            <!-- lateral -->
            <div class="widget users user-supporters">
                <h3><?php echo Texg::get('profile-my_investors-header'); ?></h3>
                <div>
                <?php foreach ($this['investors'] as $user=>$investor) {
                    echo new View('view/user/widget/supporter.html.php', array('user' => $investor, 'worthcracy' => $worthcracy));
    //                echo "{$investor->avatar} {$investor->name} De nivel {$investor->worth}  Cofinancia {$investor->projects} proyectos  Me aporta: {$investor->amount} â‚¬ <br />";
                } ?>
                </div>
                <?php echo new View('view/worth/base.html.php', array('worthcracy' => $worthcracy, 'type' => 'side')); ?>
            </div>

            <div class="widget users user-mates">
                <h3><?php echo Texg::get('profile-sharing_interests-header'); ?></h3>
                <?php foreach ($this['shares'] as $share) {
                    echo '<div style="float:left;margin: 10px;"><img src="/image/' . $share->avatar->id . '/50/50" /><br />';
                    echo '<a href="/user/' . $share->user . '">' . $share->name . '</a><br />';
                    echo "Proyectos(" . $share->projects .")<br/>Aportacion(" . $share->invests ." )";
                    echo '</div>';
                } ?>
                </div>
            
            </div>


        </div>
        
    <?php include 'view/footer.html.php' ?>

<?php include 'view/epilogue.html.php' ?>