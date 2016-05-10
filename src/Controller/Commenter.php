<?php

namespace Jacobemerick\CommentService\Controller;

use Interop\Container\ContainerInterface as Container;
use Jacobemerick\CommentService\Model\Commenter as CommenterModel;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class Comment
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
    public function createCommenter(Request $req, Response $res)
    {
        // todo something something validation

        $body = $req->getParsedBody();

        $commenterModel = new CommenterModel($this->container->get('dbal'));
        $commenterId = $commenterModel->findByFields(
            $body['commenter']['name'],
            $body['commenter']['email'],
            $body['commenter']['website']
        );
        if (!$commenterId) {
            $commenterId = $commenterModel->create(
                $body['commenter']['name'],
                $body['commenter']['email'],
                $body['commenter']['website']
            );
        }
    }
}
