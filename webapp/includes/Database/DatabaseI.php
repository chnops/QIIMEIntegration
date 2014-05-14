<?php 

namespace Database;

interface DatabaseI {

	public function __construct();
	public function userExists($username);
	public function insertUser($username, $root);
	public function getHighestUserRoot();

	public function getAllProjects($username);
	public function createProject($username, $projectName);
}
