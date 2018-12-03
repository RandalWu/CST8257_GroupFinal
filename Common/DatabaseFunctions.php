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

    if ($pStmt)
    {
//        return "success";
        return $pStmt['Status'];
    }
    else
    {
        return "fail2";
    }

}
//
//function friendshipStatus($myID, $friendID)
//{
//    $dbConnection = parse_ini_file("db_connection.ini");
//    extract($dbConnection);
//    $PDO = new PDO($dsn, $un, $p);
//
//    $sql = "SELECT FriendRequesterId, FriendRequesteeId, Friendship.Status Status
//            FROM `Friendship`
//            WHERE (FriendRequesteeId =:friendsID AND FriendRequesterId =:myID)
//            OR (FriendRequesteeId =:myID AND FriendRequesterId =:friendID)";
//    $pStmt = $PDO -> prepare( $sql );
//    $pStmt -> execute(['friendsID' => $friendID, 'myID' => $myID, 'myID2' => $myID, 'friendsID2' => $friendID]);
//    $row = ($pStmt->fetch(PDO::FETCH_ASSOC));
//
//
//    if ($row)
//    {
//        return $row['Status'];
//
//    }
//    else
//    {
//        return "not friends";
//    }
//
//
//}





