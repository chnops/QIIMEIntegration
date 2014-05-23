--DROP TABLE IF EXISTS users;
--DROP TABLE IF EXISTS projects;
--DROP TABLE IF EXISTS uploaded_files;
DROP TABLE IF EXISTS script_runs;

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
--TODO id is unique per owners
--TODO name is unique per owners

CREATE TABLE IF NOT EXISTS uploaded_files (
	project_id INT(11) NOT NULL ,
	project_owner VARCHAR(255) NOT NULL ,
	given_name VARCHAR(255) NOT NULL ,
	system_name INT(11) NOT NULL ,
	file_type VARCHAR(255) NOT NULL DEFAULT "arbitrary_text" ,
	FOREIGN KEY (project_id, project_owner) REFERENCES projects(id, owner)
	);
--TODO given_name is unique per 'project'
--TODO system_name is unique per 'project'

CREATE TABLE IF NOT EXISTS script_runs (
	id INTEGER NOT NULL UNIQUE PRIMARY KEY ,
	project_id INT(11) NOT NULL ,
	project_owner VARCHAR(255) NOT NULL ,
	script_name VARCHAR(255) NOT NULL ,
	script_string TEXT NOT NULL ,
	output TEXT ,
	version TEXT ,
	FOREIGN KEY (project_id, project_owner) REFERENCES projects(id, owner)
	);
