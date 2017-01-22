<?php

namespace Jacobemerick\CommentService\Middleware;

use AvalancheDevelopment\Peel\HttpError\Unauthorized;
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
     * @param callable $next
     * @return Response $res
     */
    public function __invoke(Request $req, Response $res, callable $next)
    {
        if ($req->getUri()->getPath() == '/api-docs') {
            return $next($req, $res);
        }

        $basicAuth = array_filter($req->getAttribute('swagger')->getSecurity(), function ($security) {
            return $security['type'] == 'basic';
        });
        if (empty($basicAuth)) {
            return $next($req, $res);
        }

        $authHeader = current($req->getHeader('Authorization'));
        if (!$authHeader) {
            throw new Unauthorized('Basic auth required');
        }
        if ($this->getAuthHeader() !== $authHeader) {
            throw new Unauthorized('Invalid credentials passed in');
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
