<?php
class Checkin
{
	public $idCheckin;
	public $idEmployee;
	public $hour;
	public $date;
	public $type;
	public $authCode;

	function __construct()
	{
		$this->idCheckin = 0;
		$this->idEmployee = 0;
		$this->hour = "";
		$this->date = "";
		$this->type = 0;
		$this->authCode = "";
	}
}

?>
