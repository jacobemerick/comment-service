<?php

namespace Jacobemerick\CommentService\ErrorHandler;

use Exception;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class JsonResponse
{

    /**
     * @param Request $req
     * @param Response $res
     * @param Exception $e
     * @returns Response
     */
    public function __invoke(Request $req, Response $res, Exception $e)
    {
        $body = json_encode([
            'error' => $e->getMessage(),
        ]);

        $res->getBody()->write($body);
        return $res;
    }
}
