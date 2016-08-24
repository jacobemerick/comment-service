<?php

namespace Jacobemerick\CommentService\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class Authentication
{

    /** @var string */
    protected $username;

    /** @var string */
    protected $password;

    /**
     * @param string $username
     * @param string $password
     */
    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @param Request $req
     * @param Response $res
     * @param Closure $next
     * @return Response $res
     */
    public function __invoke(Request $req, Response $res, $next)
    {
        if ($req->getUri()->getPath() == '/api-docs') {
            return $next($req, $res);
        }

        $authHeader = $this->getAuthHeader();
        if ($authHeader != current($req->getHeader('Authorization'))) {
            $res = $res->withStatus(403);
            return $res;
        }

        return $next($req, $res);
    }

    /**
     * @return string
     */
    protected function getAuthHeader()
    {
        $authHeader = base64_encode("{$this->username}:{$this->password}");
        $authHeader = "Basic {$authHeader}";
        return $authHeader;
    }
}
