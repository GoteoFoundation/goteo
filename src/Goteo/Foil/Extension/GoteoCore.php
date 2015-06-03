<?php

namespace Goteo\Foil\Extension;

use Foil\Contracts\ExtensionInterface;
use Goteo\Application\Message;

class GoteoCore implements ExtensionInterface
{

    private $args;

    public function setup(array $args = [])
    {
        $this->args = $args;
    }

    public function provideFilters()
    {
        return [];
    }

    public function provideFunctions()
    {
        return [
          'get_messages' => [$this, 'messages'],
          'get_errors' => [$this, 'errors'],
        ];
    }

    public function messages($var)
    {
        return Message::getMessages();
    }

    public function errors($var)
    {
        return Message::getErrors();
    }

}
