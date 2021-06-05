<?php
class Users {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function fetchAllUsers() {
        $query = "SELECT
        u.id AS user_id,
        ut.id AS user_type_id,
        ut.name AS user_type_name,
        u.fullname,
        u.email,
        u.password,
        u.user_status,
        u.created_date,
        u.mobile,
        u.gender
        FROM users u
        LEFT JOIN 
        user_type ut ON u.user_type =ut.id
        ";
        return $this->db->fetchAll($query);
    }
    public function fetchOneUser($parameter) {
        $query = "SELECT
        u.id AS user_id,
        ut.id AS user_type_id,
        ut.name AS user_type_name,
        u.fullname,
        u.email,
        u.password,
        u.user_status,
        u.created_date,
        u.mobile,
        u.gender
        FROM users u
        LEFT JOIN 
        user_type ut ON u.user_type =ut.id
        WHERE u.id = ?";
        return $this->db->fetchOne($query, $parameter);
    }

    public function insertUser($parameters) {
        $query = "INSERT INTO users (user_type,fullname,email,password,user_status,created_date,mobile,gender)
        VALUES (?,?,?,?,?,?,?,?)";
        if (isset($parameters->user_type) && isset($parameters->fullname) && isset($parameters->email) && isset($parameters->mobile) ) && isset($parameters->gender) ) {
            $user_type = $parameters->user_type;
            $fullname = $parameters->fullname;
            $email = $parameters->email;
            $password = md5("Zanzibar1");
            $user_status = "ACTIVE";
            $created_date = date('Y-m-d');
            $mobile = $parameters->mobile;
            $gender = $parameters->gender;

            $this->db->insertUser($query,$user_type,$fullname,$email,$password,$user_status,$created_date,$mobile,$gender);
            return $parameters;
        }else {
          return -1;
        }
    }
    public function updateUser($parameters) {
        $query = "UPDATE users SET
        user_type = ?,
        fullname =?,
        email=?,
        mobile=?,
        gender=?
        WHERE id = ?";
        if (isset($parameters['user_type']) && isset($parameters['fullname']) && isset($parameters['email']) && isset($parameters['mobile']) && isset($parameters['gender']) && isset($parameters['id'])) {
            $id = $parameters['id'];
            $user_type = $parameters->user_type;
            $fullname = $parameters->fullname;
            $email = $parameters->email;
            $mobile = $parameters->mobile;
            $gender = $parameters->gender;
            $results = $this->db->updateUserType($query,$user_type,$fullname,$email,$mobile,$gender,$id);
            return $parameters;
        } else {
            return -1;
        }
    }
    public function deleteUser($id) {
        $query = "DELETE FROM users WHERE id = ?";
        $results = $this->db->deleteOne($query, $id);
        return [
            "message" => "User with the id $id was successfully deleted",
        ];
    }

}