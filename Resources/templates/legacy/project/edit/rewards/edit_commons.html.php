<?php
use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\NormalForm;

$project = $vars['project'];
$reward = $vars['reward'];

$types = array();

foreach ($vars['stypes'] as $type) {

    $licenses = array();

    if (!empty($type->licenses)) {
        foreach ($type->licenses as $lid => $license) {

            if (!empty($license->url)) {
                $url = ' <a href="'.$license->url.'" target="_blank" class="license-hint-details">'.$txt_details.'</a>';
            } else {
                $url = '';
            }

            $licenses["social_reward-{$reward->id}-license-{$license->id}"] = array(
                'name'  => "social_reward-{$reward->id}-{$type->id}-license",
                'label' => $license->name,
                'value' => $license->id,
                'type'  => 'radio',
                'class' => 'license license_' . $license->id,
                'hint'  => $license->description .  $url,
                'id'    => "social_reward-{$reward->id}-license-{$license->id}",
                'checked' => $license->id == $reward->license ? true : false
            );

        }
    }

    if ($type->id == 'other') {
        // un campo para especificar el tipo
        $children = array(
            "social_reward-{$reward->id}-other" => array(
                'type'      => 'textbox',
                'class'     => 'inline other',
                'title'     => Text::get('rewards-field-social_reward-other'),
                'value'     => $reward->other,
                'name'      => "social_reward-{$reward->id}-{$type->id}",
                'hint'      => Text::get('tooltip-project-social_reward-icon-other')
            )
        );
    } elseif (!empty($licenses)) {
        $children = array(
            "social_reward-{$reward->id}-license" => array(
                'type'      => 'group',
                'class'     => 'license',
                'title'     => Text::get('rewards-field-social_reward-license'),
                'children'  => $licenses,
                'value'     => $reward->license,
                'name'      => "social_reward-{$reward->id}-{$type->id}-license"
            )
        );
    } else {
        $children = array(
            "social_reward-{$reward->id}-license" => array(
                'type' => 'hidden',
                'name' => "social_reward-{$reward->id}-{$type->id}-license"
            )
        );
    }


    $types["social_reward-{$reward->id}-icon-{$type->id}"] =  array(
        'name'  => "social_reward-{$reward->id}-icon",
        'value' => $type->id,
        'type'  => 'radio',
        'class' => "social_reward-type reward-type reward_{$type->id} social_{$type->id}",
        'label' => $type->name,
        'hint'  => $type->description,
        'id'    => "social_reward-{$reward->id}-icon-{$type->id}",
        'checked' => $type->id == $reward->icon ? true : false,
        'children' => $children
    );

}

?>
<form method="post" action="<?php echo $vars['path']; ?>/edit/<?php echo $project->id; ?>?reward_id=<?php echo $reward->id; ?>" class="project">
    <input type="hidden"  name="action" value="<?php echo $vars['action']; ?>" />

<?php
$sfid = 'sf-project-rewards';
$level = 3;

echo new NormalForm(array(

    'id'            => $sfid,
    'action'        => '',
    'level'         => $level,
    'method'        => 'post',
    'class'         => 'aqua',
    'elements'      => array(
        "social_reward-{$reward->id}" => array (
            'type' => 'hidden',
            'value' => $reward->id
        ),

        'social_rewards' => array(
            'type'      => 'group',
            'class'     => 'reward social_reward editsocial_reward',
            'children'  => array(
                "social_reward-{$reward->id}-edit" => array(
                    'type'      => 'hidden',
                    'value'     => '1'
                ),
                "social_reward-{$reward->id}-reward" => array(
                    'title'     => Text::get('rewards-field-social_reward-reward'),
                    'type'      => 'textbox',
                    'required'  => true,
                    'class'     => 'inline',
                    'value'     => $reward->reward,
                    'hint'      => Text::get('tooltip-project-social_reward-reward')
                ),
                "social_reward-{$reward->id}-description" => array(
                    'type'      => 'textarea',
                    'required'  => true,
                    'title'     => Text::get('rewards-field-social_reward-description'),
                    'cols'      => 100,
                    'rows'      => 4,
                    'class'     => 'inline reward-description',
                    'value'     => $reward->description,
                    'hint'      => Text::get('tooltip-project-social_reward-description')
                ),
                "social_reward-{$reward->id}-url" => array(
                    'title'     => Text::get('rewards-field-social_reward-url'),
                    'type'      => 'textbox',
                    'required'  => false,
                    'class'     => 'inline',
                    'value'     => $reward->url,
                    'hint'      => Text::get('tooltip-project-social_reward-url')
                ),
                "social_reward-{$reward->id}-icon" => array(
                    'title'     => Text::get('rewards-field-social_reward-type'),
                    'class'     => 'inline',
                    'type'      => 'group',
                    'required'  => true,
                    'children'  => $types,
                    'value'     => $reward->icon,
                    'hint'      => Text::get('tooltip-project-social_reward-type')
                ),
            )
        ),

        "submit" => array(
            'type' => 'html',
            'html' => '<input type="submit" value="GUARDAR" />'
        )


    )

));
?>
</form>
