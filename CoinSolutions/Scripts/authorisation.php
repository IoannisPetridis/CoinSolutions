<?
function checkAuthorised() {
    $pass = isset($_GET['pass']) ? $_GET['pass'] : '';
    if (($pass=='')||($pass!='masterpass')) {
        echo "Unauthorised access, terminating...";
        exit();
    }
}
?>