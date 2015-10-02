<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller\Translate {

    class Menu
    {

        public static function get()
        {

            $menu = array(
                'tables' => array(
                    'label' => 'Gestión de Textos y Traducciones',
                    'options' => array(
                        'banner' => array(
                            'label' => 'Banners',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'edit' => array('label' => 'Traduciendo Banner', 'item' => true)
                            )
                        ),
                        'bazar' => array(
                            'label' => 'Bazaar',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'edit' => array('label' => 'Traduciendo Bazaar', 'item' => true)
                            )
                        ),
                        'post' => array(
                            'label' => 'Blog',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'edit' => array('label' => 'Traduciendo Entrada', 'item' => true)
                            )
                        ),
                        'texts' => array(
                            'label' => 'Textos interficie',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'edit' => array('label' => 'Traduciendo Texto', 'item' => true)
                            )
                        ),
                        'faq' => array(
                            'label' => 'FAQs',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'edit' => array('label' => 'Traduciendo Pregunta', 'item' => true)
                            )
                        ),
                        'pages' => array(
                            'label' => 'Páginas',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'edit' => array('label' => 'Traduciendo contenido de Página', 'item' => true)
                            )
                        ),
                        'category' => array(
                            'label' => 'Categorias',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'edit' => array('label' => 'Traduciendo Categoría', 'item' => true)
                            )
                        ),
                        'open_tag' => array(
                            'label' => 'Agrupaciones',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'edit' => array('label' => 'Traduciendo Agrupación', 'item' => true)
                            )
                        ),
                        'license' => array(
                            'label' => 'Licencias',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'edit' => array('label' => 'Traduciendo Licencia', 'item' => true)
                            )
                        ),
                        'icon' => array(
                            'label' => 'Tipos de Retorno',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'edit' => array('label' => 'Traduciendo Tipo', 'item' => true)
                            )
                        ),
                        'tag' => array(
                            'label' => 'Tags de blog',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'edit' => array('label' => 'Traduciendo Tag', 'item' => true)
                            )
                        ),
                        'criteria' => array(
                            'label' => 'Criterios de revisión',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'edit' => array('label' => 'Traduciendo Criterio', 'item' => true)
                            )
                        ),
                        'template' => array(
                            'label' => 'Plantillas de email',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'edit' => array('label' => 'Traduciendo Plantilla', 'item' => true)
                            )
                        ),
                        'glossary' => array(
                            'label' => 'Glosario',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'edit' => array('label' => 'Traduciendo Término', 'item' => true)
                            )
                        ),
                        'info' => array(
                            'label' => 'Ideas about',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'edit' => array('label' => 'Traduciendo Idea', 'item' => true)
                            )
                        ),
                        'worthcracy' => array(
                            'label' => 'Meritocracia',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'edit' => array('label' => 'Traduciendo Nivel', 'item' => true)
                            )
                        )
                    )
                ),
                'home' => array(
                    'label' => 'Portada',
                    'options' => array(
                        'news' => array(
                            'label' => 'Micronoticias',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'edit' => array('label' => 'Traduciendo Micronoticia', 'item' => true)
                            )
                        ),
                        'promote' => array(
                            'label' => 'Proyectos destacados',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'edit' => array('label' => 'Traduciendo Destacado', 'item' => true)
                            )
                        ),
                        'stories' => array(
                            'label' => 'Historias exitosas',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'edit' => array('label' => 'Traduciendo Historia', 'item' => true)
                            )
                        ),
                        'patron' => array(
                            'label' => 'Proyectos recomendados',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'edit' => array('label' => 'Traduciendo recomendado', 'item' => true)
                            )
                        )
                    )
                ),
                'node' => array(
                    'label' => 'Nodo',
                    'options' => array(
                        'data' => array(
                            'label' => 'Descripción',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'edit' => array('label' => 'Traduciendo', 'item' => false)
                            )
                        ),
                        'banner' => array(
                            'label' => 'Banners',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'edit' => array('label' => 'Traduciendo banner', 'item' => true)
                            )
                        ),
                        'post' => array(
                            'label' => 'Blog',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'edit' => array('label' => 'Traduciendo entrada', 'item' => true)
                            )
                        ),
                        'page' => array(
                            'label' => 'Páginas institucionales',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'edit' => array('label' => 'Traduciendo página', 'item' => true)
                            )
                        )
                    )
                )
            );

            return $menu;
        }
    }
}