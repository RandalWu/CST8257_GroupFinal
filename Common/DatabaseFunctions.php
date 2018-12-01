<?php

//              SQL Functions           
//All Functions have been collaborated on by Randy Wu, Brenna Arbour, Kyle Leslie

//Select User given an ID and return a User object or null if user doesn't exist
function getUserById($userId) {
    $dbConnection = parse_ini_file("db_connection.ini");
    extract($dbConnection);
    $PDO = new PDO($dsn, $un, $p);
    
    $sql = "SELECT * FROM Student WHERE StudentId = :userId"; 
    $pStmt = $PDO -> prepare( $sql );
    $pStmt -> execute(['userId' => $userId]);
    $row = $pStmt->fetch(PDO::FETCH_ASSOC);
    if ($row){
        return new Student($row['StudentId'], $row['Name'], $row['Phone'], $row['Password'] );  
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

function validateFriendsAlreadyCheck($friendID, $myID)
{
    $dbConnection = parse_ini_file("db_connection.ini");
    extract($dbConnection);
    $PDO = new PDO($dsn, $un, $p);

    $sql = "SELECT FriendRequesterId, FriendRequesteeId, Friendship.Status            
            FROM `Friendship` 
            WHERE FriendRequesteeId =:friendsID AND FriendRequesterId =:myID";
    $pStmt = $PDO -> prepare( $sql );
    $pStmt -> execute(['friendsID' => $friendID, 'myID' => $myID]);
    $row = ($pStmt->fetch(PDO::FETCH_ASSOC));


    if ($row)
    {
        echo $row['Status'];
        return $row['Status'];

    }
    else
    {
        echo "else";
        return "not friends";
    }


}

function validatePreExistingFriendRequest($friendID, $myID)
{
    $dbConnection = parse_ini_file("db_connection.ini");
    extract($dbConnection);
    $PDO = new PDO($dsn, $un, $p);

    $sql = "SELECT FriendRequesterId, FriendRequesteeId, Friendship.Status                      
            FROM `Friendship` 
            WHERE Status = 'request' AND FriendRequesteeId =:myID AND FriendRequesterId =:friendsID";
    $pStmt = $PDO -> prepare( $sql );
    $pStmt -> execute(['friendsID' => $friendID, 'myID' => $myID]);
    $row = $pStmt->fetch(PDO::FETCH_ASSOC);


    if ($row)
    {
        return "request exists";
    }
    else
    {
        return false;
    }
}