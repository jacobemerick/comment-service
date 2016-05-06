<?php

namespace Jacobemerick\CommentService\Controller;

use Interop\Container\ContainerInterface as Container;
use Jacobemerick\CommentService\Model\Comment as CommentModel;
use Jacobemerick\CommentService\Model\Commenter as CommenterModel;
use Jacobemerick\CommentService\Model\CommentBody as CommentBodyModel;
use Jacobemerick\CommentService\Model\CommentLocation as CommentLocationModel;
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
    public function createComment(Request $req, Response $res)
    {
        // todo something something validation

        $body = $req->getParsedBody();

        // todo option to pass in by commenter id
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

        $bodyModel = new CommentBodyModel($this->container->get('dbal'));
        $bodyId = $bodyModel->create($body['body']);

        $locationModel = new CommentLocationModel($this->container->get('dbal'));
        $locationId = $locationModel->findByFields(
            $body['domain'],
            $body['path'],
            $body['thread']
        );
        if (!$locationId) {
            $locationId = $locationModel->create(
                $body['domain'],
                $body['path'],
                $body['thread']
            );
        }
            
        var_dump($commenterId);
        return $res;
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
