<?php

namespace Jacobemerick\CommentService;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class Comments
{

    public function __construct() {}

    public function getComments(Request $request, Response $response)
    {
        echo 'yay get comments called';
    }

    public function createComment(Request $request, Response $response)
    {
        echo 'yay create comments called';
    }
}
