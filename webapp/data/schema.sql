--DROP TABLE IF EXISTS users;
--DROP TABLE IF EXISTS projects;
--DROP TABLE IF EXISTS uploaded_files;
--DROP TABLE IF EXISTS script_runs;

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

CREATE TABLE IF NOT EXISTS file_statuses (
	status INTEGER NOT NULL UNIQUE ,
	description TEXT NOT NULL UNIQUE ,
	PRIMARY KEY (status)
	);
--INSERT INTO file_statuses (status, description) VALUES (0, "ready");
--INSERT INTO file_statuses (status, description) VALUES (1, "download in progress");
--INSERT INTO file_statuses (status, description) VALUES (2, "download failed");
--INSERT INTO file_statuses (status, description) VALUES (-1, "deleted");

CREATE TABLE IF NOT EXISTS uploaded_files (
	project_id INT(11) NOT NULL ,
	project_owner VARCHAR(255) NOT NULL ,
	name VARCHAR(255) NOT NULL ,
	file_type VARCHAR(255) NOT NULL DEFAULT "arbitrary_text" ,
	FOREIGN KEY (project_id, project_owner) REFERENCES projects(id, owner)
	);
--TODO given_name is unique per 'project'
--ALTER TABLE uploaded_files ADD COLUMN status INTEGER DEFAULT 0 REFERENCES file_statuses(status);
--ALTER TABLE uploaded_files ADD COLUMN approx_size INTEGER;

CREATE TABLE IF NOT EXISTS script_runs (
	id INTEGER NOT NULL UNIQUE PRIMARY KEY ,
	project_id INT(11) NOT NULL ,
	project_owner VARCHAR(255) NOT NULL ,
	script_name VARCHAR(255) NOT NULL ,
	script_string TEXT NOT NULL , -- rename to param_string
	output TEXT , -- delete
	version TEXT , -- delete
	FOREIGN KEY (project_id, project_owner) REFERENCES projects(id, owner)
	);
--ALTER TABLE script_runs ADD COLUMN run_status INTEGER DEFAULT -1;
--ALTER TABLE script_runs ADD COLUMN deleted INTEGER DEFAULT 0;

CREATE TABLE IF NOT EXISTS generated_files (
	run_id INTEGER NOT NULL ,
	name TEXT NOT NULL ,
	size INTEGER DEFAULT -1 ,
	PRIMARY KEY (run_id, name) ,
	FOREIGN KEY (run_id) REFERENCES script_runs(id)
	);
-- maybe add type information
