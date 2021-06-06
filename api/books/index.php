<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods,X-Requested-With");

require_once "../../config/Database.php";
require_once "../../models/Books.php";
require_once "../../models/HttpResponse.php";

$db = new Database();
$book = new Books($db);
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
          $http->badRequest("Only a valid integer is allowed to fetch a single Book");
          die();
      }
      // FETCH ONE BOOK IF ID EXISTS OR ALL IF ID DOESN'T EXIST
      $resultsData = isset($_GET['id']) ? $book->fetchOneBook($_GET['id']) : $book->fetchAllBooks();
  
      if ($resultsData === 0) {
          $message = "No Book ";
          $message .= isset($_GET['id']) ? "with the id " . $_GET['id'] : "";
          $message .= " was found";
          $http->notFound($message);
      }else {
        $http->OK($resultsData);
      }
  } else if ($_SERVER['REQUEST_METHOD'] === "POST") {
      $bookReceived = json_decode(file_get_contents("php://input"));
      $results = $book->insertBook($bookReceived);
      if ($results === -1) {
        $http->badRequest("A valid JSON of Book fields is required");
      }else {
        $http->OK($results);
      }
  } else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $bookReceived = json_decode(file_get_contents("php://input"));
    if (!$bookReceived->id) {
      // POST ID NOT PROVIDED BAD REQUEST
      $http->badRequest("Please an id is required to make a PUT request");
      exit();
    }
    $query = "SELECT * FROM books WHERE id = ?";
    $results = $db->fetchOne($query, $bookReceived->id);
    if ($results === 0) {
      // Post NOT Found
      $http->notFound("Book with the id $bookReceived->id was not found");
    }else {
      // BOOK CAN UPDATE
      $parameters = [
        'id' => $bookReceived->id,
        'name' => isset($bookReceived->name) ? $bookReceived->name : $results['name'],
        'description' => isset($bookReceived->description) ? $bookReceived->description : $results['description'],
        'price' => isset($bookReceived->price) ? $bookReceived->price : $results['price'],
      ];
  
      $resultsData = $book->updateBook$parameters);
        $http->OK($resultsData);
      
    }
  } else if ($_SERVER['REQUEST_METHOD'] === "DELETE") {
    $idReceived = json_decode(file_get_contents("php://input"));
    if (!$idReceived->id) {
      $http->badRequest("No id was provided");
      exit();
    }
    $query = "SELECT * FROM books WHERE id = ?";
    $results = $db->fetchOne($query, $idReceived->id);
  
    if ($results === 0) {
      // POST NOT FOUND
      $http->notFound("Book with the id $idReceived->id was not found");
      exit();
    }
    else {
      // Books CAN NOW DELETE BOOK
      $resultsData = $book->deleteBook($idReceived->id);
  
        $http->OK( $resultsData);
    }
  
  
  }