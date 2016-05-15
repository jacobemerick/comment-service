<?php

namespace Jacobemerick\CommentService\Serializer;

class Commenter
{

    public function __construct() {}

    /**
     * @param array $comment
     * @returns array
     */
    public function __invoke(array $commenter)
    {
        return [
            'id' => $commenter['commenter_id'],
            'name' => $commenter['commenter_name'],
            'website' => $commenter['commenter_website'],
        ];
    }
}
