<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With");

require_once "../../config/Database.php";
require_once "../../models/Services.php";
require_once "../../models/HttpResponse.php";

$db = new Database();
$services = new Services($db);
$http = new HttpResponse();


if ($_SERVER['REQUEST_METHOD'] !== 'OPTIONS') {
if (!isset($_SERVER['PHP_AUTH_USER']) && !isset($_SERVER['PHP_AUTH_PW'])) {
    $http->notAuthorized("You must authenticate yourself before you can use our REST API services");
    exit();
} else {
    $email = $_SERVER['PHP_AUTH_USER'];
    $password = $_SERVER['PHP_AUTH_PW'];
   // $pass = md5($password);
    $query = "SELECT * FROM users WHERE email = ?";
    $results = $db->fetchOne($query, $email);

    if ($results === 0 || $results['password'] !== $password) {
        $http->notAuthorized("You provided wrong credentials");
        exit();
    } else {
        $user_id = $results['id'];
    }
}
}
// CHECK INCOMING GET REQUESTS
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id']) && !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
        // ERROR ONLY INTEGER IS ALLOWED
        $http->badRequest("Only a valid integer is allowed to fetch a single service");
        die();
    }
    // FETCH ONE USER IF ID EXISTS OR ALL IF ID DOESN'T EXIST
    $resultsData = isset($_GET['id']) ? $services->fetchOneService($_GET['id']) : $services->fetchAllServices();

    if ($resultsData === 0) {
        $message = "No Service ";
        $message .= isset($_GET['id']) ? "with the id " . $_GET['id'] : "";
        $message .= " was found";
        $http->notFound($message);
    }else {
      $http->OK($resultsData);
    }
} else if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $serviceReceived = json_decode(file_get_contents("php://input"));
    $results = $services->insertServices($serviceReceived);
    if ($results === -1) {
      $http->badRequest("A valid JSON of service fields is required");
    }else {
      $http->OK($results);
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
  $serviceReceived = json_decode(file_get_contents("php://input"));
  if (!$serviceReceived->id) {
    // POST ID NOT PROVIDED BAD REQUEST
    $http->badRequest("Please an id is required to make a PUT request");
    exit();
  }
  $query = "SELECT * FROM services WHERE id = ?";
  $results = $db->fetchOne($query, $serviceReceived->id);
  if ($results === 0) {
    // Post NOT Found
    $http->notFound("Service with the id $serviceReceived->id was not found");
  }else {
    // USER CAN UPDATE
    $parameters = [
      'id' => $serviceReceived->id,
      'service_name' => isset($serviceReceived->service_name) ? $serviceReceived->service_name : $results['service_name'],
      'gfs_code' => isset($serviceReceived->gfs_code) ? $serviceReceived->gfs_code : $results['gfs_code']
    ];

    $resultsData = $services->updateservices($parameters);
      $http->OK($resultsData);
    
  }
} else if ($_SERVER['REQUEST_METHOD'] === "DELETE") {
  $idReceived = json_decode(file_get_contents("php://input"));
  if (!$idReceived->id) {
    $http->badRequest("No id was provided");
    exit();
  }
  $query = "SELECT * FROM services WHERE id = ?";
  $results = $db->fetchOne($query, $idReceived->id);

  if ($results === 0) {
    // POST NOT FOUND
    $http->notFound("Service with the id $idReceived->id was not found");
    exit();
  }
  else {
    // User CAN NOW DELETE USER
    $resultsData = $services->deleteservices($idReceived->id);

      $http->OK( $resultsData);
  }


}