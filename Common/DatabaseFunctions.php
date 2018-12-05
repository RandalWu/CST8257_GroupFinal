<?php

//              SQL Functions           
//All Functions have been collaborated on by Randy Wu, Brenna Arbour, Kyle Leslie

//Select User given an ID and return a User object or null if user doesn't exist
function getUserById($userId) {
    $dbConnection = parse_ini_file("db_connection.ini");
    extract($dbConnection);
    $PDO = new PDO($dsn, $un, $p);
    
    $sql = "SELECT * FROM User WHERE UserID = :userId"; 
    $pStmt = $PDO -> prepare( $sql );
    $pStmt -> execute(['userId' => $userId]);
    $row = $pStmt->fetch(PDO::FETCH_ASSOC);
    if ($row){
        return new User($row['UserID'], $row['Name'], $row['Phone'], $row['Password'] );  
    }
    else {
        return null;  
    }
}

//AddFriend.php============================================================
function getFriendIdById($friendID)
{
    $dbConnection = parse_ini_file("db_connection.ini");
    extract($dbConnection);
    $PDO = new PDO($dsn, $un, $p);

    $sql = "SELECT User.UserID FROM `User` WHERE UserID=:userId";
    $pStmt = $PDO -> prepare( $sql );
    $pStmt -> execute(['userId' => $friendID]);
    $row = $pStmt->fetch(PDO::FETCH_ASSOC);
    if ($row){
        return $row;
    }
    else {
        return null;
    }

}





//region Deprecated Functions

function validateFriendshipMeToThem($friendID, $myID)
{
    $dbConnection = parse_ini_file("db_connection.ini");
    extract($dbConnection);
    $PDO = new PDO($dsn, $un, $p);

    $sql = "SELECT FriendRequesterId, FriendRequesteeId, Friendship.Status Status
            FROM `Friendship`
            WHERE FriendRequesteeId =:friendsID AND FriendRequesterId =:myID";
    $pStmt = $PDO -> prepare( $sql );
    $pStmt -> execute(['friendsID' => $friendID, 'myID' => $myID]);
    $row = ($pStmt->fetch(PDO::FETCH_ASSOC));


    if ($row)
    {
        return $row['Status'];

    }
    else
    {
        return "not friends";
    }


}



function validateFriendshipThemToMe($friendID, $myID)
{
$dbConnection = parse_ini_file("db_connection.ini");
    extract($dbConnection);
    $PDO = new PDO($dsn, $un, $p);

    $sql = "SELECT FriendRequesterId, FriendRequesteeId, Friendship.Status Status
            FROM `Friendship`
            WHERE FriendRequesteeId =:myID AND FriendRequesterId =:friendID";
    $pStmt = $PDO -> prepare( $sql );
    $pStmt -> execute(['myID' => $myID, 'friendsID' => $friendID]);
    $row = ($pStmt->fetch(PDO::FETCH_ASSOC));


    if ($row)
    {
        return $row['Status'];

    }
    else
    {
        return "not friends";
    }


}
//endregion



function validatePreExistingFriendRequest($friendID, $myID)
{
    $dbConnection = parse_ini_file("db_connection.ini");
    extract($dbConnection);
    $PDO = new PDO($dsn, $un, $p);

    $sql = "SELECT FriendRequesterId, FriendRequesteeId, Friendship.Status Status            
            FROM `Friendship` 
            WHERE FriendRequesteeId =:myID AND FriendRequesterId =:friendsID";
    $pStmt = $PDO -> prepare( $sql );
    $pStmt -> execute(['friendsID' => $friendID, 'myID' => $myID]);
    $row = ($pStmt->fetch(PDO::FETCH_ASSOC));


    if ($row)
    {
        return $row['Status'];

    }
    else
    {
        return "fail1";
    }
}


function addFriendsFromExistingRequest($friendID, $myID)
{
    $dbConnection = parse_ini_file("db_connection.ini");
    extract($dbConnection);
    $PDO = new PDO($dsn, $un, $p);

    $sql = "UPDATE `CST8257`.`Friendship` 
            SET `Status` = 'accepted' 
            WHERE `friendship`.`FriendRequesteeId` =:myID AND `FriendRequesterId` =:friendID";
    $pStmt = $PDO -> prepare( $sql );
    $pStmt -> execute(['myID' => $myID, 'friendID' => $friendID]);
    $row = ($pStmt->fetch(PDO::FETCH_ASSOC));


    if ($row)
    {
//        return "success";
        return $row['Status'];
    }
    else
    {
        return "fail2";
    }

}

//checks if two users are friends or not
function friendshipStatus($myID, $friendID)
{
    $dbConnection = parse_ini_file("db_connection.ini");
    extract($dbConnection);
    $PDO = new PDO($dsn, $un, $p);

    $sql = "SELECT FriendRequesterId, FriendRequesteeId, Friendship.Status Status
            FROM `Friendship`
            WHERE (FriendRequesteeId =:friendID AND FriendRequesterId =:myID)
            OR (FriendRequesteeId =:myID2 AND FriendRequesterId =:friendID2)";
    $pStmt = $PDO -> prepare( $sql );
    $pStmt -> execute(['friendID' => $friendID, 'myID' => $myID, 'myID2' => $myID, 'friendID2' => $friendID]);
    $row = ($pStmt->fetch(PDO::FETCH_ASSOC));


    if ($row)
    {
        return $row['Status'];   //this will return "accepted" or "request"

    }
    else
    {
        return "not friends";
    }

}


function sendFriendRequest($myID, $friendID)
{
    $dbConnection = parse_ini_file("db_connection.ini");
    extract($dbConnection);
    $PDO = new PDO($dsn, $un, $p);

    $sql = "INSERT INTO `CST8257`.`Friendship` (`FriendRequesterId`, `FriendRequesteeId`, `Status`) 
            VALUES (?, ?, ?);";
    $pStmt = $PDO -> prepare( $sql );
    $pStmt -> execute([$myID, $friendID, 'request']);

    if ($pStmt)
    {
//        return "success";
        return "success";
    }
    else
    {
        return "fail3";
    }

}



function getListOfFriendIDs($myID)
{
    $dbConnection = parse_ini_file("db_connection.ini");
    extract($dbConnection);
    $PDO = new PDO($dsn, $un, $p);

    $friendsList = array();

    $sql = "SELECT FriendRequesterId, FriendRequesteeId, Friendship.Status Status
            FROM `Friendship`
            WHERE (FriendRequesterId =:myID
            OR FriendRequesteeId =:myID2)  AND Status='accepted'";
    $pStmt = $PDO -> prepare( $sql );
    $pStmt -> execute(['myID' => $myID, 'myID2' => $myID]);

    foreach ($pStmt as $row)
    {
        $requestReciever = $row['FriendRequesteeId'];
        $requestSender = $row['FriendRequesterId'];

        if ($requestReciever != $myID)
        {
            $friendsList[] = $requestReciever;
        }
        if ($requestSender != $myID)
        {
            $friendsList[] = $requestSender;

        }

    }

    return $friendsList;


}

function getListOfRequests($myID)
{
    $dbConnection = parse_ini_file("db_connection.ini");
    extract($dbConnection);
    $PDO = new PDO($dsn, $un, $p);

    $friendsRequests = array();

    $sql = "SELECT FriendRequesterId, FriendRequesteeId, Friendship.Status Status
            FROM `Friendship`
            WHERE (FriendRequesterId =:myID
            OR FriendRequesteeId =:myID2)  AND Status='request'";
    $pStmt = $PDO -> prepare( $sql );
    $pStmt -> execute(['myID' => $myID, 'myID2' => $myID]);

    foreach ($pStmt as $row)
    {
        $requestReciever = $row['FriendRequesteeId'];
        $requestSender = $row['FriendRequesterId'];

        if ($requestReciever != $myID)
        {
            $friendsList[] = $requestReciever;
        }
        if ($requestSender != $myID)
        {
            $friendsRequests[] = $requestSender;
        }
    }

    return $friendsRequests;

}

function removeSelectedFriends($myID, $friendID, $status)
{
    $dbConnection = parse_ini_file("db_connection.ini");
    extract($dbConnection);
    $PDO = new PDO($dsn, $un, $p);

    $sql = "DELETE FROM `Friendship`
            WHERE ((FriendRequesteeId =:friendID AND FriendRequesterId =:myID)
            OR (FriendRequesteeId =:myID2 AND FriendRequesterId =:friendID2)) 
            AND Friendship.Status=:status";

    $pStmt = $PDO -> prepare( $sql );
    $pStmt -> execute(['friendID' => $friendID, 'myID' => $myID, 'myID2' => $myID, 'friendID2' => $friendID, 'status' => $status]);

}







