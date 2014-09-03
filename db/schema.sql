# ------------------------------------------------------------
# Database schema for comment service
# For use w/ https://github.com/jacobemerick/comment-service
# ------------------------------------------------------------


# Create database to hold schema
# ------------------------------------------------------------
DROP DATABASE IF EXISTS `comment_service`;

CREATE DATABASE `comment_service`;

SHOW WARNINGS;

USE `comment_service`;



# Primary holder table for comment data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `comment`;

CREATE TABLE `comment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `commenter` int(11) unsigned NOT NULL DEFAULT '0',
  `comment_body` int(11) unsigned NOT NULL DEFAULT '0',
  `comment_location` int(11) unsigned NOT NULL DEFAULT '0',
  `comment_request` int(11) DEFAULT '0',
  `notify` tinyint(1) NOT NULL DEFAULT '0',
  `display` tinyint(1) NOT NULL DEFAULT '0',
  `create_time` datetime NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SHOW WARNINGS;



# Holder of comment body
# ------------------------------------------------------------

DROP TABLE IF EXISTS `comment_body`;

CREATE TABLE `comment_body` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `body` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SHOW WARNINGS;



# Location of comment (domain/page/thread)
# ------------------------------------------------------------

DROP TABLE IF EXISTS `comment_location`;

CREATE TABLE `comment_location` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `domain` int(11) unsigned NOT NULL,
  `page` int(11) unsigned DEFAULT NULL,
  `thread` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SHOW WARNINGS;



# Domain (location) of the comment
# ------------------------------------------------------------

DROP TABLE IF EXISTS `comment_domain`;

CREATE TABLE `comment_domain` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `domain` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SHOW WARNINGS;



# Page (location) of the comment
# ------------------------------------------------------------

DROP TABLE IF EXISTS `comment_page`;

CREATE TABLE `comment_page` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `page` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SHOW WARNINGS;



# Thread (location) of the comment
# ------------------------------------------------------------

DROP TABLE IF EXISTS `comment_thread`;

CREATE TABLE `comment_thread` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `thread` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SHOW WARNINGS;



# Extra parameters that define the request of the comment
# ------------------------------------------------------------

DROP TABLE IF EXISTS `comment_request`;

CREATE TABLE `comment_request` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` binary(16) NOT NULL DEFAULT '\\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',
  `user_agent` text NOT NULL,
  `referrer` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SHOW WARNINGS;



# Commenter (author) of the comment
# ------------------------------------------------------------

DROP TABLE IF EXISTS `commenter`;

CREATE TABLE `commenter` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `url` varchar(100) DEFAULT NULL,
  `key` char(10) NOT NULL DEFAULT '',
  `is_trusted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SHOW WARNINGS;
