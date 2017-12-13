---
currentMenu: migration
---
MySQL migrations
================

Starting in version **3.2** a SQL migrations uses the library [LibMigration](http://kohkimakimoto.github.io/lib-migration/).

The console command `migrate` must be used to create sql migrations:

**Creates a new PHP Class file to write migrations inside:**

```bash
php bin/console create some_description
```

A new file will be created in `db/migrations/{timestamp}_dbname_some_description.php`, edit it and write the UP/DOWN SQL commands there.


To apply migrations these commands can be used:

**Shows the current status:**

```bash
php bin/console migrate
```

**Execute all the migrations up pending:**

```bash
php bin/console migrate all
```

**Execute only the next migration up pending:**

```bash
php bin/console migrate up
```


In case you need to downgrade:

**Execute the next migration down:**

```bash
php bin/console migrate up
```


Legacy views migration:
======================

This is about how to change Controllers and views (templates) from the old system to the new one:

## Response

New controllers are intended to be used with the [Route](http://symfony.com/doc/current/components/routing/introduction.html) component from Symfony. Therefore all must return a proper [Response](http://symfony.com/doc/current/components/http_foundation/introduction.html#response):

Controllers can also return redirections by using the [RedirectResponse](http://symfony.com/doc/current/components/http_foundation/introduction.html#redirecting-the-user) sub-class. In short, any sub-class of `Response` will do.

New controllers **must** follow these guidelines:
- Don't use global variables like `$_GET` or `$_POST`, Use the [Request](http://symfony.com/doc/current/components/http_foundation/introduction.html#request) object which will be automatically injected into the controller (better to put it as an argument at the end of the controller method). 
- Names of vars follows the Symfony [Route specification](http://symfony.com/doc/current/components/routing/introduction.html#load-routes-from-a-file), i.e. variables names must match the ones defined in the routes (view [Routes](#routes) for more info). The order of variables has no importance.
- Session global variables such as `$_SESSION` or `$_COOKIE` must be substituted by the static classes `src/Goteo/Application/Session.php` and `src/Goteo/Application/Cookie.php`. Take a look a the source code on that files for more info.

**Example**

```php
<?php
    // src/extend/goteo-private/src/Goteo/Controller/DiscoverAddonsController.php
    namespace Goteo\Controller;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\RedirectResponse;
    use Symfony\Component\HttpFoundation\Request;
    // use ...
    class DiscoverAddonsController extends \Goteo\Core\Controller {
        public function callAction () {
            return new RedirectResponse('/discover/calls');
            }
        public function callsAction () {
            return $this->viewResponse('discover/calls', [
                    'title' => Text::html('discover-calls-header'),
                    'list'  => Model\Call::getActive(null, true)
                   ]);
            }
        public function testAction ($foo, $bar = 'default', Request $request) {
            // Variable in "POST":
            $post_var = $request->request->get('var');
            // Variable in "GET":
            $get_var = $request->query->get('var');
        }
    }
?>
```

## Template system

The new `src/Goteo/Application/View.php` class must be used to provide the views. Legacy `src/Goteo/Core/View.php` must be progressively removed.

Rendering a view in a controller is easy as calling `return new Response(View::render(...));` as the return statement.

Controllers extending `src/Goteo/Core/Controller.php` can make use of some short cuts:

- `return $this->viewResponse('template', ['obj' => ...]);` instead of `return new Response(View::render('template', ['obj' => ...]));`
- `return $this->redirect('url');` instead of `return new RedirectResponse('url');`
- ...

## Migrating views

1. Views must be transferred from the old folder  `Resources/templates/legacy/controller/view.html.php` to the new `Resources/templates/default/controller/view.php`. Sub extension `.html.php` is not needed any more.

2. New view uses template inheritance so there's no need for including headers, prologues or any other common partial sub-view. 
Includes to `prologue.html.php`, `header.html.php`, etc. from the begging and the end of must be removed.

3. Rebuild the content of the templates in blocks (sections). Main sections are in the default layout `Resources/templates/default/layout.php`. 
All sub-views must implement section `content`. 
Long Javascript blocks should be avoided as much as possible. Little scripts can be included by extending the section `footer`.
    
**Example: including javascript inside a view:**

`Resources/templates/default/discover/index.php`

```php
<?php $this->layout("layout", ['bodyClass' => 'discover']) ?>

<?php $this->section('content') // Section 'content' in template "layout" will be replace by this ?>

    ... HTML main content ...

<?php $this->replace() ?>

<?php $this->section('footer') // This content will be appended into the section 'footer' in template "layout" ?>

    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            ...
        });

    </script>

<?php $this->append() ?>
```

4. References to the formers `$this['variable']` or `$vars['variable']` arrays must be replaced by calling the automatically escaped variable on the self-object `$this`: `<?= $this->variable ?>`. Un-escaped variables can be reached using the method `raw`: `<?= $this->raw('variable') ?>`. Check [Foil documentation on this matter](http://www.foilphp.it/docs/DATA/RETRIEVE-DATA.html) for more info.

5. Avoid calling full namespaced classes inside templates, particularly the heavily used `<?php echo Text::get('id') ?>` method must be changed by the `<?= $this->text('id') ?>` extension method.

For other handy methods, take a look into:

```
src/Goteo/Util/Foil/GoteoCore.php
src/Goteo/Util/Foil/LangUtils.php
src/Goteo/Util/Foil/Pages.php
src/Goteo/Util/Foil/TextUtils.php
```

**Note** plugins can also add methods in templates.

6. Also avoid using global vars inside templates. Needless to say that assign values into `$_SESSION` or `$_COOKIE` must be strictly avoided, 
Methods such as `$this->get_session('foo')` or `$this->get_cookie('foo')` can be used if absolutely necessary.

Refer to [templates documentation](templates.html#extensions) for more info.

<a name="routes"></a>
## Routes

Routes are defined primarily in the `src/routes.php` file. Plugins however can either add or replace routes. New routes use [Symfony route component](http://symfony.com/doc/current/components/routing/introduction.html)..

Consequently, in order for the controllers to be active in the routing sub-system, they must be added in the above-mentioned file.

**NOTES:**
- Do not append `/`  at the end of the route (there's a controller at the end which redirect any trailing slash `/` at the end of the route to the proper url)
- For readability, append the word **Action** at the end of the Controller method (e.g: `edit()` becomes into `editAction()`)

**Once all controller methods are processed**:

- Append **Controller** at the end of the controller's file name. That's for readability as well.

```php
<?php
// src/routes.php

$routes = new RouteCollection();

// ...

// This route has parameters, some of them optional:
// Matching urls:
//      /discover/results/2
//      /discover/resultss (if 'category' wasn't optional this url would not match)
$routes->add('discover-results', new Route(
    '/discover/results/{category}',
    array('category' => null,
          'name' => null,
          '_controller' => 'Goteo\Controller\DiscoverController::resultsAction',
          'category' => '' // optional parameter
         )
));
// This route has no parameters
$routes->add('discover', new Route(
    '/discover',
    array('_controller' => 'Goteo\Controller\DiscoverController::discoverAction')
));

return $routes;
?>
```


REAL EXAMPLE
------------

This is an example of the conversion for the view `discover/results.html.php` of the controller `DiscoverController`:

**Before:**

```php
<?php
// Resources/templates/legacy/discover/results.html.php

use Goteo\Core\View,
    Goteo\Library\Text;

$bodyClass = 'discover';

include __DIR__ . '/../prologue.html.php';

include __DIR__ . '/../header.html.php' ?>

        <div id="sub-header">
            <div>
                <h2 class="title"><?php echo Text::get('discover-results-header'); ?></h2>
            </div>

        </div>

        <div id="main">
            <?php echo View::get('discover/searcher.html.php',
                                array('params'     => $vars['params'])); ?>

            <div class="widget projects">
                <?php if (!empty($vars['results'])) :
                    foreach ($vars['results'] as $result) :
                        echo View::get('project/widget/project.html.php', array(
                            'project' => $result
                        ));
                    endforeach;
                else :
                    echo Text::get('discover-results-empty');
                endif; ?>
            </div>

        </div>

        <?php include __DIR__ . '/../footer.html.php' ?>

<?php include __DIR__ . '/../epilogue.html.php' ?>
```

**After**

```php
<?php
// Resources/templates/default/discover/results.php

$this->layout("layout", [
    'bodyClass' => 'discover', // Class variable into <body class="...">
    'title' => $this->text('meta-title-discover'), // Let's change the default page title
    'meta_description' => $this->text('meta-description-discover') // idem
    ]);

$this->section('content');

?>

    <div id="sub-header">
        <div>
            <h2 class="title"><?= $this->text('discover-results-header') ?></h2>
            </div>

    </div>

    <div id="main">

        <?= $this->insert('discover/partials/searcher', ['params' => $this->params]) ?>

        <div class="widget projects">
            <?php if ($this->results) : ?>
                <?php foreach ($this->results as $result) : ?>
                    <?= $this->insert('project/widget/project', ['project' => $result]) ?>
                    <?php endforeach ?>
            <?php else : ?>
                <?= $this->text('discover-results-empty') ?>
            <?php endif ?>
        </div>

    </div>

<?php $this->replace() ?>

```

Note that this conversion implies that the sub-views `Resources/templates/default/discover/partials/searcher.php` and `Resources/templates/default/project/widget/project.php` has been converted in a similar way.
