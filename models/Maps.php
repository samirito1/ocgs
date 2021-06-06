<?php
class Maps {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function fetchAllMaps() {
        $query = "SELECT * FROM maps";
        return $this->db->fetchAll($query);
    }
    public function fetchOneMap($parameter) {
        $query = "SELECT * FROM maps
        WHERE id = ?";
        return $this->db->fetchOne($query, $parameter);
    }

    public function insertMap($parameters) {
        $query = "INSERT INTO maps (map_type,map_category,paper_size,price)
        VALUES (?,?,?,?)";
        if (isset($parameters->map_type) && isset($parameters->map_category) &&  isset($parameters->paper_size) && isset($parameters->price)) {
            $map_type = $parameters->map_type;
            $map_category = $parameters->map_category;
            $paper_size = $parameters->paper_size;
            $price = $parameters->price;
            $this->db->insertMap($query,$map_type,$map_category,$paper_size,$price);
            return $parameters;
        }else {
          return -1;
        }
        
    }
    
    public function updateMap($parameters) {
        $query = "UPDATE maps SET
        map_type =?,
        map_category=?,
        paper_size=?,
        price=?,
        WHERE id = ?";
        if (isset($parameters['map_type']) && isset($parameters['map_category']) && isset($parameters['paper_size']) && isset($parameters['price'])) {
            $id = $parameters['id'];
            $map_type = $parameters->map_type;
            $map_category = $parameters->map_category;
            $paper_size = $parameters->paper_size;
            $price = $parameters->price;
            $results = $this->db->updateMap($query,$map_type,$map_category,$paper_size,$price,$id);
            return $parameters;
        } else {
            return -1;
        }
    }
    public function deleteMap($id) {
        $query = "DELETE FROM maps WHERE id = ?";
        $results = $this->db->deleteOne($query, $id);
        return [
            "message" => "Map with the id $id was successfully deleted",
        ];
    }

}