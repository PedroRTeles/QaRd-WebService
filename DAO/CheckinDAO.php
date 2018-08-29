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
        authenticateCheckin();
    }
    else
    {
      $response["code"] = 0;
      echo json_encode($response);
    }
  }

  function authenticateCheckin()
  {
    //TODO: implement authentication logic
  }

?>
