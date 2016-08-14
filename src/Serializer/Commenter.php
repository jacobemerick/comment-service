<?php

namespace Jacobemerick\CommentService\Serializer;

class Commenter
{

    /**
     * @param array $comment
     * @returns array
     */
    public function __invoke(array $commenter)
    {
        return [
            'id' => $commenter['id'],
            'name' => $commenter['name'],
            'website' => $commenter['website'],
        ];
    }
}
