#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2021-04-28';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '2021.1.0';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '2021r1';

CREATE TABLE `DESKTOPPROJECTTHUMBNAILS` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `datecreated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datemodified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `projectref` varchar(255) NOT NULL DEFAULT '',
  `available` tinyint(1) NOT NULL DEFAULT 0,
  `projectdatemodified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `userid` int(11) NOT NULL DEFAULT 0,
  `groupcode` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `projectref_UNIQUE` (`projectref`),
  INDEX `datemodified_groupcode` (`datemodified` ASC, `groupcode` ASC),
  INDEX `userid` (`userid` ASC)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `BRANDING`
  ADD COLUMN `desktopthumbnaildeletionenabled` tinyint(1) NOT NULL DEFAULT '0' AFTER `orderredactionmode`,
  ADD COLUMN `desktopthumbnaildeletionordereddays` int(11) NOT NULL DEFAULT '7' AFTER `desktopthumbnaildeletionenabled`,
  ADD INDEX `desktopthumbnaildeletionenabled` (`desktopthumbnaildeletionenabled`);

INSERT INTO `TASKS` (
  `datecreated`, `taskcode`, `taskname`, `intervaltype`, `intervalvalue`, `lastruntime`,
  `nextruntime`, `statuscode`, `statusmessage`, `runstatus`, `maxruncount`, `internal`,
  `scriptfilename`, `deleteexpiredinterval`, `active`
)
VALUES (
  NOW(), 'TAOPIX_DESKTOPPROJECTTHUMBNAILDELETION', 'en Desktop Project Thumbnail Deletion', 2, '03:00', '0000-00-00 00:00:00',
  '0000-00-00 00:00:00', 0, '', 0, 10, 1, 'desktopProjectThumbnailDeletionTask.php', 10, 1
);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;

