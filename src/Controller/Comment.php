<?php

namespace Jacobemerick\CommentService\Controller;

use Interop\Container\ContainerInterface as Container;
use Jacobemerick\CommentService\Model\Comment as CommentModel;
use Jacobemerick\CommentService\Model\CommentBody as CommentBodyModel;
use Jacobemerick\CommentService\Model\CommentDomain as CommentDomainModel;
use Jacobemerick\CommentService\Model\CommentLocation as CommentLocationModel;
use Jacobemerick\CommentService\Model\CommentPath as CommentPathModel;
use Jacobemerick\CommentService\Model\CommentRequest as CommentRequestModel;
use Jacobemerick\CommentService\Model\CommentThread as CommentThreadModel;
use Jacobemerick\CommentService\Model\Commenter as CommenterModel;
use Jacobemerick\CommentService\Serializer\Comment as CommentSerializer;
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
     * @param Request $req
     * @param Response $res
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

        $domainModel = new CommentDomainModel($this->container->get('dbal'));
        $domainId = $domainModel->findByFields($body['domain']);
        if (!$domainId) {
            $domainId = $domainModel->create($body['domain']);
        }

        $pathModel = new CommentPathModel($this->container->get('dbal'));
        $pathId = $pathModel->findByFields($body['path']);
        if (!$pathId) {
            $pathId = $pathModel->create($body['path']);
        }

        $threadModel = new CommentThreadModel($this->container->get('dbal'));
        $threadId = $threadModel->findByFields($body['thread']);
        if (!$threadId) {
            $threadId = $threadModel->create($body['thread']);
        }

        $locationModel = new CommentLocationModel($this->container->get('dbal'));
        $locationId = $locationModel->findByFields(
            $domainId,
            $pathId,
            $threadId
        );
        if (!$locationId) {
            $locationId = $locationModel->create(
                $domainId,
                $pathId,
                $threadId
            );
        }

        $commentRequestModel = new CommentRequestModel($this->container->get('dbal'));
        $commentRequestId = $commentRequestModel->findByFields(
            $body['ip_address'],
            $body['user_agent'],
            $body['referrer']
        );
        if (!$commentRequestId) {
            $commentRequestId = $commentRequestModel->create(
                $body['ip_address'],
                $body['user_agent'],
                $body['referrer']
            );
        }

        $commentModel = new CommentModel($this->container->get('dbal'));
        $commentId = $commentModel->create(
            $commenterId,
            $bodyId,
            $locationId,
            $commentRequestId,
            (int) $body['should_notify'],
            (int) $body['should_display'],
            time()
        );

        $commentSerializer = new CommentSerializer;
        $comment = $commentModel->findById($commentId);
        $comment = $commentSerializer($comment);
        $comment = json_encode($comment);

        $res->getBody()->write($comment);
        return $res;
    }

    /**
     * @param Request $req
     * @param Response $res
     */
    public function getComment(Request $req, Response $res)
    {
        $commentSerializer = new CommentSerializer;
        $commentModel = new CommentModel($this->container->get('dbal'));
        $comment = $commentModel->findById(1);
        $comment = $commentSerializer($comment);
        $comment = json_encode($comment);

        $res->getBody()->write($comment);
        return $res;
    }

    /**
     * @param Request $req
     * @param Response $res
     */
    public function getComments(Request $req, Response $res)
    {
        $commentSerializer = new CommentSerializer;
        $commentModel = new CommentModel($this->container->get('dbal'));
        $comments = $commentModel->getComments();
        $comments = array_map($commentSerializer, $comments);
        $comments = json_encode($comments);

        $res->getBody()->write($comments);
        return $res;
    }
}
