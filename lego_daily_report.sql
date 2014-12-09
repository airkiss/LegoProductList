use librick;
CREATE TABLE `lego_daily_report` (
  `days` int(11) NOT NULL default '0' COMMENT '查詢日期',
  `id` char(128) NOT NULL default '' comment 'LibrickID',
  `badges` TEXT NULL comment '標籤',
  `URL` TEXT NULL comment 'URL',
  `descr` TEXT NULL comment '產品描述',
  `legoID` char(128) NULL comment 'Lego代號',
  `price` char(128) NULL comment '售價',
  `status` char(128) NULL comment '產品狀態',
  `rate` int(11) NOT NULL default '0' comment '評價',
  PRIMARY KEY (`days`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='每日樂高產品報價';
