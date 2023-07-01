<?
function connectToDb($host,$username, $password, $dbName, $port){
    $link = mysqli_connect($host,$username, $password, $dbName, $port) or die("Could net establish connection with server");
    mysqli_query($link, "set names 'utf8'");
    return $link;
}