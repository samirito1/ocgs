<?php 
class HttpResponse {
  public function badRequest($message) {
    http_response_code(400);
    echo json_encode([
      "date" => date("d/m/Y h:i:s:a"),
      "version" => "1.0.0",
      "error_type"=> "Invalid Parameter",
      "message" => $message,
    ]);
  }


  public function notFound($message) {
    http_response_code(404);
    echo json_encode([
      "date" => date("d/m/Y h:i:s:a"),
      "version" => "1.0.0",
      "error_type"=> "Not Found",
      "message" => $message,
    ]);
  }

  public function notAuthorized($message) {
    http_response_code(401);
    echo json_encode([
      "date" => date("d/m/Y h:i:s:a"),
      "version" => "1.0.0",
      "error_type"=> "Unauthorized",
      "message" => $message,
    ]);
  }
  public function OK( $resultsData) {
    http_response_code(200);
    echo json_encode([
      "date_time" => date("d/m/Y h:i:s:a"),
      "version" => "1.0.0",
      "data" => $resultsData,
    ]);
  }


  
}