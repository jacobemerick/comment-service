<?php

namespace Jacobemerick\CommentService\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class ParseJsonBody
{

    /**
     * @param Request $req
     * @param Response $res
     * @param callable $next
     * @return Response $res
     */
    public function __invoke(Request $req, Response $res, callable $next)
    {
        if (!$req->getBody()->isReadable()) {
            return;
        }

        $body = (string) $req->getBody();
        if (empty($body)) {
            return $next($req, $res);
        }

        // todo better handling of json parse errors
        $body = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return;
        }

        $req = $req->withParsedBody($body);
        return $next($req, $res);
    }
}
