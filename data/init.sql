CREATE DATABASE photo_sharing;

use photo_sharing;

CREATE TABLE `user` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `email` varchar(30) NOT NULL,
  `password` char(40) NOT NULL,
  `lastname` varchar(30) DEFAULT NULL,
  `firstname` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `photo` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `userid` int(4) NOT NULL,
  `imagename` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `imagename` (`imagename`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
