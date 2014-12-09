use librick;
CREATE TABLE `lego_info` (
	`days` INT(11) NOT NULL DEFAULT 0 comment '日期',
	`id` varchar(128) NOT NULL DEFAULT '' comment 'Lego ID',
	`badges` TEXT NOT NULL DEFAULT '' comment 'Badges',
	`price` TEXT NOT NULL DEFAULT '' comment '價格',
	`condition` varchar(128) NOT NULL DEFAULT '' comment '狀態',
	`rating` varchar(32) NOT NULL DEFAULT '' comment '評價',
	`name` TEXT NOT NULL DEFAULT '' comment '說明',
	`url` TEXT NOT NULL DEFAULT '' comment '產品URL',
	PRIMARY KEY (`days`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Lego Info Table';
