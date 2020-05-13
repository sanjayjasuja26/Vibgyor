SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
-- -------------------------------------------
SET AUTOCOMMIT=0;
START TRANSACTION;
SET SQL_QUOTE_SHOW_CREATE = 1;
-- -------------------------------------------

-- -------------------------------------------

-- START BACKUP

-- -------------------------------------------

-- -------------------------------------------

-- TABLE `ha_logins`

-- -------------------------------------------
DROP TABLE IF EXISTS `ha_logins`;
CREATE TABLE IF NOT EXISTS `ha_logins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `loginProvider` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `loginProviderIdentifier` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `loginProvider_2` (`loginProvider`,`loginProviderIdentifier`),
  KEY `loginProvider` (`loginProvider`),
  KEY `loginProviderIdentifier` (`loginProviderIdentifier`),
  KEY `userId` (`userId`),
  KEY `id` (`id`),
  KEY `user_id` (`id`),
  KEY `fk_ha_logins_created_by` (`user_id`),
  CONSTRAINT `fk_ha_logins_user` FOREIGN KEY (`user_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_blog_category`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_blog_category`;
CREATE TABLE IF NOT EXISTS `tbl_blog_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `state_id` int(11) NOT NULL,
  `created_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  `created_by_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `title` (`title`),
  KEY `state_id` (`state_id`),
  KEY `FK_blog_category_created_by_id` (`created_by_id`),
  CONSTRAINT `FK_blog_category_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_blog_post`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_blog_post`;
CREATE TABLE IF NOT EXISTS `tbl_blog_post` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `keywords` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_file` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
  `view_count` int(11) NOT NULL DEFAULT '0',
  `state_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `created_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  `created_by_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `title` (`title`),
  KEY `keywords` (`keywords`),
  KEY `state_id` (`state_id`),
  KEY `created_on` (`created_on`),
  KEY `FK_blog_created_by_id` (`created_by_id`),
  CONSTRAINT `FK_blog_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_category`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_category`;
CREATE TABLE IF NOT EXISTS `tbl_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `state_id` int(11) NOT NULL,
  `type_id` int(11) DEFAULT '0',
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_category_created_by` (`created_by_id`),
  CONSTRAINT `fk_category_created_by` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_check`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_check`;
CREATE TABLE IF NOT EXISTS `tbl_check` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `street_name` text NOT NULL,
  `created_by_id` int(11) NOT NULL,
  `qweqweqwk` int(11) NOT NULL,
  `eqweqwa` int(11) NOT NULL,
  `eqweouiy` int(11) NOT NULL,
  `weqweqwl` int(11) NOT NULL,
  `eqweqwk` int(11) NOT NULL,
  `weqwewer` int(11) NOT NULL,
  `qweqweerq` int(11) NOT NULL,
  `qweqwewq` int(11) NOT NULL,
  `qweqwwq` int(11) NOT NULL,
  `ewqeqwewqe` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;


-- -------------------------------------------

-- TABLE `tbl_comment`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_comment`;
CREATE TABLE IF NOT EXISTS `tbl_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model_id` int(11) NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `comment` text,
  `state_id` int(11) DEFAULT '1',
  `type_id` int(11) DEFAULT '0',
  `created_on` datetime DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_comment_created_by_id` (`created_by_id`),
  CONSTRAINT `fk_comment_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- -------------------------------------------

-- TABLE `tbl_email_queue`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_email_queue`;
CREATE TABLE IF NOT EXISTS `tbl_email_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_email` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `to_email` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8_unicode_ci,
  `subject` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_published` datetime DEFAULT NULL,
  `last_attempt` datetime DEFAULT NULL,
  `date_sent` datetime DEFAULT NULL,
  `attempts` int(11) DEFAULT NULL,
  `state_id` int(11) DEFAULT NULL,
  `model_id` int(11) DEFAULT NULL,
  `model_type` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email_account_id` int(11) DEFAULT NULL,
  `message_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_feed`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_feed`;
CREATE TABLE IF NOT EXISTS `tbl_feed` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text COLLATE utf8_unicode_ci,
  `model_type` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `model_id` int(11) NOT NULL,
  `state_id` int(11) NOT NULL DEFAULT '0',
  `type_id` int(11) NOT NULL DEFAULT '0',
  `created_on` datetime DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_files`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_files`;
CREATE TABLE IF NOT EXISTS `tbl_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text COLLATE utf8_unicode_ci NOT NULL,
  `model_id` text COLLATE utf8_unicode_ci NOT NULL,
  `model_type` text COLLATE utf8_unicode_ci NOT NULL,
  `target_url` text COLLATE utf8_unicode_ci,
  `description` text COLLATE utf8_unicode_ci,
  `filename_user` text COLLATE utf8_unicode_ci NOT NULL,
  `filename_path` text COLLATE utf8_unicode_ci NOT NULL,
  `extension` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `public` int(11) NOT NULL,
  `size` bigint(20) NOT NULL,
  `download_count` bigint(20) DEFAULT '0',
  `file_type` int(11) DEFAULT '1',
  `mimetype` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `seo_title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `seo_alt` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state_id` int(11) DEFAULT NULL,
  `type_id` int(11) DEFAULT '0',
  `created_on` datetime DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `updated_by_id` int(11) DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_files_created_by_id` (`created_by_id`),
  KEY `fk_files_updated_by_id` (`updated_by_id`),
  CONSTRAINT `fk_files_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`),
  CONSTRAINT `fk_files_updated_by_id` FOREIGN KEY (`updated_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_language_option`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_language_option`;
CREATE TABLE IF NOT EXISTS `tbl_language_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `language_code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `state_id` int(11) NOT NULL DEFAULT '1',
  `type_id` int(11) NOT NULL DEFAULT '0',
  `created_on` datetime DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_language_option_created_by_id` (`created_by_id`),
  CONSTRAINT `fk_language_option_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_logger_log`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_logger_log`;
CREATE TABLE IF NOT EXISTS `tbl_logger_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `error` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `api` text COLLATE utf8_unicode_ci,
  `description` text COLLATE utf8_unicode_ci,
  `state_id` int(11) NOT NULL DEFAULT '1',
  `link` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type_id` int(11) NOT NULL,
  `created_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_login_history`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_login_history`;
CREATE TABLE IF NOT EXISTS `tbl_login_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `user_ip` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_agent` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `failer_reason` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_media_gallery`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_media_gallery`;
CREATE TABLE IF NOT EXISTS `tbl_media_gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `alt` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `file` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `thumb_file` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `size` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `extension` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `type_id` int(11) DEFAULT '0',
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `createBy` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_migration`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_migration`;
CREATE TABLE IF NOT EXISTS `tbl_migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- -------------------------------------------

-- TABLE `tbl_notice`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_notice`;
CREATE TABLE IF NOT EXISTS `tbl_notice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci,
  `model_type` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `model_id` int(11) NOT NULL,
  `state_id` int(11) DEFAULT '0',
  `type_id` int(11) DEFAULT '0',
  `created_on` datetime DEFAULT NULL,
  `created_by_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_notice_created_by` (`created_by_id`),
  CONSTRAINT `fk_notice_created_by` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_notification`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_notification`;
CREATE TABLE IF NOT EXISTS `tbl_notification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `model_id` int(11) NOT NULL,
  `model_type` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `is_read` tinyint(2) DEFAULT '0',
  `state_id` int(11) DEFAULT '0',
  `type_id` int(11) DEFAULT '0',
  `created_on` datetime NOT NULL,
  `to_user_id` int(11) DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_notification_created_by` (`created_by_id`),
  CONSTRAINT `fk_notification_created_by` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_page`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_page`;
CREATE TABLE IF NOT EXISTS `tbl_page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci NOT NULL,
  `state_id` int(11) DEFAULT '1',
  `type_id` int(11) DEFAULT NULL,
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `created_by_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_page_created_by_id` (`created_by_id`),
  CONSTRAINT `fk_page_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_queue`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_queue`;
CREATE TABLE IF NOT EXISTS `tbl_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `channel` varchar(255) NOT NULL,
  `job` blob NOT NULL,
  `pushed_at` int(11) NOT NULL,
  `ttr` int(11) NOT NULL,
  `delay` int(11) NOT NULL DEFAULT '0',
  `priority` int(11) unsigned NOT NULL DEFAULT '1024',
  `reserved_at` int(11) DEFAULT NULL,
  `attempt` int(11) DEFAULT NULL,
  `done_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `channel` (`channel`),
  KEY `reserved_at` (`reserved_at`),
  KEY `priority` (`priority`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- -------------------------------------------

-- TABLE `tbl_rating`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_rating`;
CREATE TABLE IF NOT EXISTS `tbl_rating` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model_id` int(11) NOT NULL,
  `model_type` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `rating` double NOT NULL,
  `title` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `comment` text COLLATE utf8_unicode_ci,
  `state_id` int(11) DEFAULT '1',
  `type_id` int(11) DEFAULT '0',
  `created_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  `created_by_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_rating_created_by_id` (`created_by_id`),
  CONSTRAINT `FK_rating_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_seo`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_seo`;
CREATE TABLE IF NOT EXISTS `tbl_seo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `route` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `data` varchar(255) DEFAULT NULL,
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `seo_idx_route` (`route`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- -------------------------------------------

-- TABLE `tbl_seo_analytics`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_seo_analytics`;
CREATE TABLE IF NOT EXISTS `tbl_seo_analytics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account` varchar(512) COLLATE utf8_unicode_ci NOT NULL,
  `domain_name` varchar(512) COLLATE utf8_unicode_ci NOT NULL,
  `additional_information` varchar(512) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state_id` int(11) NOT NULL DEFAULT '1',
  `type_id` int(11) NOT NULL DEFAULT '0',
  `created_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  `created_by_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_seo_analytics_created_by_id` (`created_by_id`),
  CONSTRAINT `fk_seo_analytics_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_seo_redirect`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_seo_redirect`;
CREATE TABLE IF NOT EXISTS `tbl_seo_redirect` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `old_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `new_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `state_id` int(11) NOT NULL DEFAULT '0',
  `type_id` int(11) NOT NULL DEFAULT '0',
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `created_by_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_seo_redirect_created_by_id` (`created_by_id`),
  CONSTRAINT `fk_seo_redirect_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_setting`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_setting`;
CREATE TABLE IF NOT EXISTS `tbl_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` longtext COLLATE utf8_unicode_ci,
  `type_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state_id` int(11) DEFAULT '0',
  `created_by_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
   KEY `fk_setting_created_by_id` (`created_by_id`),
  CONSTRAINT `fk_setting_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_translator_language`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_translator_language`;
CREATE TABLE IF NOT EXISTS `tbl_translator_language` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` text COLLATE utf8_unicode_ci,
  `attribute_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `language` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `model_id` int(11) NOT NULL,
  `model_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `state_id` int(11) NOT NULL DEFAULT '1',
  `type_id` int(11) NOT NULL DEFAULT '0',
  `created_on` datetime DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_translator_language_created_by_id` (`created_by_id`),
  CONSTRAINT `fk_translator_language_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



-- -------------------------------------------

-- TABLE `tbl_user`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_user`;
CREATE TABLE IF NOT EXISTS `tbl_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` int(11) DEFAULT '0',
  `about_me` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contact_no` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `alternate_contact_no` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(512) COLLATE utf8_unicode_ci DEFAULT NULL,
  `latitude` varchar(512) COLLATE utf8_unicode_ci DEFAULT NULL,
  `longitude` varchar(512) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `zipcode` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `language` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email_verified` tinyint(1) DEFAULT '0',
  `profile_file` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tos` int(11) DEFAULT NULL,
  `role_id` int(11) NOT NULL,
  `state_id` int(11) NOT NULL,
  `type_id` int(11) DEFAULT '0',
  `last_visit_time` datetime DEFAULT NULL,
  `last_action_time` datetime DEFAULT NULL,
  `last_password_change` datetime DEFAULT NULL,
  `login_error_count` int(11) DEFAULT NULL,
  `activation_key` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `access_token` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `timezone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_course

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_course`;
CREATE TABLE IF NOT EXISTS `tbl_course` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci NOT NULL,
  `state_id` int(11) DEFAULT '1',
  `type_id` int(11) DEFAULT NULL,
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `created_by_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_course_created_by_id` (`created_by_id`),
  CONSTRAINT `fk_course_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



-- -------------------------------------------

-- TABLE `tbl_parent_info`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_parent_info`;
CREATE TABLE IF NOT EXISTS `tbl_parent_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11)  NOT NULL,
  `profession` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `child_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `caste` int(11) NOT NULL,
  `current_studies` int(11) NOT NULL,
  `state_id` int(11) NOT NULL,
  `type_id` int(11) DEFAULT '0',
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `created_by_id` int(11)  NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_parent_info_created_by_id` (`created_by_id`),
  CONSTRAINT `fk_parent_info_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`),
 KEY `fk_parent_info_user_id` (`user_id`),
  CONSTRAINT `fk_parent_info_user_id` FOREIGN KEY (`user_id`) REFERENCES `tbl_user` (`id`),
 KEY `fk_parent_info_course_id` (`course_id`),
  CONSTRAINT `fk_parent_info_course_id` FOREIGN KEY (`course_id`) REFERENCES `tbl_course` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;




-- -------------------------------------------

-- TABLE `tbl_student_info`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_student_info`;
CREATE TABLE IF NOT EXISTS `tbl_student_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11)  NOT NULL,
  `course_id` int(11) NOT NULL,
  `f_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `m_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `caste` int(11) NOT NULL,
  `current_studies` int(11) NOT NULL,
  `state_id` int(11) NOT NULL,
  `type_id` int(11) DEFAULT '0',
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `created_by_id` int(11)  NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_student_info_created_by_id` (`created_by_id`),
  CONSTRAINT `fk_student_info_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`),
 KEY `fk_student_info_user_id` (`user_id`),
  CONSTRAINT `fk_student_info_user_id` FOREIGN KEY (`user_id`) REFERENCES `tbl_user` (`id`),
 KEY `fk_student_info_course_id` (`course_id`),
  CONSTRAINT `fk_student_info_course_id` FOREIGN KEY (`course_id`) REFERENCES `tbl_course` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;





-- -------------------------------------------

-- TABLE `tbl_college_info`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_college_info`;
CREATE TABLE IF NOT EXISTS `tbl_college_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11)  NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state_id` int(11) NOT NULL,
  `type_id` int(11) DEFAULT '0',
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `created_by_id` int(11)  NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_college_info_created_by_id` (`created_by_id`),
  CONSTRAINT `fk_college_info_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`),
 KEY `fk_college_info_user_id` (`user_id`),
  CONSTRAINT `fk_college_info_user_id` FOREIGN KEY (`user_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



-- -------------------------------------------

-- TABLE `tbl_course_offerd_by_college`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_course_offerd_by_college`;
CREATE TABLE IF NOT EXISTS `tbl_course_offerd_by_college` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11)  NOT NULL,
 `course_id` int(11)  NOT NULL,
  `state_id` int(11) NOT NULL,
  `type_id` int(11) DEFAULT '0',
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `created_by_id` int(11)  NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_course_offerd_by_college_created_by_id` (`created_by_id`),
  CONSTRAINT `fk_course_offerd_by_college_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`),
 KEY `fk_course_offerd_by_college_user_id` (`user_id`),
  CONSTRAINT `fk_course_offerd_by_college_user_id` FOREIGN KEY (`user_id`) REFERENCES `tbl_user` (`id`),
 KEY `fk_course_offerd_by_college_course_id` (`course_id`),
  CONSTRAINT `fk_course_offerd_by_college_course_id` FOREIGN KEY (`course_id`) REFERENCES `tbl_course` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



-- -------------------------------------------

-- TABLE `tbl_college_counseller`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_college_counseller`;
CREATE TABLE IF NOT EXISTS `tbl_college_counseller` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
   `full_name`  varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `contact_no` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `college_id` int(11)  NOT NULL,
  `state_id` int(11) NOT NULL,
  `type_id` int(11) DEFAULT '0',
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `created_by_id` int(11)  NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_college_counseller_created_by_id` (`created_by_id`),
  CONSTRAINT `fk_college_counseller_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`),
 KEY `fk_college_counseller_college_id` (`college_id`),
  CONSTRAINT `fk_college_counseller_college_id` FOREIGN KEY (`college_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_college_event`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_college_event`;
CREATE TABLE IF NOT EXISTS `tbl_college_event` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
   `title`  varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci  DEFAULT  NULL,
  `start_on` datetime NOT NULL,
  `end_on` datetime NOT NULL,
  `college_id` int(11)  NOT NULL,
  `state_id` int(11) NOT NULL,
  `type_id` int(11) DEFAULT '0',
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `created_by_id` int(11)  NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_college_event_created_by_id` (`created_by_id`),
  CONSTRAINT `fk_college_event_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`),
 KEY `fk_college_event_college_id` (`college_id`),
  CONSTRAINT `fk_college_event_college_id` FOREIGN KEY (`college_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



-- -------------------------------------------

-- TABLE `tbl_voting`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_voting`;
CREATE TABLE IF NOT EXISTS `tbl_voting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model_id` int(11) NOT NULL,
  `model_type` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `score` double NOT NULL,
  `title` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `comment` text COLLATE utf8_unicode_ci,
  `state_id` int(11) DEFAULT '1',
  `type_id` int(11) DEFAULT '0',
  `created_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  `created_by_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FKk_tbl_voting_created_by_id` (`created_by_id`),
  CONSTRAINT `FKk_tbl_voting_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
-- -------------------------------------------

-- TABLE `tbl_visitor`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_visitor`;
CREATE TABLE IF NOT EXISTS `tbl_visitor` (
  `id` bigint(20) unsigned NOT NULL,
  `ip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `visit_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `group_date` int(11) unsigned DEFAULT NULL,
  `location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `browser` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `browser_version` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `os` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `os_version` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_model` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referer` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


COMMIT;
-- -------------------------------------------
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
 -- -------AutobackUpStart------ -- -------------------------------------------

-- -------------------------------------------

-- END BACKUP

-- -------------------------------------------
