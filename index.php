<?php
    include "./Common/Header.php";
    
    if (!file_exists('Users')) {
        mkdir('Users', true);
        chmod('Users', 0755);
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