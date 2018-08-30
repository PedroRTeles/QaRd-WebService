<?php

class BaseDAO
{
	private $config;
	private $connection = null;

	public function __construct()
	{
		include("config.php"); //Create config.php in the same folder
		$this->config = $config;
		$this->connect();
	}

	public function getConnection()
	{
		return $this->connection;
	}

	private function connect()
	{
		$this->connection = new mysqli($this->config["server"], $this->config["username"], $this->config["password"], $this->config["dbName"]);
	}
}
?>
