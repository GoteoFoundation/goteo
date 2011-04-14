<?php

use Goteo\Library\Text,
    Goteo\Library\SuperForm;
            
$project = $this['project'];
$user = $this['user'];

$interests = array();

$errors = $project->errors[$this['step']] ?: array();

foreach ($this['interests'] as $value => $label) {
    $interests[] =  array(
        'value'     => $value,
        'label'     => $label,
        'checked'   => in_array($value, $user->interests)
        );            
}

$user_webs = array();

foreach ($user->webs as $web) {
    
    $user_webs['web' . $web->id] = array(
        'type'      => 'textbox',
        'value'     => $web->url,
        'hint'      => '',
        'errors'    => array(),
        'required'  => true
    );
    
}

echo new SuperForm(array(

    'action'        => '',
    'level'         => $this['level'],
    'method'        => 'post',
    'title'         => 'Usuario/Perfil',
    'hint'          => Text::get('guide-project-user-information'),    
    'footer'        => array(
        'view-step-userPersonal' => array(
            'type'  => 'submit',
            'label' => 'Siguiente',
            'class' => 'next'
        )        
    ),    
    'elements'      => array(
        
        'user_name' => array(
            'type'      => 'textbox',
            'required'  => true,
            'size'      => 20,
            'title'     => 'Nombre completo',
            'hint'      => Text::get('tooltip-user-name'),
            'errors'    => !empty($errors['name']) ? array($errors['name']) : array(),
            'value'     => $user->name,
        ),        
        
        'user_image' => array(      
            'title'     => 'Tu imagen',
            'hint'      => Text::get('tooltip-user-image'),
            'errors'    => !empty($errors['avatar']) ? array($errors['avatar']) : array()
            
        ),        
        
        'user_about' => array(
            'type'      => 'textarea',
            'cols'      => 40,
            'rows'      => 4,
            'title'     => 'Cuéntanos algo sobre ti',
            'hint'      => Text::get('tooltip-user-about'),
            'errors'    => !empty($errors['about']) ? array($errors['about']) : array(),
            'value'     => $user->about
        ),          

        'interests' => array(
            'type'      => 'checkboxes',
            'name'      => 'interests[]',
            'title'     => 'Tus intereses',
            'hint'      => Text::get('tooltip-user-interests'),            
            'errors'    => !empty($errors['interests']) ? array($errors['interests']) : array(),
            'options'   => $interests
        ),  
        
        'user_keywords' => array(
            'type'      => 'textbox',
            'size'      => 20,
            'title'     => 'Palabras clave',
            'hint'      => Text::get('tooltip-user-keywords'),
            'errors'    => !empty($errors['keywords']) ? array($errors['keywords']) : array(),
            'value'     => $user->keywords
        ), 
        
        'user_contribution' => array(
            'type'      => 'textarea',
            'cols'      => 40,
            'rows'      => 4,
            'title'     => 'Qué podrías aportar a Goteo',
            'hint'      => Text::get('tooltip-user-contribution'),
            'errors'    => !empty($errors['contribution']) ? array($errors['contribution']) : array(),
            'value'     => $user->contribution
        ),
        
        'user_webs' => array(
            'title'     => 'Mis webs',            
            'hint'      => Text::get('tooltip-user-blog'),
            'children'  => $user_webs
        ),
            
        /*
        <dt><label for="nweb">Nueva web</label></dt>
		<dd>http://<input type="text" id="nweb" name="nweb" value=""/></dd>
        <?php foreach ($user->webs as $web) : ?>
            <label>REMOVE! <input type="checkbox" name="remove-web<?php echo $web->id; ?>" value="1" /></label>
            <label><input type="text" name="web<?php echo $web->id; ?>" value="<?php echo $web->url; ?>" /></label>
            <hr />
        <?php endforeach; ?>
		<span><?php echo Text::get('tooltip user blog'); ?></span><br />

		<dt><label for="twitter">Twitter</label></dt>
		<dd>http://twitter.com/<input type="text" id="twitter" name="user_twitter" value="<?php echo $user->twitter; ?>"/></dd>
		<span><?php echo Text::get('tooltip user twitter'); ?></span><br />

		<dt><label for="facebook">Facebook</label></dt>
		<dd>http://facebook.com/<input type="text" id="facebook" name="user_facebook" value="<?php echo $user->facebook; ?>"/></dd>
		<span><?php echo Text::get('tooltip user facebook'); ?></span><br />

		<dt><label for="linkedin">Linkedin</label></dt>
		<dd>http://linkedin.com/<input type="text" id="linkedin" name="user_linkedin" value="<?php echo $user->linkedin; ?>"/></dd>
		<span><?php echo Text::get('tooltip user linkedin'); ?></span><br />

	</dl>
                    <?php else : ?>
                    <?php endif; ?>*/
        
        'user_social' => array(            
            'type'      => 'group',
            'title'     => 'Perfiles sociales',
            'children'  => array(
                'user_facebook' => array(
                    'type'      => 'textbox',
                    'class'     => 'facebook',
                    'size'      => 40,
                    'title'     => 'Facebook',
                    'hint'      => Text::get('tooltip-user-facebook'),
                    'errors'    => !empty($errors['facebook']) ? array($errors['facebook']) : array(),
                    'value'     => $user->facebook
                ), 

                'user_twitter' => array(
                    'type'      => 'textbox',
                    'class'     => 'twitter',
                    'size'      => 40,
                    'title'     => 'Twitter',
                    'hint'      => Text::get('tooltip-user-twitter'),
                    'errors'    => !empty($errors['twitter']) ? array($errors['twitter']) : array(),
                    'value'     => $user->twitter
                ), 

                'user_linkedin' => array(
                    'type'      => 'textbox',
                    'class'     => 'linkedin',
                    'size'      => 40,
                    'title'     => 'LinkedIn',
                    'hint'      => Text::get('tooltip-user-linkedin'),
                    'errors'    => !empty($errors['linkedin']) ? array($errors['linkedin']) : array(),
                    'value'     => $user->linkedin
                )
            )            
        )        
    )

));