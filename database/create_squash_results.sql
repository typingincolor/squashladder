# phpMyAdmin SQL Dump
# version 2.5.6
# http://www.phpmyadmin.net
#
# Host: localhost
# Generation Time: Jul 15, 2004 at 12:28 PM
# Server version: 4.0.20
# PHP Version: 4.2.3
# 
# Database : `braither_prod`
# 

# --------------------------------------------------------

#
# Table structure for table `squash_results`
#

CREATE TABLE `squash_results` (
  `resultid` int(11) NOT NULL default '0',
  `match_date` date NOT NULL default '0000-00-00',
  `player1` int(11) NOT NULL default '0',
  `player2` int(11) NOT NULL default '0',
  `result` int(11) NOT NULL default '0',
  PRIMARY KEY  (`resultid`)
) TYPE=MyISAM;
