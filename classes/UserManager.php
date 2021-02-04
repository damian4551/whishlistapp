<?php

class UserManager {

    public function userValidation() {
        $args = [
            'username' => [
                'filter' => FILTER_VALIDATE_REGEXP,
                'options' => ['regexp' => '/^[a-zA-Z0-9]{5,}$/']
            ],
            'password' => [
                'filter' => FILTER_VALIDATE_REGEXP,
                'options' => ['regexp' => '/.{6,25}/']
            ],
        ];

        $data = filter_input_array(INPUT_POST, $args);

        $errors = "";

        foreach ($data as $key => $val) {
            if ($val === false or $val === NULL) {
            $errors .= $key . " ";
            }
        }

        if ($errors === "") {
            $user_array = array(
                'username' => $data['username'],
                'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                'register_date' => date("Y-m-d H:i:s"),
            );
            return $user_array;
        } else {
            return 'error-data';
        }

        
    }

    public function addUserToDB($db) {
        $user_array = $this->userValidation();
        if($user_array == 'error-data') {
            return $user_array;
        } else {

            $username = $user_array['username'];
            $password = $user_array['password'];
            $register_date = $user_array['register_date'];

            $is_user = $db->selectElements("SELECT * FROM users WHERE username = '$username'");

            //add to database if $is_user is empty array
            if(count($is_user) == 0) {

                $db->insert("INSERT INTO users (user_id, username, password, register_date) VALUES (NULL, '$username', '$password', '$register_date')");

                $user_id = $db->selectElements("SELECT user_id FROM users WHERE username = '$username'");

                $user_id = $user_id[0]->user_id;
                
                //log in user after register
                session_start();
                $last_update = date("Y-m-d H:i:s");
                $session_id = session_id();
                $db->insert("INSERT INTO logged_in_users (session_id, user_id, last_update) VALUES ('$session_id', '$user_id', '$last_update')");
                session_destroy();

            } else {
                return 'error-user-exist';
            }

        }

    }

    public function loginUser($db, $username, $password) {
        $user_id = -1;
        
        $user_id = $db->selectUser($username, $password, "users");
        
        if ($user_id >= 0) {
            //session start and delete last user's session from db
            session_start();
            $db->delete("DELETE FROM logged_in_users WHERE user_id=$user_id");

            //insert data to logged_in_users table
            $last_update = date("Y-m-d H:i:s");
            $session_id = session_id();
            $db->insert("INSERT INTO logged_in_users (session_id, user_id, last_update) VALUES ('$session_id', '$user_id', '$last_update')");
            session_destroy();
        }
        
        return $user_id;
        
    }

    public function logoutUser($db) {
        //session and session cookie delete
        session_start();
        $session_id = session_id();
        setcookie ("PHPSESSID", "", time() - 3600, '/');
        $_SESSION = array();
        session_destroy();

        $db->delete("DELETE FROM logged_in_users WHERE session_id='$session_id'");
    }

    public function getLoggedInUser($db, $session_id) {
        $user_id = $db->select("SELECT * FROM logged_in_users WHERE session_id='$session_id'", 'user_id');

        return $user_id;
    }

}