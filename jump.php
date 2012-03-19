<?php
/**
 *  Parametros GET
 *
 *  // Yendo a otro nodo
 *  action = go
 *  - url = destino
 *
 *
 *  // Viniendo de otro nodo
 *  action = come
 *  - sesid = session_id
 *  - path = /project/... + (?)asdf=asdf + (#)asdf
 *              path             query        fragment
 */

switch ($_GET['action']) {
    case 'go': // Vamos a otro nodo:
        // nombrar e iniciar sesion
        session_name('goteo');
        session_start();
        // obtener session id
        $sesid = session_id();
        // descomponer el destino con parse_url()
        $parts = parse_url($_GET['url']);
        // recomponer y montar el destino
        $host = $parts['host'];
        $path = isset($parts['path']) ? $parts['path'] : '';
        $query = isset($parts['query']) ? '?'.$parts['query'] : '';
        $fragment = isset($parts['fragment']) ? '#'.$parts['fragment'] : '';
        $dest = "$path$query$fragment";
        // montar la url del script de llegada:   http://nodo.goteo.org/jumpo.php?action=come&sesid=...&path=...
        $url = "http://{$host}/jump.php?action=come&sesid={$sesid}&path=".urlencode($dest);
        // Saltar con header('Location: ');
        header("Location: $url");
        die;
        break;

    case 'come': // Venimos de otro nodo:
        // verificar el sesid
        if (!isset($_GET['sesid'])) {
            header('Location: /');
            die;
        }
        // nombrar sesion
        session_name('goteo');
        // aplicar con session_id($sesid)
        session_id($_GET['sesid']);
        // iniciar sesion
        session_start();
        // saltar al path con header('Location ');
        $path = empty($_GET['path']) ? '/' : $_GET['path'];
        header("Location: $path");
        die;

        break;

    default:
        die('Unknown action');
}



