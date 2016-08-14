<?php

namespace Jacobemerick\CommentService\Model;

use Aura\Sql\ExtendedPdo;

class CommentRequest
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
     * @param string $ipAddress
     * @param string $userAgent
     * @param string $referrer
     * @return integer
     */
    public function create($ipAddress, $userAgent, $referrer)
    {
        $query = "
            INSERT INTO
                `comment_request` (`ip_address`, `user_agent`, `referrer`)
            VALUES
                (:ip_address, :user_agent, :referrer)";

        $bindings = [
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'referrer' => $referrer,
        ];

        $this->extendedPdo->perform($query, $bindings);
        return $this->extendedPdo->lastInsertId();
    }

    /**
     * @param string $ipAddress
     * @param string $userAgent
     * @param string $referrer
     * @return integer
     */
    public function findByFields($ipAddress, $userAgent, $referrer)
    {
        $query = "
            SELECT `id`
            FROM `comment_request`
            WHERE `ip_address` = :ip_address AND
                  `user_agent` = :user_agent AND
                  `referrer` = :referrer
            LIMIT 1";

        $bindings = [
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'referrer' => $referrer,
        ];

        return $this->extendedPdo->fetchValue($query, $bindings);
    }
}
