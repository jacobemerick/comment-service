<?php

namespace Jacobemerick\CommentService\Model;

use Aura\Sql\ExtendedPdo;

class CommentBody
{

    /** @var ExtendedPdo */
    protected $extendedPdo;

    /**
     * @params ExtendedPdo $extendedPdo
     */
    public function __construct(ExtendedPdo $extendedPdo)
    {
        $this->extendedPdo = $extendedPdo;
    }

    /**
     * @params string $body
     * @returns integer
     */
    public function create($body)
    {
        $query = "
            INSERT INTO
                `comment_body` (`body`)
            VALUES
                (:body)";

        $bindings = [
            'body' => $body,
        ];

        $this->extendedPdo->perform($query, $bindings);
        return $this->extendedPdo->lastInsertId();
    }
}
