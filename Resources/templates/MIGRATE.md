Template migration
=======================

The new system of templates in Goteo uses PHP FOIL:
http://www.foilphp.it

**Old class system:**

`\Goteo\Core\View::get('template.html.php', array( 'obj' => ... ));`



**New Foil base templates:**

`\Goteo\Application\View::render('template', [ 'obj' => ... ]);`

Or, inside a Controller which extends `\Goteo\Core\Controller` ust do:

`$this->viewResponse('template', ['ojb' => ...]);`

Main differences
----------------

**Before:**

Variables where located inside the array  `$vars[]` (or event for even older behaviour inside the `$this` [ArrayObject](http://php.net/manual/es/class.arrayobject.php) instance);

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

**NOTE**: For compatibility reasons it an automatic array `$this->vars` it's created with all variables copied inside. New views (or completely refactored) must not use this array.

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

Things starts with a basic layout used as base by the other templates. Please refer to the official documentation of Foil to know more about this.

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
http://www.foilphp.it/docs/TEMPLATES/INHERITANCE.html
http://www.foilphp.it/docs/DATA/PASS-DATA.html


Legacy views migration:
---------------------------------

Goteo views can have themes, currently 2 (`default` & `responsive`)

**First level**


Multiples folders
Multiples directorios que permiten sobreescritura de vistas segun las extensiones cargadas

```text
Resources/templates               <- Carpeta de vistas generales
extend/{plugin}/Resources/templates  <- Carpeta de vistas de plugins 
                           (si existe una vista aquí sobreescribira la general)
```

Las vistas del primer nivel no se llaman directamente desde los controladores,
se llaman con la función `$this->layout('...')` dentro de otras vistas

**Segundo nivel**

Multiples directorios que permiten sobreescritura de vistas segun el tema usado
(por ejemplo: nodos, convocatorias, widgets, etc)

```text
Resources/templates/default      <- primer nivel, indica el tema a usar
Resources/templates/node            Un tema son paginas dentro de los tags <html></html>
Resources/templates/...
```

Ejemplo de llamada en un controlador:

```php
<?php
// src/Goteo/Controller/DiscoverController.php

namespace Goteo\Controller;

use Symfony\Component\HttpFoundation\Response;

class DiscoverController extends \Goteo\Core\Controller {
    public function index () {
        return new Response(View::render(
            'discover/index',
            [ 'test' => 'Es un texto que se escapará automáticamente' ]
         ));
    }
}
?>
```

Como se ve, la ruta empieza en el tercer nivel (`discover`), la primera parte se busca automaticamente segun el tema en el que estamos (nodo, convocatoria, etc)

**Tercer nivel**

El tercel nivel es territorio de controladores, vistas que heredan del tema principal:

```text
Resources/templates/default/errors/   <- tercer nivel estan las secciones de cada tema
Resources/templates/default/home/        Por ej. vistas principales de los controladores
Resources/templates/default/discover/            que usan herencia
```

Ejemplo de uso en una vista (`Resources/templates/default/discover/index.php`):

```php
<?php $this->layout("layout", ['bodyClass' => 'discover']) ?>

<?php $this->section('content') ?>

Contenido de la vista:
<?=$this->test?>

<?php $this->replace() ?>
```

También dentro de una carpeta `partials` (para especificar que no son controladores que heredan) se pueden poner partes de vistas que se incluyen dentro de otras:

```text
Resources/templates/default/partials/  <- O bien bloques parciales que se incluyen en la
                                vista padre
```

Ejemplo de uso de inclusión de un parcial (`Resources/templates/default/layout.php`):

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

La vista `partials/header/metas` heredará las variables que tenga `layout.php`

OBJETIVOS PARA LAS VISTAS:
-------------------------

### Syntaxis:

Las vistas deberían ser lo más simple posible y no tener código acoplado, el objetivo a largo plazo debería ser:

* Siempre usar HTML con PHP incrustado. No usar bloques de PHP.
* Siempre escapar las variables (por defecto las variables estan escapadas en Foil). Leer mas en http://www.foilphp.it/docs/DATA/RETRIEVE-DATA.html 
* Siempre utilizar la sintaxis corta (`<?=`) para el echo de variables. Para el resto de código PHP, usar el tag completo `<?php`.
* Siempre utilizar la sintaxis alternativa para las estructuras de control, que están diseñados para hacer plantillas más legibles (endforeach, endif, etc).
* Nunca usar llaves PHP.
* Tener una sola orden de código por cada tag PHP.
* Evitar el uso de punto y coma. No son necesarios cuando sólo hay una declaración por etiqueta PHP.
* Nunca utilizar el operador `use`. Las plantillas no deberían estar interactuando con las clases de esta manera. Utilizar en su lugar funciones *wrapper*
* Nunca utilizar las estructuras de control *for*, *while* o *switch*. Utilizar su lugar *if* y *foreach. También estructuras del lenguage propias: http://www.foilphp.it/docs/FUNCTIONS/LOOP-HELPERS.html http://www.foilphp.it/docs/FUNCTIONS/ARRAY-HELPERS.html
* Evitar la asignación de variables.


Guía de cambio de controladores:
--------------------------------

1. Usar el objecto Response (de Symfony) como retorno del controlador y no la vista directamente `return new Response(View::render(...));`
    - **NOTAS:**
    - Para las redirecciones usar la classe `RedirectResponse` de Symfony
    - No usar directamente variables `$_GET` o `$_POST`, usar el objecto `Symfony\Component\HttpFoundation\Request` inyectado automáticamente al controlador (recomendable ponerlo al final del método). Para más información ver el [objeto Request](http://symfony.com/doc/current/components/http_foundation/introduction.html)
    - Los nombres de las variables deben concordar con los definidos en las rutas (ver [Rutas](#routes)). En realidad el orden de las variables no es importante.
    - No usar directamente variables `$_SESSION` o `$_COOKIE`, usar la clase `\Goteo\Application\Session` y `\Goteo\Application\Cookie` respectivamente. Mirar los archivos correspondientes para más informacion.

    ```php
    <?php
        // src/extend/goteo/src/Goteo/Controller/DiscoverAddonsController.php

        namespace Goteo\Controller;
        use Symfony\Component\HttpFoundation\Response;
        use Symfony\Component\HttpFoundation\RedirectResponse;
        use Symfony\Component\HttpFoundation\Request;
        use ...
        class DiscoverAddonsController extends \Goteo\Core\Controller {

            public function call () {
                return new RedirectResponse('/discover/calls');
            }

            public function calls () {
                $viewData = array();
                $viewData['title'] = Text::html('discover-calls-header');
                $viewData['list']  = Model\Call::getActive(null, true);
                return new Response(View::render('discover/calls', $viewData));
            }

            public function prueba ($foo, $bar = '', Request $request) {
                // Variable en "POST":
                $post_var = $request->request->get('var');
                // Variable en "GET":
                $get_var = $request->query->get('var');
            }
        }
    ?>
    ```

2. Usar el nuevo wrapper de vistas `Goteo\Application\View` y no el antiguo `Gote\Core\View`. La Función de renderizado es `View::render(...)`

3. Trasladar las vistas de `Resources/templates/legacy/controller/vista.html.php` al nuevo directorio `Resources/templates/default/controller/vista.php`. La sub extension `.html.php` no hace falta.

4. Eliminar includes a prologue.html.php, header.html.php, etc. del principio y final de las vistas. Reestructurar el contenido de la vista en bloques (secciones). Las secciones principales actuales se pueden consultar en `Resources/templates/default/layout.php`. Todas las sub-vistas deben implementar la sección 'content'. El javascript debería idealmente eliminarse pero como paso intermedio puede simplemente añadirse al final de la sección `footer`.
    
**Ejemplo de adición de javascript dentro de una vista:**

`Resources/templates/default/discover/index.php`

```php
<?php $this->layout("layout", ['bodyClass' => 'discover']) ?>

<?php $this->section('content') ?>

    Contenido de la vista:
<?=$this->test?>

<?php $this->replace() ?>
<?php // Se reemplazará la sección 'content' en el template "layout.php" ?>

<?php $this->section('footer') ?>

    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            ...
        });

    </script>

<?php $this->append() ?>
<?php // Se añadirá al final de la sección 'footer' en el template "layout.php" ?>

```

    Mas info http://www.foilphp.it/docs/TEMPLATES/INHERITANCE.html. 

5. Cambiar las referencias al array `$this['variable']` or `$vars['variable']` por referencias al objecto `$this->variable`. Tener en cuenta que si estas variables son arrays o escalares estaran escapadas por defecto.

6. Quitar referencias a clases dentro de las vistas (sin obsesionarse pues puede ser un trabajo largo). Empezamos por sustituir las funciones de `Text::algo(...)` por `$this->text_algo(...)`. Mirar la extensiones `src/Goteo/Foil/Extension/TextUtils.php` y `src/Goteo/Foil/Extension/GoteoCore.php` para las funciones activas (o añadir las que falten).
Tampoco usar directamente variables `$_SESSION` o `$_COOKIE`, ni asignar nada en esas variables dentro de la vista. Usar en su lugar las funciones `$this->get_session('foo')` o `$this->get_cookie('foo')` si es imprescindible
Algunas funciones útiles:
    - `$this->text()` => antiguo Text::get()
    - `$this->text_html()` => antiguo Text::html()
    - `$this->text_widget()` => antiguo Text::widget()
    - `$this->get_session('foo')` => obtiene la variable de session "foo"
    - `$this->get_cookie('foo')` => obtiene la cookie "foo"
    - `$this->is_logged()` => Devuelve "true" si el usuario está loggeado

7. <a name="routes"></a>Poner la entrada del controlador para el procesado del nuevo dispacher en `src/routes.php`. Se usa el elemento [Route de Symfony](http://symfony.com/doc/current/components/routing/introduction.html).
    - **NOTAS:**
    - No añadir `/`  al final de la ruta (hay un controlador al final que se encarga de redirigir peticiones con `/` al final de la ruta)
    - Añadir **Action** al final del método del controlador por claridad (ej: `edit()` pasa a `editAction()`)
    - **UNA VEZ SE HAN PROCESADO TODOS LOS METODOS DEL CONTROLADOR:**
    - Añadir **Controller** al final del nombre del archivo y nombre de clase del controlador. Esto se hace por claridad. **CAMBIAR LOS TESTS ACORDE**
    - Añadir a la variable `$non_legacy_routes` del archivo `src/Goteo/Controller/ErrorController.php` los prefijos de rutas que nunca deben tratarse con el controlador antiguo. **ES DECIR:** Cuando un controlador está completamente actualizado al nuevo sistema de rutas evitar que si hay una ruta no existente se procese segun el controlador antiguo.
    **Por ejemplo:** `\Goteo\Controller\DiscoverController` y `\Goteo\Controller\UserController` ya estan acabados. Hay que evitar que todas las rutas que empiezan por "/discover" se procesen con el controlador antiguo:
    ```php
    <?php
    // src/Goteo/Controller/ErrorController.php
    ...
    $non_legacy_routes = array(
                '/discover',
                '/user',
                ...
                );
    ?>
    ```

```php
<?php
// src/routes.php

$routes = new RouteCollection();

// ...

// Esta ruta tiene parametros, algunos opcionales, va primero
// Esta ruta concuerda con:
//      /discover/results/2
//      /discover/results
// La última ruta no concuerdaria si no fuera opcional el parametro
$routes->add('discover-results', new Route(
    '/discover/results/{category}',
    array('category' => null,
          'name' => null,
          '_controller' => 'Goteo\Controller\DiscoverController::resultsAction',
          'category' => '' //parametro opcional
         )
));
// esta ruta no tiene parametros
$routes->add('discover', new Route(
    '/discover',
    array('_controller' => 'Goteo\Controller\DiscoverController::discoverAction')
));

return $routes;
?>
```


EJEMPLO REAL
------------

Para la vista "results" del controlador "DiscoverController":

Antes `Resources/templates/legacy/discover/results.html.php`

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

Después: `Resources/templates/default/discover/results.php`
(implica la conversion de las sub-vistas: `Resources/templates/default/discover/partials/searcher.php` y `Resources/templates/default/project/widget/project.php`)

```php
<?php
// Resources/templates/default/discover/results.php

$this->layout("layout", [
    'bodyClass' => 'discover', // variable de classe en <body class="...">
    'title' => $this->text('meta-title-discover'), //esto solo si hace falta cambiar el titulo de la pagina por otro
    'meta_description' => $this->text('meta-description-discover') // idem
    ]);

$this->section('content');

?>

        <div id="sub-header">
            <div>
                <h2 class="title"><?=$this->text('discover-results-header')?></h2>
            </div>

        </div>

        <div id="main">

            <?=$this->insert('discover/partials/searcher', ['params' => $this->params])?>

            <div class="widget projects">
                <?php if ($this->results) : ?>
                    <?php foreach ($this->results as $result) : ?>
                        <?=$this->insert('project/widget/project', ['project' => $result])?>
                    <?php endforeach ?>
                <?php else : ?>
                    <?=$this->text('discover-results-empty')?>
                <?php endif ?>
            </div>

        </div>

<?php $this->replace() ?>

```
