-- -------------------------------------------
SET AUTOCOMMIT=0;
START TRANSACTION;
SET SQL_QUOTE_SHOW_CREATE = 1;
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
-- -------------------------------------------

-- -------------------------------------------

-- START BACKUP

-- -------------------------------------------

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
  `is_locked` int(11) NOT NULL DEFAULT '0',
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

-- TABLE `tbl_api_device_detail`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_api_device_detail`;
CREATE TABLE IF NOT EXISTS `tbl_api_device_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `device_token` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `device_name` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `device_type` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `type_id` int(11) DEFAULT '0',
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_api_device_detail_create_user` (`created_by_id`),
  CONSTRAINT `fk_api_device_detail_create_user` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_area_code`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_area_code`;
CREATE TABLE IF NOT EXISTS `tbl_area_code` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `zip_code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `state_id` int(11) DEFAULT '1',
  `type_id` int(11) DEFAULT '0',
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_area_code_created_by_id` (`created_by_id`),
  CONSTRAINT `fk_area_code_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
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
  KEY `FK_blog_created_by_id` (`created_by_id`),
  CONSTRAINT `FK_blog_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_budget`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_budget`;
CREATE TABLE IF NOT EXISTS `tbl_budget` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `state_id` int(11) DEFAULT '1',
  `type_id` int(11) DEFAULT '0',
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_budget_created_by_id` (`created_by_id`),
  CONSTRAINT `fk_budget_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_chat_media`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_chat_media`;
CREATE TABLE IF NOT EXISTS `tbl_chat_media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `file` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `size` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `extension` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `message_id` int(11) DEFAULT NULL,
  `state_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_chat_media_created_by_id` (`created_by_id`),
  KEY `fk_chat_media_message_id` (`message_id`),
  CONSTRAINT `fk_chat_media_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`),
  CONSTRAINT `fk_chat_media_message_id` FOREIGN KEY (`message_id`) REFERENCES `tbl_chat_message` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_chat_message`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_chat_message`;
CREATE TABLE IF NOT EXISTS `tbl_chat_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message` text COLLATE utf8_unicode_ci,
  `state_id` int(11) DEFAULT '0',
  `type_id` int(11) DEFAULT '0',
  `created_on` datetime NOT NULL,
  `to_user_name` varchar(512) COLLATE utf8_unicode_ci NOT NULL,
  `from_user_name` varchar(512) COLLATE utf8_unicode_ci NOT NULL,
  `to_user_id` int(11) NOT NULL,
  `from_user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_chat_response`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_chat_response`;
CREATE TABLE IF NOT EXISTS `tbl_chat_response` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message_id` int(11) NOT NULL,
  `type_id` tinyint(2) DEFAULT '0',
  `created_by_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_chat_response_message_id` (`message_id`),
  KEY `fk_chat_response_created_by_id` (`created_by_id`),
  CONSTRAINT `fk_chat_response_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`),
  CONSTRAINT `fk_chat_response_message_id` FOREIGN KEY (`message_id`) REFERENCES `tbl_chat_message` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_complaint_resolve`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_complaint_resolve`;
CREATE TABLE IF NOT EXISTS `tbl_complaint_resolve` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `complaint_id` int(11) NOT NULL,
  `state_id` int(11) DEFAULT '1',
  `type_id` int(11) DEFAULT '0',
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_complaint_resolve_created_by_id` (`created_by_id`),
  CONSTRAINT `fk_complaint_resolve_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_credit_score`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_credit_score`;
CREATE TABLE IF NOT EXISTS `tbl_credit_score` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `state_id` int(11) DEFAULT '1',
  `type_id` int(11) DEFAULT '0',
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_credit_score_created_by_id` (`created_by_id`),
  CONSTRAINT `fk_credit_score_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_down_payment`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_down_payment`;
CREATE TABLE IF NOT EXISTS `tbl_down_payment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `state_id` int(11) DEFAULT '1',
  `type_id` int(11) DEFAULT '0',
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_down_payment_created_by_id` (`created_by_id`),
  CONSTRAINT `fk_down_payment_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


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

-- TABLE `tbl_faq`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_faq`;
CREATE TABLE IF NOT EXISTS `tbl_faq` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `faq_type` int(11) NOT NULL,
  `question` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `answer` text COLLATE utf8_unicode_ci,
  `state_id` int(11) DEFAULT '1',
  `type_id` int(11) DEFAULT '0',
  `created_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  `created_by_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_faq_created_by` (`created_by_id`),
  CONSTRAINT `fk_faq_created_by` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_featured_image`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_featured_image`;
CREATE TABLE IF NOT EXISTS `tbl_featured_image` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `purpose` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bedroom` int(11) DEFAULT '0',
  `bathroom` int(11) DEFAULT '0',
  `area` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `price` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `location` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `state_id` int(11) DEFAULT '1',
  `type_id` int(11) DEFAULT '0',
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_featured_image_created_by_id` (`created_by_id`),
  CONSTRAINT `fk_featured_image_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_feedback`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_feedback`;
CREATE TABLE IF NOT EXISTS `tbl_feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `feedback` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `suggestion` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `agent_id` int(11) NOT NULL,
  `state_id` int(11) NOT NULL,
  `type_id` int(11) DEFAULT '0',
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_feedback_created_by_id` (`created_by_id`),
  CONSTRAINT `fk_feedback_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_file`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_file`;
CREATE TABLE IF NOT EXISTS `tbl_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `file` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `size` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `extension` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `model_id` int(11) NOT NULL,
  `model_type` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `state_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `created_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  `created_by_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_file_created_by_id` (`created_by_id`),
  CONSTRAINT `fk_file_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_home_inspection`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_home_inspection`;
CREATE TABLE IF NOT EXISTS `tbl_home_inspection` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `location_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `service_date` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state_id` int(11) NOT NULL,
  `type_id` int(11) DEFAULT '0',
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_home_inspection_created_by_id` (`created_by_id`),
  CONSTRAINT `fk_home_inspection_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_home_loans`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_home_loans`;
CREATE TABLE IF NOT EXISTS `tbl_home_loans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `property_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `use_of_property` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `plan` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `found_home` int(11) DEFAULT '0',
  `already_working` int(11) DEFAULT '0',
  `estimate_price` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `down_payment` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `credit_score` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `in_military` int(11) DEFAULT '0',
  `bankruptcy_foreclosure` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bankruptcy_time` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `foreclosure_time` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state_id` int(11) NOT NULL,
  `type_id` int(11) DEFAULT '0',
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_home_loans_created_by_id` (`created_by_id`),
  CONSTRAINT `fk_home_loans_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_home_section`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_home_section`;
CREATE TABLE IF NOT EXISTS `tbl_home_section` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `video_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `image_file` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title1` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description1` text COLLATE utf8_unicode_ci,
  `state_id` int(11) DEFAULT '1',
  `type_id` int(11) DEFAULT '0',
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_home_section_created_by_id` (`created_by_id`),
  CONSTRAINT `fk_home_section_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_home_type`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_home_type`;
CREATE TABLE IF NOT EXISTS `tbl_home_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci,
  `font_icon` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_file` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state_id` int(11) NOT NULL,
  `type_id` int(11) DEFAULT '0',
  `created_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  `created_by_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_home_type_created_by_id` (`created_by_id`),
  CONSTRAINT `fk_home_type_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_home_value`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_home_value`;
CREATE TABLE IF NOT EXISTS `tbl_home_value` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `contact_no` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `latitude` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `longitude` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bedroom` int(11) DEFAULT '0',
  `basement` int(11) DEFAULT '0',
  `bathroom` int(11) DEFAULT '0',
  `home_type_id` int(11) NOT NULL,
  `professional_id` int(11) NOT NULL,
  `home_condition` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state_id` int(11) NOT NULL,
  `type_id` int(11) DEFAULT '0',
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_home_value_created_by_id` (`created_by_id`),
  CONSTRAINT `fk_home_value_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_locked_user`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_locked_user`;
CREATE TABLE IF NOT EXISTS `tbl_locked_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `professional_id` int(11) NOT NULL,
  `complaint_date` date NOT NULL,
  `state_id` int(11) DEFAULT '1',
  `type_id` int(11) DEFAULT '0',
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_locked_user_created_by_id` (`created_by_id`),
  CONSTRAINT `fk_locked_user_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_log`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_log`;
CREATE TABLE IF NOT EXISTS `tbl_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `error` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `api` text COLLATE utf8_unicode_ci,
  `description` text COLLATE utf8_unicode_ci,
  `state_id` int(11) DEFAULT '1',
  `link` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type_id` int(11) DEFAULT '0',
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

-- TABLE `tbl_message`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_message`;
CREATE TABLE IF NOT EXISTS `tbl_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `to_user_id` int(11) NOT NULL,
  `state_id` int(11) NOT NULL,
  `type_id` int(11) DEFAULT '0',
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_message_created_by_id` (`created_by_id`),
  CONSTRAINT `fk_message_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_notice`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_notice`;
CREATE TABLE IF NOT EXISTS `tbl_notice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci,
  `model_type` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `model_id` int(11) NOT NULL,
  `state_id` int(11) NOT NULL DEFAULT '0',
  `type_id` int(11) NOT NULL DEFAULT '0',
  `created_on` datetime DEFAULT NULL,
  `created_by_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_notice_created_by` (`created_by_id`),
  CONSTRAINT `fk_notice_created_by` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_page`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_page`;
CREATE TABLE IF NOT EXISTS `tbl_page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci NOT NULL,
  `video_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
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

-- TABLE `tbl_payment_gateway`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_payment_gateway`;
CREATE TABLE IF NOT EXISTS `tbl_payment_gateway` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` longtext COLLATE utf8_unicode_ci,
  `mode` tinyint(4) DEFAULT '0',
  `state_id` int(11) DEFAULT '0',
  `type_id` int(11) DEFAULT '0',
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `created_by_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_payment_gateway_create_user` (`created_by_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_payment_transaction`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_payment_transaction`;
CREATE TABLE IF NOT EXISTS `tbl_payment_transaction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `model_id` int(11) DEFAULT NULL,
  `model_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `amount` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `currency` varchar(125) COLLATE utf8_unicode_ci NOT NULL,
  `transaction_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `payer_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `value` text COLLATE utf8_unicode_ci,
  `gateway_type` int(11) DEFAULT NULL,
  `payment_status` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_plan_features`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_plan_features`;
CREATE TABLE IF NOT EXISTS `tbl_plan_features` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `state_id` int(11) DEFAULT '1',
  `type_id` int(11) DEFAULT '0',
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_plan_features_created_by_id` (`created_by_id`),
  CONSTRAINT `fk_plan_features_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_profession_type`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_profession_type`;
CREATE TABLE IF NOT EXISTS `tbl_profession_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci,
  `image_file` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state_id` int(11) DEFAULT '1',
  `type_id` int(11) NOT NULL DEFAULT '0',
  `created_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  `created_by_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_profession_type_created_by_id` (`created_by_id`),
  CONSTRAINT `fk_profession_type_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_professional`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_professional`;
CREATE TABLE IF NOT EXISTS `tbl_professional` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `specialities` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `purpose_id` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `website_link` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `video_link` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `profession_type_id` int(11) NOT NULL,
  `association_us` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `association_outside_us` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_licenced` int(11) DEFAULT '0',
  `licence` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fax` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `office_no` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `facebook_link` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `twiter_link` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `googleplus_link` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `linkedin_link` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `youtube_link` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `plan_id` int(11) DEFAULT NULL,
  `company_info` text COLLATE utf8_unicode_ci,
  `calculator_url` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `property_type_id` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `budget_id` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `down_payment_id` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `time_period_id` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `credit_score_id` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `representaion_id` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state_id` int(11) DEFAULT '1',
  `type_id` int(11) DEFAULT '0',
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_professional_created_by` (`created_by_id`),
  CONSTRAINT `fk_professional_created_by` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_property_type`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_property_type`;
CREATE TABLE IF NOT EXISTS `tbl_property_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `image_file` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fa_icon` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state_id` int(11) DEFAULT '1',
  `type_id` int(11) DEFAULT '0',
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_property_type_created_by_id` (`created_by_id`),
  CONSTRAINT `fk_property_type_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_purpose`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_purpose`;
CREATE TABLE IF NOT EXISTS `tbl_purpose` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci,
  `button_text` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `placeholder_text` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `heading_text` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state_id` int(11) DEFAULT '1',
  `type_id` int(11) NOT NULL DEFAULT '0',
  `created_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  `created_by_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_purpose_created_by_id` (`created_by_id`),
  CONSTRAINT `fk_purpose_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_representation`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_representation`;
CREATE TABLE IF NOT EXISTS `tbl_representation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `state_id` int(11) DEFAULT '1',
  `type_id` int(11) DEFAULT '0',
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_representation_created_by_id` (`created_by_id`),
  CONSTRAINT `fk_representation_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_search_record`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_search_record`;
CREATE TABLE IF NOT EXISTS `tbl_search_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `property_type_id` int(11) NOT NULL,
  `budget_id` int(11) NOT NULL,
  `time_period_id` int(11) NOT NULL,
  `want_to_buy` int(11) DEFAULT '0',
  `want_to_sell` int(11) DEFAULT '0',
  `type_id` int(11) DEFAULT '0',
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_search_record_created_by_id` (`created_by_id`),
  CONSTRAINT `fk_search_record_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


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
  `created_by_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_subscribed_users`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_subscribed_users`;
CREATE TABLE IF NOT EXISTS `tbl_subscribed_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `plan_type` int(11) NOT NULL,
  `state_id` int(11) DEFAULT '1',
  `type_id` int(11) NOT NULL DEFAULT '0',
  `created_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  `created_by_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_subscribed_users_created_by_id` (`created_by_id`),
  CONSTRAINT `fk_subscribed_users_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_subscription_plan`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_subscription_plan`;
CREATE TABLE IF NOT EXISTS `tbl_subscription_plan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `plan_features_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `amount` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `validity` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `discount` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state_id` int(11) NOT NULL,
  `type_id` int(11) DEFAULT '0',
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_subscription_plan_created_by_id` (`created_by_id`),
  CONSTRAINT `fk_subscription_plan_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_testimonial`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_testimonial`;
CREATE TABLE IF NOT EXISTS `tbl_testimonial` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `rating` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `profile_file` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `state_id` int(11) NOT NULL,
  `type_id` int(11) DEFAULT '0',
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_testimonial_created_by_id` (`created_by_id`),
  CONSTRAINT `fk_testimonial_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_time_period`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_time_period`;
CREATE TABLE IF NOT EXISTS `tbl_time_period` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `state_id` int(11) DEFAULT '1',
  `type_id` int(11) DEFAULT '0',
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_time_period_created_by_id` (`created_by_id`),
  CONSTRAINT `fk_time_period_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- -------------------------------------------

-- TABLE `tbl_title_agent`

-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_title_agent`;
CREATE TABLE IF NOT EXISTS `tbl_title_agent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `property_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `service_date` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `have_agent` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state_id` int(11) NOT NULL,
  `type_id` int(11) DEFAULT '0',
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_title_agent_created_by_id` (`created_by_id`),
  CONSTRAINT `fk_title_agent_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;




-- -------------------------------------------
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
COMMIT;
 -- -------AutobackUpStarttoxsl------ -- -------------------------------------------

-- -------------------------------------------

-- END BACKUP

-- -------------------------------------------
