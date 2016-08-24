<?php

namespace Jacobemerick\CommentService\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class JsonHeader
{

    /**
     * @param Request $req
     * @param Response $res
     * @param Closure $next
     * @return Response $res
     */
    public function __invoke(Request $req, Response $res, $next)
    {
        $res = $next($req, $res);
        $res = $res->withAddedHeader('Content-Type', 'application/json');
        return $res; 
    }
}
