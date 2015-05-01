-- install sql, create a table to identify admin-only front end pages
CREATE TABLE `civicrm_contributextra_adminpages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT  COMMENT 'Id',
  `contribution_page_id` int(10) unsigned NOT NULL COMMENT 'Contribution Page Id',
  PRIMARY KEY ( `id` ),
  UNIQUE INDEX (`contribution_page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Table to identify admin-only front end contribution pages';
