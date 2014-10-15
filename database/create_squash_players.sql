create table squash_players
(
	playerid mediumint unsigned not null auto_increment,
	forename varchar(20) not null,
	surname  varchar(30) not null,
	email    varchar(60),
	rank     mediumint,
	primary key (playerid)
);
