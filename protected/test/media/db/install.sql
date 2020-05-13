
-- -------------------------------------------
SET AUTOCOMMIT=0;
START TRANSACTION;
SET SQL_QUOTE_SHOW_CREATE = 1;
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
-- -------------------------------------------
-- -------------------------------------------

-- TABLE `tbl_media_file`
-- -------------------------------------------
DROP TABLE IF EXISTS `tbl_media_file`;
CREATE TABLE IF NOT EXISTS `tbl_media_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) NOT NULL,
  `file` varchar(256) NOT NULL,
  `thumb_file` varchar(256) DEFAULT NULL,
  `size` varchar(256) NOT NULL,
  `extension` varchar(256) NOT NULL,
  `model_id` int(11) DEFAULT NULL,
  `model_type` varchar(256) NOT NULL,
  `state_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `created_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `createBy` varchar(256) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_media_file_created_by_id` (`created_by_id`),
  CONSTRAINT `fk_media_file_created_by_id` FOREIGN KEY (`created_by_id`) REFERENCES `tbl_user` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- -- -------------------------------------------
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
COMMIT;
-- --------------------------------------------------------------------------------------
-- END BACKUP
-- -------------------------------------------
