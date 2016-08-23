<?php

namespace Jacobemerick\CommentService\Helper;

use DateTime;
use Jacobemerick\Archangel\Archangel;
use Jacobemerick\CommentService\Model\Commenter;

class NotificationHandler
{

    /** @var Archangel */
    protected $archangel;

    /** @var Commenter */
    protected $commenterModel;

    /** @var array */
    protected static $from = [
        'name' => 'Comment Notifier 3000',
        'email' => 'comments@jacobemerick.com',
    ];

    /** @var array */
    protected static $replyTo = [
        'name' => 'Jacob Emerick',
        'email' => 'jpemeric@gmail.com',
    ];

    /** @var string */
    protected static $subjectTemplate = 'New comment on %s';

    /** @var string */
    protected static $messageTemplate = <<<MESSAGE
Hello!

There has been a new comment on the %s at %s. You have chosen to be notified of new comments - please reply to this email if you would like to be removed from future notifications.

On %s, %s commented...
%s

Visit %s to view and reply. Have a good one!
MESSAGE;

    /**
     * @param Archangel $mailer
     * @param Commenter $commenterModel
     */
    public function __construct(Archangel $mailer, Commenter $commenterModel)
    {
        $this->mailer = $mailer;
        $this->commenterModel = $commenterModel;
    }

    /**
     * @param integer $locationId
     * @param array $comment
     * @return null
     */
    public function __invoke($locationId, array $comment)
    {
        $recipientList = $this->commenterModel
            ->getNotificationRecipients($locationId);

        $recipientList = array_filter($recipientList, function ($recipient) use ($comment) {
            return $recipient['id'] !== $comment['commenter_id'];
        });

        if (empty($recipientList)) {
            return;
        }

        $templateParameters = $this->getTemplateParameters($comment['domain']);
        $subject = $this->getSubject($templateParameters['domainTitle']);
        $message = $this->getMessage(
            $templateParameters['pageType'],
            $templateParameters['domainTitle'],
            new DateTime($comment['date']),
            $comment['commenter_name'],
            $comment['body'],
            str_replace('{{id}}', $comment['id'], $comment['url'])
        );

        $this->mailer->setFrom(self::$from['email'], self::$from['name'])
            ->setReplyTo(self::$replyTo['email'], self::$replyTo['name'])
            ->setSubject($subject)
            ->setPlainMessage($message);

        foreach ($recipientList as $recipient) {
            $singleMailer = clone $this->mailer;
            $singleMailer->addTo($recipient['email'], $recipient['name'])->send();
            unset($singleMailer);
        }
    }

    /**
     * @param string $domain
     * @return array
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

        return compact('pageType', 'domainTitle');
    }

    /**
     * @param string $domainTitle
     * @return string
     */
    protected function getSubject($domainTitle)
    {
        return sprintf(self::$subjectTemplate, $domainTitle);
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
            $commentUrl
        );
    }
}
