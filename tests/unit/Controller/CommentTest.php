<?php

namespace Jacobemerick\CommentService\Controller;

use Interop\Container\ContainerInterface as Container;
use PHPUnit_Framework_TestCase;

class CommentTest extends PHPUnit_Framework_TestCase
{

    public function testIsInstanceOfComment()
    {
        $mockContainer = $this->createMock(Container::class);
        $controller = new Comment($mockContainer);

        $this->assertInstanceOf(Comment::class, $controller);
    }

    public function testConstructSetsContainer()
    {
        $mockContainer = $this->createMock(Container::class);
        $controller = new Comment($mockContainer);

        $this->assertAttributeSame($mockContainer, 'container', $controller);
    }
}
