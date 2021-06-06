<?php
class Books {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function fetchAllBooks() {
        $query = "SELECT * FROM books";
        return $this->db->fetchAll($query);
    }
    public function fetchOneBook($parameter) {
        $query = "SELECT * books WHERE id = ?";
        return $this->db->fetchOne($query, $parameter);
    }

    public function insertBook($parameters) {
        $query = "INSERT INTO book (name,description,price)
        VALUES (?,?,?)";
        if (isset($parameters->name) && isset($parameters->description) && isset($parameters->price)) {
            $name = $parameters->name;
            $description = $parameters->description;
            $price = $parameters->price;
            $this->db->insertBook($query,$name,$description,$price);
            return $parameters;
        }else {
          return -1;
        }

        
    }
    
    public function updateBook($parameters) {
        $query = "UPDATE books SET
        name =?,
        description=?,
        price=?,
        WHERE id = ?";
        if (isset($parameters['name']) && isset($parameters['description']) && isset($parameters['price'])) {
            $id = $parameters['id'];
            $name = $parameters->name;
            $description = $parameters->description;
            $price = $parameters->price;
            $results = $this->db->updateBook($query,$name,$description,$price,$id);
            return $parameters;
        } else {
            return -1;
        }
    }
    public function deleteBook($id) {
        $query = "DELETE FROM books WHERE id = ?";
        $results = $this->db->deleteOne($query, $id);
        return [
            "message" => "Book with the id $id was successfully deleted",
        ];
    }

}