<h1>Admin Panel</h1>
<h3>Manage Members</h3>
<div class="container">
    <h3 class='text-center'>Click on member row to edit</h3>
    <!-- show a list of members -->
    
    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">User Id</th>
                <th scope="col">Mbr_id</th>
                <th scope="col">Name</th>
                <th scope="col">eMail</th>
                <th scope="col">Status</th>
                <th scope="col">Type</th>
                <th scope="col">Sign up Date</th>
                <th scope="col">Last Updted</th>
            </tr>
        </thead>
        <tbody>
            <?php getMbrList($pdo); ?>
        </tbody> 
    </table>       
</div>

<?php
if (isset($goto)) {
   echo "<script>"; 
   echo "var elm = document.getElementById('".$goto."');";
   echo "elm.scrollIntoView(true);"; 
   echo "</script>"; 
}
?>


