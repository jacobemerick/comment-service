<?php

namespace Jacobemerick\CommentService\Model;

use Aura\Sql\ExtendedPdo;

class CommentDomain
{

    /** @var ExtendedPdo */
    protected $extendedPdo;

    /**
     * @param ExtendedPdo $extendedPdo
     */
    public function __construct(ExtendedPdo $extendedPdo)
    {
        $this->extendedPdo = $extendedPdo;
    }

    /**
     * @param string $domain
     * @return integer
     */
    public function create($domain)
    {
        $query = "
            INSERT INTO
                `comment_domain` (`domain`)
            VALUES
                (:domain)";

        $bindings = [
            'domain' => $domain,
        ];

        $this->extendedPdo->perform($query, $bindings);
        return $this->extendedPdo->lastInsertId();
    }

    /**
     * @param string $domain
     * @return integer
     */
    public function findByFields($domain)
    {
        $query = "
            SELECT `id`
            FROM `comment_domain`
            WHERE `domain` = :domain
            LIMIT 1";

        $bindings = [
            'domain' => $domain,
        ];

        return $this->extendedPdo->fetchValue($query, $bindings);
    }
}
