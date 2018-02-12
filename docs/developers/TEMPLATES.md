---
currentMenu: templates
---
## Template system

The new template system in Goteo uses [FOIL](https://foilphp.github.io/Foil/) which is a powerful php-native template system.

However, there's still a lot of the legacy views in use. They will coexist until we can fully get rid of the old system.

This document compares the old and new template system and highlights the main differences:

**Old class system:**

`\Goteo\Core\View::get('template.html.php', array( 'obj' => ... ));`


**New Foil base templates:**

`\Goteo\Application\View::render('template', [ 'obj' => ... ]);`

Or, inside a Controller which extends `\Goteo\Core\Controller` just do:

`$this->viewResponse('template', ['obj' => ...]);`

Main differences
----------------

**Before:**

Variables were located inside the array  `$vars[]` (or event for even older behaviour inside the `$this` [ArrayObject](http://php.net/manual/es/class.arrayobject.php) instance);

File `template.html.php`

```php
<?php

// Object
$obj = $this['obj'];

$obj->doSomething();

echo htmlspecialchars($this['obj']->doMore()); // Strings needed to be manually escaped

// Scalar
echo htmlspecialchars($this['variable']); // escaped
echo $this['variable']; // non-escaped

?>
```

**After:**

Variables are instantiated by default as properties of the object `$this` (that's Foil's behaviour).

**NOTE**: For compatibility reasons an automatic array `$this->vars` is created with all variables copied inside. New views (or completely refactored) must not use this array.

File `template.php`

```php
<?php
// Object
$obj->doSomething();

echo $this->e($this->raw('obj')->doMore()); // Escaped with $this->e() function

// Scalar
echo $this->variable; // HTML escaped by default (strings & arrays)
echo $this->raw('variable'); // Non escaped

// Compatibility with old views needing and array
echo $this->vars['variable']; // HTML escaped if it's a string or a simple array
?>
```

New features
------------

FOIL supports inheritance, custom functions & a great variables manipulation capabilities between templates.

**Basic operation:**

It starts with a basic layout used as the base by the other templates. Please refer to the official documentation of Foil to know more about this.

File `layout.php`

```php
<html>
<head>
    <title><?= $this->title ?></title>
</head>
<body>

<?php $this->section('content') ?>
<?php $this->stop() ?>

</body>
</html>
```


Let's override some vars in a final template extending `layout.php`, in this case the *title* variable and the section *content*:

`profile.php`

```php
<?php $this->layout('template', ['title' => 'User Profile']) ?>

<?php $this->section('content') ?>
    <h1>User Profile</h1>
    <p>Hello, <?=$this->name?></p>
<?php $this->stop() ?>
```

More info:

https://foilphp.github.io/Foil/docs/TEMPLATES/INHERITANCE.html
https://foilphp.github.io/Foil/docs/DATA/PASS-DATA.html


Template inheritance:
--------------------

The new Goteo views can have themes, currently there are 2, `default` & `responsive` (which will be the future theme). Located under the folder `Resources/templates/`. There's another sub-folder `legacy` which contains the old view system (which will disappear someday).

Plugins can override any view by recreating the same structure and placing a new view with the same name:

```text
Resources/templates                  <- Main template folder
Resources/templates/legacy           <- Default deprecated theme (old system)
Resources/templates/default          <- Default non-responsive theme (migrated)
Resources/templates/responsive       <- New responsive theme (brand new)
extend/{plugin}/Resources/templates  <- Main view plugin template overrider
                                        Any view here will be processed before the default one
```

**Example:**

Having this default view and the plugin substitute:

```
Resources/templates/devault/discover/index.php
extend/my-plugin/Resources/templates/devault/discover/index.php
```

When a controller calls the view, the second one will be used if the plugin is activated. If the plugin is not activated, then the first one will be used:

**Controller**

```php
<?php
// src/Goteo/Controller/DiscoverController.php

namespace Goteo\Controller;

class DiscoverController extends \Goteo\Core\Controller {
    public function index () {
        // This will render the discover/index.php file 
        // from my-plugin if activated
        return $this->viewResponse('discover/index',
            [ 'test' => 'automatic html escaped text' ]
        );
    }
}
?>
```

**Default view**:

```php
<?php 
// Resources/templates/default/discover/index.php
$this->layout("layout", ['bodyClass' => 'discover']);
?>

<?php $this->section('content') ?>

Show me the test var: <?=$this->test?>

<?php $this->replace() ?>
```

**Plugin view**:

```php
<?php 
// extend/my-plugin/Resources/templates/default/discover/index.php
$this->layout("layout", ['bodyClass' => 'discover']);
?>

<?php $this->section('content') ?>

I am the plugin view,
Show me the test var: <?=$this->test?>

<?php $this->replace() ?>
```

Plugin views can also extend from a default view instead of the default layout:

```php
<?php 
// extend/my-plugin/Resources/templates/default/discover/index.php
$this->layout("default:discover/index");
?>

<?php $this->section('content') ?>

I am the plugin view

<?php $this->replace() ?>
```

Views can have partials, same principle applies to it.  
Better check the [Foil's](http://www.foilphp.it/docs/TEMPLATES/PARTIALS.html) documentation for more information.

```text
Resources/templates/default/partials/  <- Partials views better placed
                                          in a specific folder
```

Partial include example (`Resources/templates/default/layout.php`):

```php
<html>
<head>
<?=$this->insert("partials/header/metas")?>
</head>

<body>

<?php $this->section('content') ?>
<?php $this->stop() ?>

</body>
</html>
```

The partial `Resources/templates/defaul/partials/header/metas.php` will inherit all variables from `layout.php`

<a name="extensions"></a>
Template extensions:
-------------------

Views can make use of many built-in functions in order to avoid calling Classes or main functions directly. This classes provides additional functionality to views in Goteo:

```
src/Goteo/Util/Foil/GoteoCore.php
src/Goteo/Util/Foil/LangUtils.php
src/Goteo/Util/Foil/Pages.php
src/Goteo/Util/Foil/TextUtils.php
```

**Some useful functions**
    - `$this->text('id-text')` => Get the copy of the `id-text` in the current language
    - `$this->get_session('foo')` => Gets the session var "foo"
    - `$this->get_cookie('foo')` => Gets the cookie var "foo"
    - `$this->is_logged()` => Return "true" if user is logged in
    - ...

Custom functions can be called like this:

```php
<a href="#"><?= $this->text('view-project') ?></a>
```

Guidelines
----------

Ideally, templates must be kept as simple as possible, we will try to follow these guidelines for the new views using Foil (extracted from the [Plates templates system](http://platesphp.com/templates/syntax/)):

- Always use HTML with inline PHP. Never use blocks of PHP.
- Always escape potentially dangerous variables prior to outputting using the built-in escape functions. More on escaping [here](http://www.foilphp.it/docs/DATA/RETRIEVE-DATA.html).
- Always use the short echo syntax (`<?=`) when outputting variables. For all other inline PHP code, use full the `<?php` tag. Do not use [short tags](http://us3.php.net/manual/en/ini.core.php#ini.short-open-tag).
- Always use the [alternative syntax for control structures](http://php.net/manual/en/control-structures.alternative-syntax.php), which are designed to make templates more legible.
- Never use PHP curly brackets.
- Only ever have one statement in each PHP tag.
- Avoid using semicolons. They are not needed when there is only one statement per PHP tag.
- Never use the `use` operator. Templates should not be interacting with classes in this way. Use [custom extensions](http://www.foilphp.it/docs/EXTENDING/CUSTOM-EXTENSIONS.html) for that.
- Never use the `for`, `while` or `switch` control structures. Instead use `if` and `foreach`. Additionally you can take a look at the built-in [loop](http://www.foilphp.it/docs/FUNCTIONS/LOOP-HELPERS.html) and [array](http://www.foilphp.it/docs/FUNCTIONS/ARRAY-HELPERS.html) Foil structures
- Avoid variable assignment.
