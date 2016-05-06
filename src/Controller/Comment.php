<?php

namespace Jacobemerick\CommentService\Controller;

use Interop\Container\ContainerInterface as Container;
use Jacobemerick\CommentService\Model\Comment as CommentModel;
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

        $commenterModel = new CommenterModel($this->container->get('dbal'));
        $commenterId = $commenterModel->findByFields(
            $request->getParsedBody()['commenter']['name'],
            $request->getParsedBody()['commenter']['email'],
            $request->getParsedBody()['commenter']['website']
        );
        if (!$commenterId) {
            $commenterId = $commenterModel->create(
                $request->getParsedBody()['commenter']['name'],
                $request->getParsedBody()['commenter']['email'],
                $request->getParsedBody()['commenter']['website']
            );
        }
        var_dump($commenterId);
        return $response;
    }

    /**
     * @param Request $request
     * @param Response $response
     */
    public function getComment(Request $request, Response $response)
    {
        $commentModel = new CommentModel($this->container->get('dbal'));
        $comment = $commentModel->findById(1);
        var_dump($comment);
        return $response;
    }
}
