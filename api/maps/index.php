<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods,X-Requested-With");

require_once "../../config/Database.php";
require_once "../../models/Maps.php";
require_once "../../models/HttpResponse.php";

$db = new Database();
$map = new Maps($db);
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
          $http->badRequest("Only a valid integer is allowed to fetch a single Map");
          die();
      }
      // FETCH ONE MAPS IF ID EXISTS OR ALL IF ID DOESN'T EXIST
      $resultsData = isset($_GET['id']) ? $map->fetchOneMap($_GET['id']) : $map->fetchAllMaps();
  
      if ($resultsData === 0) {
          $message = "No Map ";
          $message .= isset($_GET['id']) ? "with the id " . $_GET['id'] : "";
          $message .= " was found";
          $http->notFound($message);
      }else {
        $http->OK($resultsData);
      }
  } else if ($_SERVER['REQUEST_METHOD'] === "POST") {
      $mapReceived = json_decode(file_get_contents("php://input"));
      $results = $map->insertMap($mapReceived);
      if ($results === -1) {
        $http->badRequest("A valid JSON of Map fields is required");
      }else {
        $http->OK($results);
      }
  } else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $mapReceived = json_decode(file_get_contents("php://input"));
    if (!$mapReceived->id) {
      // POST ID NOT PROVIDED BAD REQUEST
      $http->badRequest("Please an id is required to make a PUT request");
      exit();
    }
    $query = "SELECT * FROM maps WHERE id = ?";
    $results = $db->fetchOne($query, $mapReceived->id);
    if ($results === 0) {
      // Post NOT Found
      $http->notFound("Map with the id $mapReceived->id was not found");
    }else {
      // MAP CAN UPDATE
      $parameters = [
        'id' => $mapReceived->id,
        'map_type' => isset($mapReceived->map_type) ? $mapReceived->map_type : $results['map_type'],
        'map_category' => isset($mapReceived->map_category) ? $mapReceived->map_category : $results['map_category'],
        'paper_size' => isset($mapReceived->paper_size) ? $mapReceived->paper_size : $results['paper_size'],
        'price' => isset($mapReceived->price) ? $mapReceived->price : $results['price'],
      ];
  
      $resultsData = $map->updateMap($parameters);
        $http->OK($resultsData);
      
    }
  } else if ($_SERVER['REQUEST_METHOD'] === "DELETE") {
    $idReceived = json_decode(file_get_contents("php://input"));
    if (!$idReceived->id) {
      $http->badRequest("No id was provided");
      exit();
    }
    $query = "SELECT * FROM maps WHERE id = ?";
    $results = $db->fetchOne($query, $idReceived->id);
  
    if ($results === 0) {
      // POST NOT FOUND
      $http->notFound("Map with the id $idReceived->id was not found");
      exit();
    }
    else {
      // Maps CAN NOW DELETE MAP
      $resultsData = $map->deleteMap($idReceived->id);
  
        $http->OK( $resultsData);
    }
  
  
  }