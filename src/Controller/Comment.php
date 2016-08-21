<?php

namespace Jacobemerick\CommentService\Controller;

use Interop\Container\ContainerInterface as Container;
use Jacobemerick\CommentService\Helper\NotificationHandler;
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
        if (array_key_exists('should_display', $body)) {
            $shouldDisplay = (int) $body['should_display'];
        }
        $replyTo = 0;
        if (array_key_exists('reply_to', $body)) {
            $replyTo = (int) $body['reply_to'];
        }

        $dateTime = $this->container
            ->get('datetime');

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
                $dateTime
            );
        $comment = $this->container
            ->get('commentModel')
            ->findById($commentId);

        if ($shouldDisplay) {
            $this->container
                ->get('notificationHandler')
                ->__invoke($locationId, $comment);
        }

        $comment = $this->container
            ->get('commentSerializer')
            ->__invoke($comment);

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

        $comment = $this->container
            ->get('commentSerializer')
            ->__invoke($comment);

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
        $isAscending = true;

        $query = $req->getQueryParams();
        if (array_key_exists('per_page', $query)) {
            $limit = $query['per_page'];
        }
        if (array_key_exists('page', $query)) {
            $offset = ($query['page'] - 1) * $limit;
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
                $isAscending = false;
                $order = substr($order, 1);
            }
        }

        if ($limit > 0) {
            $comments = $this->container
                ->get('commentModel')
                ->getComments(
                    $domain,
                    $path,
                    $order,
                    $isAscending,
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
                    $isAscending
                );
        }

        $comments = array_map(
            $this->container->get('commentSerializer'),
            $comments
        );

        $comments = json_encode($comments);
        $res->getBody()->write($comments);
        return $res;
    }
}
