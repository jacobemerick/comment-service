<?php

namespace Jacobemerick\CommentService\Controller;

use Interop\Container\ContainerInterface as Container;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class Comments
{

    /** @var Container */
    protected $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param Request $request
     * @param Response $response
     */
    public function getComments(Request $request, Response $response)
    {
        echo 'yay get comments called';
    }

    /**
     * @param Request $request
     * @param Response $response
     */
    public function createComment(Request $request, Response $response)
    {
        // todo something something validation
        // todo something something data layers

        $query = "
            SELECT `id`
            FROM `commenter`
            WHERE `name` = :name AND
                  `email` = :email AND
                  `url` = :url
            LIMIT 1";
        $params = [
            'name' => $request->getParsedBody()['commenter']['name'],
            'email' => $request->getParsedBody()['commenter']['email'],
            'url' => $request->getParsedBody()['commenter']['url'],
        ];
        var_dump($params);
        return;
        $commenter = $this->container->db->selectOne($query, $params);
    }
}
