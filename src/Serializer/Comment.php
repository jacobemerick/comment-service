<?php

namespace Jacobemerick\CommentService\Serializer;

use DateTime;
use Jacobemerick\CommentService\Serializer\Commenter as CommenterSerializer;

class Comment
{

    public function __construct() {}

    /**
     * @param array $comment
     * @returns array
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
            'url' => $comment['url'], // todo magic replacement
            'reply_to' => $comment['reply_to'],
            'thread' => $comment['thread'],
        ];
    }
}
