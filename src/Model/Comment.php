<?php

namespace Jacobemerick\CommentService\Model;

use Aura\Sql\ExtendedPdo;

class Comment
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
     * @param integer $commenter
     * @param integer $body
     * @param integer $location
     * @param integer $reply_to
     * @param integer $request
     * @param string $url
     * @param integer $notify
     * @param integer $display
     * @param integer $create_time
     * @returns integer
     */
    public function create(
        $commenter,
        $body,
        $location,
        $reply_to,
        $request,
        $url,
        $notify,
        $display,
        $create_time
    ) {
        $query = "
            INSERT INTO
                `comment` (`commenter`, `comment_body`, `comment_location`, `reply_to`, `comment_request`,
                           `url`, `notify`, `display`, `create_time`)
            VALUES
                (:commenter, :body, :location, :reply_to, :request, :url, :notify, :display, :create_time)";

        $bindings = [
            'commenter' => $commenter,
            'body' => $body,
            'location' => $location,
            'reply_to' => $reply_to,
            'request' => $request,
            'url' => $url,
            'notify' => $notify,
            'display' => $display,
            'create_time' => date('Y-m-d H:i:s', $create_time),
        ];

        $this->extendedPdo->perform($query, $bindings);
        return $this->extendedPdo->lastInsertId();
    }

    /**
     * @param integer $id
     */
    public function findById($id)
    {
        $query = "
            SELECT
                `comment`.`id`,
                `comment`.`url`,
                `commenter`.`id` AS `commenter_id`,
                `commenter`.`name` AS `commenter_name`,
                `commenter`.`website` AS `commenter_website`,
                `comment_body`.`body`,
                `comment_domain`.`domain`,
                `comment_path`.`path`,
                `comment_thread`.`thread`,
                `comment`.`reply_to`
            FROM `comment`
            INNER JOIN `commenter` ON `commenter`.`id` = `comment`.`commenter`
            INNER JOIN `comment_body` ON `comment_body`.`id` = `comment`.`comment_body`
            INNER JOIN `comment_location` ON `comment_location`.`id` = `comment`.`comment_location`
            INNER JOIN `comment_domain` ON `comment_domain`.`id` = `comment_location`.`domain`
            INNER JOIN `comment_path` ON `comment_path`.`id` = `comment_location`.`path`
            INNER JOIN `comment_thread` ON `comment_thread`.`id` = `comment_location`.`thread`
            WHERE `comment`.`id` = :id AND
                  `comment`.`is_deleted` = :not_deleted
            LIMIT 1";

        $bindings = [
            'id' => $id,
            'not_deleted' => 0,
        ];

        return $this->extendedPdo->fetchOne($query, $bindings);
    }

    /**
     * @param integer $id
     */
    public function deleteById($id)
    {
        $query = "
            UPDATE `comment`
            SET `is_deleted` = :deleted
            WHERE `id` = :id
            LIMIT 1";

        $bindings = [
            'deleted' => 1,
            'id' => $id,
        ];

        return $this->extendedPdo->perform($query, $bindings);
    }

    /**
     * @param string $domain
     * @param string $path
     * @param string $order
     * @param boolean $is_ascending
     * @param boolean $only_displayable
     * @param integer $limit
     * @param integer $offset
     * @returns array
     */
    public function getComments(
        $domain = '',
        $path = '',
        $order = '',
        $is_ascending = true,
        $only_displayable = true,
        $limit = 0,
        $offset = 0
    ) {
        $query = "
            SELECT
                `comment`.`id`,
                `comment`.`url`,
                `comment`.`create_time` AS `date`,
                `commenter`.`id` AS `commenter_id`,
                `commenter`.`name` AS `commenter_name`,
                `commenter`.`website` AS `commenter_website`,
                `comment_body`.`body`,
                `comment_domain`.`domain`,
                `comment_path`.`path`,
                `comment_thread`.`thread`,
                `comment`.`reply_to`
            FROM `comment`
            INNER JOIN `commenter` ON `commenter`.`id` = `comment`.`commenter`
            INNER JOIN `comment_body` ON `comment_body`.`id` = `comment`.`comment_body`
            INNER JOIN `comment_location` ON `comment_location`.`id` = `comment`.`comment_location`
            INNER JOIN `comment_domain` ON `comment_domain`.`id` = `comment_location`.`domain`
            INNER JOIN `comment_path` ON `comment_path`.`id` = `comment_location`.`path`
            INNER JOIN `comment_thread` ON `comment_thread`.`id` = `comment_location`.`thread`
            WHERE `is_deleted` = :not_deleted";

        if ($domain != '') {
            $query .= " AND
                `comment_domain`.`domain` = :domain";
        }
        if ($path != '') {
            $query .= " AND
                `comment_path`.`path` = :path";
        }
        if ($only_displayable) {
            $query .= " AND
                `comment`.`display` = :displayable";
        }
        if ($order != '') {
            $direction = ($is_ascending) ? 'ASC' : 'DESC';
            $query .= "
                ORDER BY {$order} {$direction}";
        }

        if ($limit > 0) {
            $query .= "
                LIMIT {$offset}, {$limit}";
        }

        $bindings = [
            'not_deleted' => 0,
        ];
        if ($domain != '') {
            $bindings['domain'] = $domain;
        }
        if ($path != '') {
            $bindings['path'] = $path;
        }
        if ($only_displayable) {
            $bindings['displayable'] = 1;
        }

        return $this->extendedPdo->fetchAll($query, $bindings);
    }
}
