<?php
    include "./Common/Header.php";
    include "./Common/ValidationFunctions.php";
    
    if (!isset($_SESSION['loggedInUser'])) {
        $_SESSION["fromPage"]= "MyAlbums";
        header('Location: index.php');
    }
   ?> 

<h1 align="center">My Albums</h1>
<p>Welcome <b><?php echo $_SESSION['loggedInUser']->getName(); ?></b>! (not you? change user <a href="Login.php">here</a>)</p>

<form method="post" class="form-horizontal" action="<?php $_SERVER["PHP_SELF"]; ?>">
   
    <table class="table" name="table">
        <tr>
            <th>Title </th>
            <th>Date Updated </th>
            <th>Number of Pictures </th>
            <th>Accessibility </th>
        </tr>
        
         <?php
//        foreach ($semesters as $semester) {
//            foreach ($registrations as $reg) {
//                if ($reg->getSem() == $semester->getCode()) {
//                    print("<tr><td>" . $reg->getYear() . "</td>");
//                    print("<td>" . $reg->getTerm() . "</td>");
//                    print("<td>" . $reg->getCode() . "</td>");
//                    print("<td>" . $reg->getTitle() . "</td>");
//                    print("<td><a href="Login.php"></tr>");
//                }
//                
//            }
//            <button type="submit" name="delete" value="delete" onclick="return confirm('All the pictures in this album will be delelted')">Delete Selected</button>
//        }
        ?>
        
    </table>
    
    
    <div class="form-group"> 
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" name="saveBtn" class="btn btn-default">Save Changes</button>
        </div>
    </div>
    
</form>

<?php
    include "./Common/Footer.php"; 

