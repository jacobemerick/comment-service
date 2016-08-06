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
        $commenter = $commenterModel->findByFields(
            $body['commenter']['name'],
            $body['commenter']['email'],
            $body['commenter']['website']
        );
        if (!$commenter) {
            $commenterId = $commenterModel->create(
                $body['commenter']['name'],
                $body['commenter']['email'],
                $body['commenter']['website']
            );
            $commenter = $commenterModel->findById($commenterId);
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

        $shouldDisplay = $commenter['is_trusted'];
        if (!empty($body['should_display'])) {
            $shouldDisplay = (int) $body['should_display'];
        }
        $replyTo = 0;
        if (!empty($body['reply_to'])) {
            $replyTo = (int) $body['reply_to'];
        }

        $commentModel = new CommentModel($this->container->get('dbal'));
        $commentId = $commentModel->create(
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

        $commentSerializer = new CommentSerializer;
        $comment = $commentModel->findById($commentId);
        $comment = $commentSerializer($comment);
        $comment = json_encode($comment);

        if ($shouldDisplay) {
            $this->sendNotifications($locationId, $comment);
        }

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
        $comment = $commentModel->findById($req->getAttribute('comment_id'));
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
        $commentModel = new CommentModel($this->container->get('dbal'));

        if ($limit > 0) {
            $comments = $commentModel->getComments($domain, $path, $order, $is_ascending, true, $limit, $offset);
        } else {
            $comments = $commentModel->getComments($domain, $path, $order, $is_ascending);
        }

        $comments = array_map($commentSerializer, $comments);
        $comments = json_encode($comments);

        $res->getBody()->write($comments);
        return $res;
    }

    /**
     * @param integer $locationId
     * @param Comment $comment
     */
    protected function sendNotifications($locationId, Comment $comment)
    {
        // collect people to send notification to
        // filter out current user
        // if no one, eject

        switch ($domain) {
            case 'blog.jacobemerick.com':
                $pageType = 'post';
                $domainTitle = "Jacob Emerick's Blog";
                break;
            case 'waterfallsofthekeweenaw.com':
                $pageType = 'page';
                $domainTitle = 'Waterfalls of the Keweenaw';
                break;
            default:
                $pageType = 'page';
                $domainTitle = $domain;
                break;
        }
        $subject = sprintf('New Comment on %s', $domainTitle);
        $message = sprintf(
            $this->getMessageTemplate(),
            $pageType,
            $domainTitle,
            $commentDate,
            $commenterName,
            $comment,
            $commentUrl,
            $pageType
        );

        $mailer = $this->container->get('mail');
        $mailer->setFrom('email', 'name'); // save in config?
        $mailer->setReplyTo('email', 'name'); // also save in config?
        foreach ($subscriberList as $subscriber) {
            $mailer->addTo($subscriber['email'], $subscriber['name']);
        }
        $mailer->setSubject($subject);
        $mailer->setMessage($message);
        $mailer->send();
    }

    // this should probably just return the message
    protected function getMessageTemplate()
    {
        return <<<MESSAGE
Hello!

There has been a new comment on the %s at %s. You have chosen to be notified of new comments - please reply to this email if you would like to be removed from future notifications.

On %s, %s commented...
%s

Visit %s to view and reply to any comments on this %s. Have a good one!
MESSAGE;
    }
}
