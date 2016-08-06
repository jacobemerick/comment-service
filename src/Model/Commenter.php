<?php

namespace Jacobemerick\CommentService\Model;

use Aura\Sql\ExtendedPdo;

class Commenter
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
     * @param string $name
     * @param string $email
     * @param string $website
     * @return integer
     */
    public function create($name, $email, $website)
    {
        $query = "
            INSERT INTO
                `commenter` (`name`, `email`, `website`)
            VALUES
                (:name, :email, :website)";

        $bindings = [
            'name' => $name,
            'email' => $email,
            'website' => $website,
        ];

        if (!$this->extendedPdo->perform($query, $bindings)) {
            return false;
        }

        return $this->extendedPdo->lastInsertId();
    }

    /**
     * @param integer $id
     * @return array
     */
    public function findById($id)
    {
        $query = "
            SELECT *
            FROM `commenter`
            WHERE `id` = :id
            LIMIT 1";

        $bindings = [
            'id' => $id,
        ];

        return $this->extendedPdo->fetchOne($query, $bindings);
    }

    /**
     * @param string $name
     * @param string $email
     * @param string $website
     * @return array
     */
    public function findByFields($name, $email, $website)
    {
        $query = "
            SELECT *
            FROM `commenter`
            WHERE `name` = :name AND
                  `email` = :email AND
                  `website` = :website
            LIMIT 1";

        $bindings = [
            'name' => $name,
            'email' => $email,
            'website' => $website,
        ];

        return $this->extendedPdo->fetchOne($query, $bindings);
    }

    /**
     * @return array
     */
    public function getCommenters()
    {
        $query = "
            SELECT *
            FROM `commenter`";

        return $this->extendedPdo->fetchAll($query);
    }

    /**
     * @param integer $locationId
     * @return array
     */
    public function getNotificationRecipients($locationId)
    {
        $query = "
            SELECT `name`, `email`
            FROM `commenter`
            INNER JOIN `comment` ON `comment`.`commenter` = `commenter`.`id` AND
                                    `comment`.`comment_location` = :location AND
                                    `comment`.`notify` = :should_notify AND
                                    `comment`.`display` = :is_displayed";

        $bindings = [
            'location' => $locationId,
            'should_notify' => 1,
            'is_displayed' => 1,
        ];

        return $this->extendedPdo->fetchAll($query, $bindings);
    }
}
