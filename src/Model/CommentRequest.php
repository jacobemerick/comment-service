<?php

namespace Jacobemerick\CommentService\Model;

use Aura\Sql\ExtendedPdo;

class CommentRequest
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
     * @param string $ip_address
     * @param string $user_agent
     * @param string $referrer
     * @returns integer
     */
    public function create($ip_address, $user_agent, $referrer)
    {
        $query = "
            INSERT INTO
                `comment_request` (`ip_address`, `user_agent`, `referrer`)
            VALUES
                (:ip_address, :user_agent, :referrer)";

        $bindings = [
            'ip_address' => $ip_address,
            'user_agent' => $user_agent,
            'referrer' => $referrer,
        ];

        $this->extendedPdo->perform($query, $bindings);
        return $this->extendedPdo->lastInsertId();
    }

    /**
     * @param string $ip_address
     * @param string $user_agent
     * @param string $referrer
     * @returns integer
     */
    public function findByFields($ip_address, $user_agent, $referrer)
    {
        $query = "
            SELECT `id`
            FROM `comment_request`
            WHERE `ip_address` = :ip_address AND
                  `user_agent` = :user_agent AND
                  `referrer` = :referrer
            LIMIT 1";

        $bindings = [
            'ip_address' => $ip_address,
            'user_agent' => $user_agent,
            'referrer' => $referrer,
        ];

        return $this->extendedPdo->fetchValue($query, $bindings);
    }
}
