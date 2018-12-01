<!DOCTYPE html>
<?php
    include './Common/Classes.php';
    include './Common/ConstantsAndSettings.php';
    
    session_start();
    
    $dbConnection = parse_ini_file("db_connection.ini"); //Have to change the db_connection.ini to make it work on your local machine
    extract($dbConnection);
    $myPDO = new PDO($dsn, $un, $p);
    $myPDO ->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
?>
<html lang="en" style="position: relative; min-height: 100%;">
<head>
<title>CST8257 Group Final</title>
        <meta charset="utf-8"> 
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="/AlgCommon/Contents/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="/AlgCommon/Contents/AlgCss/Site.css" rel="stylesheet" type="text/css"/>
</head>

<meta name=”viewport” content=”width=device-width, initial-scale=1″>

<body style="padding-top: 50px; margin-bottom: 60px; background-color: lightblue;">
    <nav class="navbar navbar-default navbar-fixed-top navbar-inverse">
      <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" 
                       data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" style="padding: 10px" href="http://www.algonquincollege.com">
              <img src="/AlgCommon/Contents/img/AC.png" 
                   alt="Algonquin College" style="max-width:100%; max-height:100%;"/>
          </a>    
        </div>
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
               <li><a href="index.php">Home</a></li>   
               <li><a href="MyFriends.php">My Lack Of Friends</a></li>   
               <li><a href="MyAlbums.php">My Albums</a></li>
               <li><a href="MyPictures.php">My Pictures</a></li>
               <li><a href="UploadPictures.php">Upload Pictures</a></li>
               <?php
               if (isset($_SESSION["loggedInStudent"]))
               {
                   echo "<li><a href='Logout.php'>Logout</a></li>";
               }
               
               else
               {
                   echo "<li><a href='Login.php'>Login</a></li>";
               }
               ?>
            </ul>
        </div>
      </div>  
    </nav>

