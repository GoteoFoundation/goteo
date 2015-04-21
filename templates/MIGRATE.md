Migración de plantillas
=======================

Se usa PHP FOIL
http://foilphp.it/

Antiguo sistema

`\Goteo\Core\View::get('plantilla.php', array( 'obj' => ... ));`

Nuevo

`\Goteo\Application\View::render('plantilla', [ 'obj' => ... ]);`


Diferencias en las plantillas
-----------------------------
`plantilla.php`

Antes:

Las variables están dentro del array **$this** o $vars[]

```php
<?php

//Objecto
$obj = $this['obj'];

$obj->doSomething();

echo htmlspecialchars($this['obj']->doMore()); // escapado con htmlspecialchars

//Escalar
echo htmlspecialchars($this['variable']); //escapada
echo $this['variable']; //sin escapar

?>
```

Después:

Las variables están instanciadas por defecto en el objecto $this

```php
<?php
//objecto
$obj->doSomething();

echo $this->e($this->raw('obj')->doMore()); // escapado con $this->e()

//escalar
echo $this->variable; // escapada por defecto
echo $this->raw('variable'); // sin escapar

?>
```

Nuevas funcionalidades
----------------------

FOIL soporta herencia, funciones y reasignación de variables

Funcionamiento básico

`layout.php`

```php
<html>
<head>
    <title><?=$this->title?></title>
</head>
<body>

<?php $this->section('content') ?>
<?php $this->stop() ?>

</body>
</html>
```

reescritura del titulo y sustitución de la sección 'content':

`profile.php`

```php
<?php $this->layout('template', ['title' => 'User Profile']) ?>

<?php $this->section('content') ?>
<h1>User Profile</h1>
<p>Hello, <?=$this->name?></p>
<?php $this->stop() ?>
```

Mas información en:
http://www.foilphp.it/docs/TEMPLATES/INHERITANCE.html
http://www.foilphp.it/docs/DATA/PASS-DATA.html


Migración de las vistas actuales:
---------------------------------

La nueva estructura de directorios deberia marcar de manera clara el próposito de cada vista:

**Primer nivel**

Multiples directorios que permiten sobreescritura de vistas segun las extensiones cargadas

```text
templates               <- Carpeta de vistas generales
extend/goteo/templates  <- Carpeta de vistas privadas de goteo 
                           (si existe una vista aquí sobreescribira la general)
```

Las vistas del primer nivel no se llaman directamente desde los controladores,
se llaman con la función `$this->layout('...')` dentro de otras vistas

**Segundo nivel**

Multiples directorios que permiten sobreescritura de vistas segun el tema usado
(por ejemplo: nodos, convocatorias, widgets, etc)

```text
templates/default      <- primer nivel, indica el tema a usar
templates/node            Un tema son paginas dentro de los tags <html></html>
templates/...
```

Ejemplo de llamada en un controlador:

```php
<?php
namespace Goteo\Controller;

use Symfony\Component\HttpFoundation\Response;

class Discover extends \Goteo\Core\Controller {
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
templates/default/errors/   <- tercer nivel estan las secciones de cada tema
templates/default/home/        Por ej. vistas principales de los controladores
templates/default/discover/            que usan herencia
```

Ejemplo de uso en una vista (`templates/default/discover/index.php`):

```php
<?php $this->layout("layout", ['bodyClass' => 'discover']) ?>

<?php $this->section('content') ?>

Contenido de la vista:
<?=$this->test?>

<?php $this->replace() ?>
```

También dentro de una carpeta `partials` (para especificar que no son controladores que heredan) se pueden poner partes de vistas que se incluyen dentro de otras:

```text
templates/default/partials/  <- O bien bloques parciales que se incluyen en la
                                vista padre
```

Ejemplo de uso de inclusión de un parcial (`templates/default/layout.php`):

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

2. Usar el nuevo wrapper de vistas `Goteo\Application\View` y no el antiguo `Gote\Core\View`. La Función de renderizado es `View::render(...)`

3. Trasladar las vistas de `app/view/controller/vista.html.php` al nuevo directorio `templates/default/controller/vista.php`. La sub extension `.html.php` no hace falta.

4. Eliminar includes a prologue.html.php, header.html.php, etc. del principio y final de las vistas. Reestructurar el contenido de la vista en bloques (secciones). Las secciones principales actuales se pueden consultar en `templates/default/layout.php`. Todas las sub-vistas deben implementar la sección 'content'. El javascript debería idealmente eliminarse pero como paso intermedio puede simplemente añadirse al final de la sección `footer`.
    
    **Ejemplo de adición de javascript dentro de una vista:**

    `templates/default/discover/index.php`

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

6. Quitar referencias a clases dentro de las vistas (sin obsesionarse pues puede ser un trabajo largo). Empezamos por sustituir las funciones de `Text::algo(...)` por `$this->text_algo(...)`. Mirar la extensión `src/Goteo/Foil/Extension/Textutils.php` para las funciones activas (o añadir las que falten)

7. Poner la entrada del controlador para el procesado del nuevo dispacher en `src/app.php`. Se usa el elemento Route de Symfony:

    ```php
    <?php
    $routes = new RouteCollection();

    // ...

    $routes->add('discover', new Route(
        '/discover',
        array('_controller' => 'Goteo\Controller\Discover')
    ));

    return $routes;
    ?>
    ```


EJEMPLO REAL
------------

Para la vista "results" del controlador "Discover":

Antes `app/view/discover/results.html.php`

```php
<?php

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

Después: `templates/default/discover/results.php`
(implica la conversion de las sub-vistas: `templates/default/discover/partials/searcher.php` y `templates/default/project/widget/project.php`)

```php
<?php

$this->layout("layout", [
    'bodyClass' => 'discover',
    'meta_description' => $this->text('meta-description-discover'),
    'image' => $og_image
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
