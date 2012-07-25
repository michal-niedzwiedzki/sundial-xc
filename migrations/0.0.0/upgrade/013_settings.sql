CREATE TABLE settings (
	id int(11) NOT NULL auto_increment,
	name varchar(255) default NULL,
	display_name varchar(255) default NULL,
	typ varchar(10) default NULL,
	current_value text,
	options varchar(255) default NULL,
	default_value text,
	max_length varchar(5) default '99999',
	descrip text,
	section int(1) default NULL,
	PRIMARY KEY  (id)
) ENGINE MyISAM AUTO_INCREMENT 35 DEFAULT CHARACTER SET utf8;
