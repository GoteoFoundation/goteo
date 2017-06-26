<?php
namespace Goteo\Util\Forms;

use Symfony\Component\Templating\TemplateReference;
use Symfony\Component\Templating\TemplateNameParserInterface;

class SimpleTemplateNameParser implements TemplateNameParserInterface
{
    private $root;
    public function __construct($root)
    {
        $this->root = $root;
    }
    public function parse($name)
    {
        if (false !== strpos($name, ':')) {
            $path = str_replace(':', '/', $name);
        } else {
            $path = $this->root . '/' . $name;
        }
        return new TemplateReference($path, 'php');
    }
}
