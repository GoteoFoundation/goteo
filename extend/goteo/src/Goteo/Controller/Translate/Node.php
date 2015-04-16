<?php

namespace Goteo\Controller\Translate {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Model,
        Goteo\Library\Feed,
        Goteo\Library\Message,
        Goteo\Library\Content,
        Goteo\Library\Page,
        Goteo\Application\Lang;

    class Node
    {

        /**
         * Los parametros para este subcontrolador son ligeramente diferentes
         *
         *
         * @param null $node (comes from $action)
         * @param string $action (comes from $auxAction)
         * @param null $contentTable (comes from $id)
         * @param null $contentId (comes from $contentID, aditional param for just this case)
         */
        public static function process($node = null, $action = 'list', $contentTable = null, $contentId = null, &$errors = array())
        {


            // si llega post, vamos a guardar los cambios
            if ($action == 'edit' && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save'])) {

                switch ($contentTable) {
                    case 'banner':
                        if (Content::save(array(
                            'id' => $contentId,
                            'table' => $contentTable,
                            'title' => $_POST['title'],
                            'description' => $_POST['description'],
                            'lang' => $_POST['lang']
                        ), $errors)
                        ) {
                            Message::Info('El Banner <strong>' . $contentId . '</strong> del nodo <strong>' . $node . '</strong> traducido correctamente al <strong>' . Lang::get($_POST['lang'])->name . '</strong>');
                            throw new Redirection("/translate/node/$node/$contentTable/list");
                        } else {
                            Message::Error('Ha habido algun ERROR al traducir el Banner <strong>' . $contentId . '</strong> del nodo <strong>' . $node . '</strong> al <strong>' . Lang::get($_POST['lang'])->name . '</strong><br />' . implode('<br />', $errors));
                        }
                        break;
                    case 'page':
                        $page = Page::get($contentId, $node);
                        if ($page->update(
                            $contentId, $_POST['lang'], $node,
                            $_POST['name'], $_POST['description'], $_POST['content'],
                            $errors)
                        ) {
                            Message::Info('La página <strong>' . $contentId . '</strong> del nodo <strong>' . $node . '</strong> traducido correctamente al <strong>' . Lang::get($_POST['lang'])->name . '</strong>');
                            throw new Redirection("/translate/node/$node/$contentTable/list");
                        } else {
                            Message::Error('Ha habido algun ERROR al traducir la página <strong>' . $contentId . '</strong> del nodo <strong>' . $node . '</strong> al <strong>' . Lang::get($_POST['lang'])->name . '</strong><br />' . implode('<br />', $errors));
                        }
                        break;
                    case 'post':
                        if (Content::save(array(
                            'id' => $contentId,
                            'table' => $contentTable,
                            'title' => $_POST['title'],
                            'text' => $_POST['text'],
                            'legend' => $_POST['legend'],
                            'lang' => $_POST['lang'],
                            'blog' => $_POST['blog']
                        ), $errors)
                        ) {
                            Message::Info('La entrada <strong>' . $contentId . '</strong> del nodo <strong>' . $node . '</strong> traducido correctamente al <strong>' . Lang::get($_POST['lang'])->name . '</strong>');
                            throw new Redirection("/translate/node/$node/$contentTable/list");
                        } else {
                            Message::Error('Ha habido algun ERROR al traducir la Entrada <strong>' . $contentId . '</strong> del nodo <strong>' . $node . '</strong> al <strong>' . Lang::get($_POST['lang'])->name . '</strong><br />' . implode('<br />', $errors));
                        }
                        break;
                    default:
                        $node = Model\Node::get($node);
                        $node->lang_lang = $_SESSION['translate_lang'];
                        $node->subtitle_lang = $_POST['subtitle'];
                        $node->description_lang = $_POST['description'];
                        if ($node->updateLang($errors)) {
                            Message::Info('La Descripción del nodo <strong>' . $node->id . '</strong> traducido correctamente al <strong>' . Lang::get($_POST['lang'])->name . '</strong>');
                            throw new Redirection("/translate/node/$node->id");
                        } else {
                            Message::Error('Ha habido algun ERROR al traducir la Descripción del nodo <strong>' . $node->id . '</strong> al <strong>' . Lang::get($_POST['lang'])->name . '</strong><br />' . implode('<br />', $errors));
                        }

                }

                return new View(
                    'translate/index.html.php',
                    array(
                        'section' => 'node',
                        'action' => 'edit_' . $contentTable,
                        'option' => $contentTable,
                        'id' => $contentId,
                        'node' => $node
                    )
                );

            } elseif ($action == 'edit') {
                return new View(
                    'translate/index.html.php',
                    array(
                        'section' => 'node',
                        'action' => 'edit_' . $contentTable,
                        'option' => $contentTable,
                        'id' => $contentId,
                        'node' => $node
                    )
                );
            } elseif ($contentTable == 'data') {
                return new View(
                    'translate/index.html.php',
                    array(
                        'section' => 'node',
                        'action' => 'edit_' . $contentTable,
                        'option' => $contentTable,
                        'id' => $node,
                        'node' => $node
                    )
                );
            } else {
                // sino, mostramos la lista
                return new View(
                    'translate/index.html.php',
                    array(
                        'section' => 'node',
                        'action' => 'list_' . $contentTable,
                        'option' => $contentTable,
                        'node' => $node
                    )
                );
            }


        }
    }
}
