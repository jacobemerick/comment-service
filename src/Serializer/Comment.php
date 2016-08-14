<?php

namespace Jacobemerick\CommentService\Serializer;

use DateTime;
use Jacobemerick\CommentService\Serializer\Commenter as CommenterSerializer;

class Comment
{

    /**
     * @param array $comment
     * @return array
     */
    public function __invoke(array $comment)
    {
        $commenterSerializer = new CommenterSerializer();

        return [
            'id' => $comment['id'],
            'commenter' => $commenterSerializer([
                'id' => $comment['commenter_id'],
                'name' => $comment['commenter_name'],
                'website' => $comment['commenter_website'],
            ]),
            'body' => $comment['body'],
            'date' => (new DateTime($comment['date']))->format('c'),
            'url' => $this->prepareUrl($comment['url'], $comment),
            'reply_to' => $comment['reply_to'],
            'thread' => $comment['thread'],
        ];
    }

    protected function prepareUrl($url, array $comment)
    {
        return str_replace('{{id}}', $comment['id'], $url);
    }
}
