
<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods,X-Requested-With");

require_once "../../config/Database.php";
require_once "../../models/Users.php";
require_once "../../models/HttpResponse.php";

$db = new Database();
$user = new Users($db);
$http = new HttpResponse();


// CHECK INCOMING GET REQUESTS
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userReceived = json_decode(file_get_contents("php://input"));
    if((!$userReceived->email) || (!$userReceived->password)){
    $http->badRequest("Email and Password should be provided");
        exit();
    }else{
        
    $email = $userReceived->email;
    $password = $userReceived->password;
    $pass = md5($password); 
    //$query ="SELECT * FROM users WHERE userName =? AND password =?";
    $results = $user->fetchLoggedUser($email,$pass);
    if ($results === 0 ) {
        $message = "No User with the credentials was found";
        $http->notFound($message);

    } else {
        $http->OK($results);
    }
    }
    
} 