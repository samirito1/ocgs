<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With");

require_once "../../config/Database.php";
require_once "../../models/Users.php";
require_once "../../models/HttpResponse.php";

$db = new Database();
$user = new Users($db);
$http = new HttpResponse();


// if ($_SERVER['REQUEST_METHOD'] !== 'OPTIONS') {
// if (!isset($_SERVER['PHP_AUTH_USER']) && !isset($_SERVER['PHP_AUTH_PW'])) {
//     $http->notAuthorized("You must authenticate yourself before you can use our REST API services");
//     exit();
// } else {
//     $username = $_SERVER['PHP_AUTH_USER'];
//     $password = $_SERVER['PHP_AUTH_PW'];
//    // $pass = md5($password);
//     $query = "SELECT * FROM users WHERE username = ?";
//     $results = $db->fetchOne($query, $username);

//     if ($results === 0 || $results['password'] !== $password) {
//         $http->notAuthorized("You provided wrong credentials");
//         exit();
//     } else {
//         $user_id = $results['id'];
//     }
// }
// }
// CHECK INCOMING GET REQUESTS
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id']) && !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
        // ERROR ONLY INTEGER IS ALLOWED
        $http->badRequest("Only a valid integer is allowed to fetch a single User");
        die();
    }
    // FETCH ONE USER IF ID EXISTS OR ALL IF ID DOESN'T EXIST
    $resultsData = isset($_GET['id']) ? $user->fetchOneUser($_GET['id']) : $user->fetchAllUsers();

    if ($resultsData === 0) {
        $message = "No User ";
        $message .= isset($_GET['id']) ? "with the id " . $_GET['id'] : "";
        $message .= " was found";
        $http->notFound($message);
    }else {
      $http->OK($resultsData);
    }
} else if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $userReceived = json_decode(file_get_contents("php://input"));
    $results = $user->insertUser($userReceived);
    if ($results === -1) {
      $http->badRequest("A valid JSON of User fields is required");
    }else {
      $http->OK($results);
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
  $userReceived = json_decode(file_get_contents("php://input"));
  if (!$userReceived->id) {
    // POST ID NOT PROVIDED BAD REQUEST
    $http->badRequest("Please an id is required to make a PUT request");
    exit();
  }
  $query = "SELECT * FROM users WHERE id = ?";
  $results = $db->fetchOne($query, $userReceived->id);
  if ($results === 0) {
    // Post NOT Found
    $http->notFound("User with the id $userReceived->id was not found");
  }else {
    // USER CAN UPDATE
    $parameters = [
      'id' => $userReceived->id,
      'user_type' => isset($userReceived->user_type) ? $userReceived->user_type : $results['user_type'],
      'fullname' => isset($userReceived->fullname) ? $userReceived->fullname : $results['fullname'],
      'email' => isset($userReceived->email) ? $userReceived->email : $results['email'],
      'mobile' => isset($userReceived->mobile) ? $userReceived->mobile : $results['mobile'],
      'gender' => isset($userReceived->gender) ? $userReceived->gender : $results['gender']
      
    ];

    $resultsData = $user->updateUser($parameters);
      $http->OK($resultsData);
    
  }
} else if ($_SERVER['REQUEST_METHOD'] === "DELETE") {
  $idReceived = json_decode(file_get_contents("php://input"));
  if (!$idReceived->id) {
    $http->badRequest("No id was provided");
    exit();
  }
  $query = "SELECT * FROM users WHERE id = ?";
  $results = $db->fetchOne($query, $idReceived->id);

  if ($results === 0) {
    // POST NOT FOUND
    $http->notFound("User with the id $idReceived->id was not found");
    exit();
  }
  else {
    // User CAN NOW DELETE USER
    $resultsData = $user->deleteUser($idReceived->id);

      $http->OK( $resultsData);
  }


}