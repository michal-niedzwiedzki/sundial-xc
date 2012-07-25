CREATE TABLE trades_pending (
	id mediumint(8) unsigned NOT NULL auto_increment,
	trade_date timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
	member_id_from varchar(15) NOT NULL default '',
	member_id_to varchar(15) NOT NULL default '',
	amount decimal(8,2) NOT NULL default 0.00,
	category smallint(4) unsigned NOT NULL default 0,
	description varchar(255) default NULL,
	typ varchar(1) default NULL,
	status varchar(1) default 'O',
	member_to_decision varchar(2) default '1',
	member_from_decision varchar(2) default '1',
	PRIMARY KEY (id)
) ENGINE MyISAM AUTO_INCREMENT 17 DEFAULT CHARACTER SET utf8;
