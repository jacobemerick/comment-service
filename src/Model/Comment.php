<?php

namespace Jacobemerick\CommentService\Model;

use Aura\Sql\ExtendedPdo;

class Comment
{

    public function __construct(ExtendedPdo $extendedPdo)
    {
        $this->extendedPdo = $extendedPdo;
    }

    public function findById($id)
    {
        $query = "
            SELECT *
            FROM `comment`
            WHERE `id` = :id
            LIMIT 1";

        $bindings = [
            'id' => $id,
        ];

        return $this->extendedPdo->fetchOne($query, $bindings);
    }
}
