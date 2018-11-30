<?php

//              SQL Functions           
//All Functions have been collaboarated on by Randy Wu, Brenna Arbour, Kyle Leslie

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
