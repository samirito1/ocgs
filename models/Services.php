<?php
class Services {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function fetchAllServices() {
        $query = "SELECT
        *
        FROM services";
        return $this->db->fetchAll($query);
    }
    public function fetchOneService($parameter) {
        $query = "SELECT
        *
        FROM services 
        WHERE id = ?";
        return $this->db->fetchOne($query, $parameter);
    }

    public function insertServices($parameters) {
        $query = "INSERT INTO services (service_name,gfs_code)
        VALUES (?,?)";
        if (isset($parameters->service_name) && isset($parameters->gfs_code) ) {
            $service_name = $parameters->service_name;
            $gfs_code = $parameters->gfs_code;
            $this->db->insertservices($query, $service_name, $gfs_code);
            return $parameters;
        }else {
          return -1;
        }
    }
    public function updateservices($parameters) {
        $query = "UPDATE services SET 
        service_name = ?,
        gfs_code = ?
        WHERE id = ?";
        if (isset($parameters['service_name']) && isset($parameters['gfs_code']) && isset($parameters['id']) ) {
            $id = $parameters['id'];
            $service_name = $parameters['service_name'];
            $gfs_code = $parameters['gfs_code'];
            $results = $this->db->updateservices($query,$service_name,$gfs_code,$id);
            return $parameters;
        } else {
            return -1;
        }
    }
    public function deleteservices($id) {
        $query = "DELETE FROM services WHERE id = ?";
        $results = $this->db->deleteOne($query, $id);
        return [
            "message" => "Service with the id $id was successfully deleted",
        ];
    }

}