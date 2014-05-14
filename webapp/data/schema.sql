DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS projects;
DROP table IF EXISTS files;

CREATE TABLE IF NOT EXISTS users (
	username VARCHAR(255) NOT NULL UNIQUE PRIMARY KEY ,
	root INT(11) NOT NULL UNIQUE
	);

CREATE TABLE IF NOT EXISTS projects (
	id INTEGER NOT NULL ,
	owner VARCHAR(255) NOT NULL ,
	name VARCHAR(255) NOT NULL ,
	PRIMARY KEY (id, owner) ,
	FOREIGN KEY (owner) REFERENCES users (username)
	);

CREATE TABLE IF NOT EXISTS files (
	project_id INTEGER NOT NULL ,
	project_owner VARCHAR(255) NOT NULL ,
	name_from_owner VARCHAR(255) NOT NULL ,
	name_on_system VARCHAR(255) NOT NULL ,
	FOREIGN KEY (project_id, project_owner) REFERENCES projects(id, owner)
	);
