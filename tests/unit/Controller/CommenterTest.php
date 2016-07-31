<?php

namespace Jacobemerick\CommentService\Controller;

use Interop\Container\ContainerInterface as Container;
use PHPUnit_Framework_TestCase;

class CommenterTest extends PHPUnit_Framework_TestCase
{

    public function testIsInstanceOfCommenter()
    {
        $mockContainer = $this->createMock(Container::class);
        $controller = new Commenter($mockContainer);

        $this->assertInstanceOf(Commenter::class, $controller);
    }

    public function testConstructSetsContainer()
    {
        $mockContainer = $this->createMock(Container::class);
        $controller = new Commenter($mockContainer);

        $this->assertAttributeSame($mockContainer, 'container', $controller);
    }
}
