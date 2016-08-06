<?php

namespace Jacobemerick\CommentService\Helper;

use Aura\Sql\ExtendedPdo;
use DateTime;
use Jacobemerick\Archangel\Archangel;

class NotificationHandler
{

    /** @var ExtendedPdo */
    protected $dbal;

    /** @var Archangel */
    protected $archangel;

    /** @var string */
    protected static $subjectTemplate = 'New comment on %s';

    /** @var string */
    protected static $messageTemplate = <<<MESSAGE
Hello!

There has been a new comment on the %s at %s. You have chosen to be notified of new comments - please reply to this email if you would like to be removed from future notifications.

On %s, %s commented...
%s

Visit %s to view and reply to any comments on this %s. Have a good one!
MESSAGE;

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

        $templateParameters = $this->getTemplateParameters($domain);
        extract($templateParameters);
        $subject = $this->getSubject($domainTitle);
        $message = $this->getMessage($pageType, $domainTitle, DATE, NAME, COMMENT, URL);

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

    /**
     * @param string $domain
     * @returns array
     */
    protected function getTemplateParameters($domain)
    {
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

        return compact($pageType, $domainTitle);
    }

    /**
     * @param string $domainTitle
     * @returns string
     */
    protected function getSubject($domainTitle)
    {
        return sprintf(self::subjectTemplate, $domainTitle);
    }

    /**
     * @param string $pageType
     * @param string $domainTitle
     * @param DateTime $commentDate
     * @param string $commenterName
     * @param string $comment
     * @param string $commentUrl
     * @return string
     */
    protected function getMessage(
        $pageType,
        $domainTitle,
        DateTime $commentDate,
        $commenterName,
        $comment,
        $commentUrl
    ) {
        return sprintf(
            self::$messageTemplate,
            $pageType,
            $domainTitle,
            $commentDate->format('F j, Y g:i a'),
            $commenterName,
            $comment,
            $commentUrl,
            $pageType
        );
    }
}
