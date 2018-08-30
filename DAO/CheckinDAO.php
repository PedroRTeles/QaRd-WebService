<?php
header('Content-Type: text/html; charset=utf-8');
include("../Model/Checkin.php");
include("BaseDAO.php");

$requestId = $_REQUEST["requestId"];

$checkin = new Checkin();
$db = new BaseDAO();

$response = array();

switch ($requestId) {
	case 1:
	registerCheckin($_REQUEST["idEmployee"], $db->getConnection());
	break;
}

function registerCheckin($idEmployee, $connection) {
	$statementSelect = $connection->prepare("SELECT type FROM checkin WHERE idEmployee = ? ORDER BY checkinTimestamp DESC LIMIT 1");
	$statementSelect->bind_param("i", $idEmployee);
	$statementSelect->execute();

	$statementSelect->store_result();
	$statementSelect->bind_result($lastType);

	$statementSelect->fetch();

	if($statementSelect->num_rows == 1)
	{
		if($lastType == 4)
		{
			$checkin->type = 1;
		}
		else
		{
			$checkin->type = $lastType + 1;
		}
	}
	else
	{
		$checkin->type = 1;
	}

	$statementSelect->close();

	$statementInsert = $connection->prepare("INSERT INTO checkin (idEmployee, type) VALUES (?, ?)");
	$statementInsert->bind_param("ii", $idEmployee, $checkin->type);

	if($statementInsert->execute())
	{
		authenticateCheckin($connection);
	}
	else
	{
		$response["code"] = 0;
		echo json_encode($response);
	}
}

function authenticateCheckin($connection)
{
	$statementSelect = $connection->prepare("SELECT idCheckin, idEmployee, checkinTimestamp, type FROM checkin ORDER BY idCheckin DESC LIMIT 1");
	$statementSelect->execute();

	$statementSelect->store_result();
	$statementSelect->bind_result($idLastCheckin, $idEmployeeLastCheckin, $timestampLastCheckin, $typeLastCheckin);

	$statementSelect->fetch();

	$timestampLastCheckin = strtotime($timestampLastCheckin);

	$date = date('d/m/Y', $timestampLastCheckin);
	$hour = date('H:i:s', $timestampLastCheckin);

	$date = str_replace("/", "", $date);
	$hour = str_replace(":", "", $hour);

	$code = $hour . $idEmployeeLastCheckin . $idLastCheckin . $typeLastCheckin . $date;

	$code += 402;

	$code = strtoupper(hash("sha256", $code));

	$statementInsert = $connection->prepare("UPDATE checkin SET authCode = ? WHERE idCheckin = ?");
 	$statementInsert->bind_param("si", $code, $idLastCheckin);

	if($statementInsert->execute())
	{
		$response["code"] = 1;
		echo json_encode($response);
	}
	else
	{
		$response["code"] = 0;
		echo json_encode($response);
	}

	$statementSelect->close();
	$statementInsert->close();
}

?>
