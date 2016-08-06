<?php

namespace Jacobemerick\CommentService\Helper;

use Aura\Sql\ExtendedPdo;
use Jacobemerick\Archangel\Archangel;

class NotificationHandler
{

    /** @var ExtendedPdo */
    protected $dbal;

    /** @var Archangel */
    protected $archangel;

    /**
     * @param ExtendedPdo $dbal
     * @param Archangel $mailer
     */
    public function __construct(ExtendedPdo $dbal, Archangel $mailer)
    {
        $this->dbal = $dbal;
        $this->mailer = $mailer;
    }

    /**
     * @param integer $locationId
     * @param Comment $comment
     */
    protected function __invoke($locationId, Comment $comment)
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

        $this->mailer->setFrom('email', 'name'); // save in config?
        $this->mailer->setReplyTo('email', 'name'); // also save in config?
        foreach ($subscriberList as $subscriber) {
            // should this send separate emails or all in one?
            $this->mailer->addTo($subscriber['email'], $subscriber['name']);
        }
        $this->mailer->setSubject($subject);
        $this->mailer->setMessage($message);
        $this->mailer->send();
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
