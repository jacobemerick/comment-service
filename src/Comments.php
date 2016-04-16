<?php

namespace Jacobemerick\CommentService;

use Interop\Container\ContainerInterface as Container;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class Comments
{

    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getComments(Request $request, Response $response)
    {
        echo 'yay get comments called';
    }

    public function createComment(Request $request, Response $response)
    {
        echo 'yay create comments called';
    }
}
