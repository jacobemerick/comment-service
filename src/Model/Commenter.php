<?php

namespace Jacobemerick\CommentService\Model;

use Aura\Sql\ExtendedPdo;

class Commenter
{

    public function __construct(ExtendedPdo $extendedPdo)
    {
        $this->extendedPdo = $extendedPdo;
    }

    public function findByFields(array $fields)
    {
        $query = "
            SELECT *
            FROM `commenter`
            WHERE `name` = :name AND
                  `email` = :email AND
                  `url` = :url
            LIMIT 1";

        $bindings = [
            'name' => $fields['name'],
            'email' => $fields['email'],
            'url' => $fields['url'],
        ];

        return $this->extendedPdo->fetchOne($query, $bindings);
    }
}
