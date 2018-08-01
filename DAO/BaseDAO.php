<?php

  class BaseDAO
  {
      private $config;

      function __construct()
      {
          include("config.php");
          $this->config = $config;
      }

      public function connect()
      {
        return new mysqli($this->config["server"], $this->config["username"], $this->config["password"], $this->config["dbName"]);
      }
  }
?>
