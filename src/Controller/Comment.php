<?php

namespace Jacobemerick\CommentService\Controller;

use Interop\Container\ContainerInterface as Container;
use Jacobemerick\CommentService\Helper\NotificationHandler;
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
        $commenter = $this->container
            ->get('commenterModel')
            ->findByFields(
                $body['commenter']['name'],
                $body['commenter']['email'],
                $body['commenter']['website']
            );
        if (!$commenter) {
            $commenterId = $this->container
                ->get('commenterModel')
                ->create(
                    $body['commenter']['name'],
                    $body['commenter']['email'],
                    $body['commenter']['website']
                );
            $commenter = $this->container
                ->get('commenterModel')
                ->findById($commenterId);
        }

        $bodyId = $this->container
            ->get('commentBodyModel')
            ->create($body['body']);

        $domainId = $this->container
            ->get('commentDomainModel')
            ->findByFields($body['domain']);
        if (!$domainId) {
            $domainId = $this->container
                ->get('commentDomainModel')
                ->create($body['domain']);
        }

        $pathId = $this->container
            ->get('commentPathModel')
            ->findByFields($body['path']);
        if (!$pathId) {
            $pathId = $this->container
                ->get('commentPathModel')
                ->create($body['path']);
        }

        $threadId = $this->container
            ->get('commentThreadModel')
            ->findByFields($body['thread']);
        if (!$threadId) {
            $threadId = $this->container
                ->get('commentThreadModel')
                ->create($body['thread']);
        }

        $locationId = $this->container
            ->get('commentLocationModel')
            ->findByFields(
                $domainId,
                $pathId,
                $threadId
            );
        if (!$locationId) {
            $locationId = $this->container
                ->get('commentLocationModel')
                ->create(
                    $domainId,
                    $pathId,
                    $threadId
                );
        }

        $commentRequestId = $this->container
            ->get('commentRequestModel')
            ->findByFields(
                $body['ip_address'],
                $body['user_agent'],
                $body['referrer']
            );
        if (!$commentRequestId) {
            $commentRequestId = $this->container
                ->get('commentRequestModel')
                ->create(
                    $body['ip_address'],
                    $body['user_agent'],
                    $body['referrer']
                );
        }

        $shouldDisplay = $commenter['is_trusted'];
        if (!empty($body['should_display'])) {
            $shouldDisplay = (int) $body['should_display'];
        }
        $replyTo = 0;
        if (!empty($body['reply_to'])) {
            $replyTo = (int) $body['reply_to'];
        }

        $commentId = $this->container
            ->get('commentModel')
            ->create(
                $commenter['id'],
                $bodyId,
                $locationId,
                $replyTo,
                $commentRequestId,
                $body['url'],
                (int) $body['should_notify'],
                $shouldDisplay,
                time()
            );
        $comment = $this->container
            ->get('commentModel')
            ->findById($commentId);

        $commentSerializer = new CommentSerializer;
        if ($shouldDisplay) {
            $notificationHandler = new NotificationHandler(
                $this->container->get('dbal'),
                $this->container->get('mail')
            );
            $notificationHandler($locationId, $comment);
        }

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
        $comment = $this->container
            ->get('commentModel')
            ->findById($req->getAttribute('comment_id'));

        $commentSerializer = new CommentSerializer;
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
        $limit = 0;
        $offset = 0;
        $domain = '';
        $path = '';
        $order = 'date';
        $is_ascending = true;

        $query = $req->getQueryParams();
        if (array_key_exists('per_page', $query)) {
            $limit = $query['per_page'];
        }
        if (array_key_exists('page', $query)) {
            $offset = ($query['page'] - 1) * $query['per_page'];
        }
        if (array_key_exists('domain', $query)) {
            $domain = $query['domain'];
        }
        if (array_key_exists('path', $query)) {
            $path = $query['path'];
        }
        if (array_key_exists('order', $query)) {
            $order = $query['order'];
            if (substr($order, 0, 1) == '-') {
                $is_ascending = false;
                $order = substr($order, 1);
            }
        }

        $commentSerializer = new CommentSerializer;

        if ($limit > 0) {
            $comments = $this->container
                ->get('commentModel')
                ->getComments(
                    $domain,
                    $path,
                    $order,
                    $is_ascending,
                    true,
                    $limit,
                    $offset
                );
        } else {
            $comments = $this->container
                ->get('commentModel')
                ->getComments(
                    $domain,
                    $path,
                    $order,
                    $is_ascending
                );
        }

        $comments = array_map($commentSerializer, $comments);
        $comments = json_encode($comments);

        $res->getBody()->write($comments);
        return $res;
    }
}
