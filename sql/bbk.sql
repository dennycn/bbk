
DROP DATABASE bbk;
CREATE DATABASE bbk;
USE bbk;

DROP TABLE IF EXISTS `pageCache`;
CREATE TABLE `pageCache` (       # 页面缓存
  `url` varchar(255) NOT NULL,
  `expire` datetime default NULL,  # 失效时间
  `page` MEDIUMBLOB,               # html page
  PRIMARY KEY  (`url`),
  KEY `expire` (`expire`)
)ENGINE=MyISAM;

DROP TABLE IF EXISTS `pending`;
CREATE TABLE `pending` (        # 已显示出，但尚未被用户投票的结果页面
  `pid` int(11) NOT NULL auto_increment,     # 过程id
  `uid` int(11) default NULL,         # 用户id -> table users
  `query` varchar(255) default NULL,
  `showOrder` varchar(255) default NULL,   #"googlebaidu" or "baidugoogle" 页面显示顺序
  `deadline` datetime default NULL,        # 失效时间
  PRIMARY KEY  (`pid`),
  KEY `deadline` (`deadline`)
)ENGINE=MyISAM;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (               # 已投票过的用户
  `uid` int(11) NOT NULL auto_increment,  # 用户id
  `cookie` varchar(255) default NULL,     # cookie string
  `lastTime` datetime default NULL,       # 上次动作时间
  PRIMARY KEY  (`uid`),
  UNIQUE KEY `cookie` (`cookie`)
)ENGINE=MyISAM;

DROP TABLE IF EXISTS `vote`;
CREATE TABLE `vote` (                # 投票结果
 `vid` int(11) NOT NULL auto_increment,     
  `query` varchar(255) default NULL,
  `uid` int(11) default NULL,        # uid -> table users
  `ip` varchar(20) default NULL,     # 投票ip地址
  `choose` varchar(255) default NULL,  # 选择 "google" or "baidu"
  `chooseTime` datetime default NULL,  # 投票时间
  `type` tinyint default 0,   # 为1则只累计到该用户的票数上，不全局累计
  KEY `query` (`query`),
  KEY `uid` (`uid`),
  KEY `choose` (`choose`),
  KEY `ip` (`ip`),
  KEY `type` (`type`, `choose`),
  PRIMARY KEY  (`vid`)
)ENGINE=MyISAM;


grant SELECT,INSERT,UPDATE,DELETE,CREATE,DROP ON bbk.* TO  'bbk'@'localhost';

