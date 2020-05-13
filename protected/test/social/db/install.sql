
-- -------------------------------------------
SET AUTOCOMMIT=0;
START TRANSACTION;
SET SQL_QUOTE_SHOW_CREATE = 1;
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
-- -------------------------------------------
-- -------------------------------------------

-- TABLE `tbl_social_user`
-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_social_user`;
CREATE TABLE IF NOT EXISTS `tbl_social_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `social_user_id` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `social_provider` varchar(255) NOT NULL,
  `loginProviderIdentifier` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `social_user_id` (`social_user_id`),
  KEY `id` (`id`),
  KEY `fk_social_user_user_id` (`user_id`),
  CONSTRAINT `fk_social_user_user_id` FOREIGN KEY (`user_id`) REFERENCES `tbl_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
-- --------------------------------------------
-- TABLE `tbl_social_provider`
-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_social_provider`;
CREATE TABLE IF NOT EXISTS `tbl_social_provider` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) NOT NULL,
  `provider_type` int(11) NOT NULL,
  `client_id` varchar(512) NOT NULL,
  `client_secret_key` varchar(1023) NOT NULL,
  `state_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `created_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  `created_by_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_social_setting_create_user` (`created_by_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
-- -- -------------------------------------------
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
COMMIT;
-- --------------------------------------------------------------------------------------
-- END BACKUP
-- -------------------------------------------
