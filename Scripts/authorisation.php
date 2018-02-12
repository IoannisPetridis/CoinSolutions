<?
function checkAuthorised() {
    $pass = isset($_GET['pass']) ? $_GET['pass'] : '';
    if (($pass=='')||($pass!='masterpass')) {
        die("Unauthorised access, terminating...");
    }
}
?>