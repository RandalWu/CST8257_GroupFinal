<?php
    include "./Common/Header.php";
    if (!file_exists('Users')) {
        mkdir('Users', true);
        chmod('Users', 0777);
    }
//Security on FriendPictures page//
unset($_SESSION['friendID']);
unset($_SESSION['friendName']);
unset($_SESSION['friendNameStripped']);
unset($_SESSION['confirmedFriend']);
///////////////////////////////////
    if (isset($_SESSION['selectedID'])) {
        unset($_SESSION['selectedID']);
    }
    
     if (isset($_SESSION['selectedID2'])) {
        unset($_SESSION['selectedID2']);
    }


?>
    <div class="container">

        <h1 align="center">Welcome to Algonquin Social Media Website</h1>
        <hr>
        <div  align="left">
            <h4> If you have never used this before, you have to <a href="NewUser.php">SIGN UP</a> first.</h4>
            <br>
            <h4> If you have already have an account, please <a href="Login.php">LOGIN</a> here.</h4>
            <br>
        </div>


    </div>


<?php 
    include "./Common/Footer.php"
?>