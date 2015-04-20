Migracion de plantillas
=======================

Se usa Plates
http://platesphp.com/

Antiguo sistema

\Goteo\Core\View::get('plantilla.php', array( 'obj' => ... ));


Nuevo

\Goteo\Application\View::render('plantilla', array( 'obj' => ... ));


Diferencias en las plantillas
-----------------------------
plantilla.php

Antes:

Las variables están dentro del array **$this**

```php
<?php

$obj = $this['obj'];

$obj->doSomething();

$this['obj']->doMore();

?>
```

Despues:

Las variables están instanciadas por defecto y también dentro del array **$this->vars**

```php
<?php

$obj->doSomething();
$this->vars['obj']->doMore();

?>
```

Nuevas funcionalidades
----------------------

Plates soporta herencia, funciones y reasignacion de variables

Funcionamento basico

layout.php

```html
<html>
<head>
    <title><?=$this->e($title)?></title>
</head>
<body>

<?=$this->section('content')?>

</body>
</html>
 ```

reescritura del titulo:

profile.php

```html
<?php $this->layout('template', ['title' => 'User Profile']) ?>

<h1>User Profile</h1>
<p>Hello, <?=$this->e($name)?></p>
```
