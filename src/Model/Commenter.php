<?php

namespace Jacobemerick\CommentService\Model;

use Aura\Sql\ExtendedPdo;

class Commenter
{

    /** @var ExtendedPdo */
    protected $extendedPdo;

    public function __construct(ExtendedPdo $extendedPdo)
    {
        $this->extendedPdo = $extendedPdo;
    }

    public function create($name, $email, $website)
    {
        $query = "
            INSERT INTO
                `commenter` (`name`, `email`, `url`)
            VALUES
                (:name, :email, :website)";

        $bindings = [
            'name' => $name,
            'email' => $email,
            'website' => $website,
        ];

        $this->extendedPdo->perform($query, $bindings);
        return $this->extendedPdo->lastInsertId();
    }

    public function findByFields($name, $email, $website)
    {
        $query = "
            SELECT `id`
            FROM `commenter`
            WHERE `name` = :name AND
                  `email` = :email AND
                  `url` = :website
            LIMIT 1";

        $bindings = [
            'name' => $name,
            'email' => $email,
            'website' => $website,
        ];

        return $this->extendedPdo->fetchValue($query, $bindings);
    }
}
