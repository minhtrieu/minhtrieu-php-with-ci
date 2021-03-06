<?php
/**
 * User Model class
 * @author Tuan Duong <bacduong@gmail.com>
 * @package Pingo - CI
 */

class Users extends PingoModel
{
    public function __construct()
    {
        parent::__construct();
        $this->tableName = 'users';
    }

    /**
     * [getUserByEmail description]
     * @param  String $email Email of users need to be looked up.
     * @return bool|array Array of user information
     */
    public function getUserByEmail($email)
    {
        return $this->getByField('email', $email);
    }

    /**
     * [getUserById description]
     * @param  int $id User Id
     * @return array Array of user information
     */
    public function getUserById($id)
    {
        return $this->getById($id);
    }

    /**
     * [updateUser description]
     * @param  array  $user Array of user information
     * @return bool
     */
    public function updateUser(array $user)
    {
        return $this->update($user);
    }

    /**
     * [createUser description]
     * @param  array  $user Array of new user informaton
     * @return bool | int Id of created user.
     */
    public function createUser(array $user)
    {
        if (!isset($user['email']) || !isset($user['password'])) {
            return false;
        }

        if ($this->getUserByEmail($user['email']) !== false) {
            return false;
        }
        $user['password'] = md5($user['password']);
        return $this->create($user);
    }

    public function saveUser(array $user)
    {
        if (isset($user['id'])) {
            return $this->updateUser($user);
        } else {
            return $this->createUser($user);
        }
    }

    /**
     * Login with email and password
     * @param  string  $email    Email of user
     * @param  string  $password Password
     * @param  boolean $remember Remember me in 30 days for example
     * @return boolean True of false
     */
    public function doLogin($email, $password, $remember = false)
    {
        if ($email == '' || $password == '') {
            return false;
        }

        $user = $this->getUserByEmail($email);
        if ($user === false) {
            return false;
        }

        $encryptedPassword = md5($password);
        if ($user['password'] != $encryptedPassword) {
            return false;
        }

        //Save user info into session
        $data = array(
            'email'      => $email,
            'userId'     => $user['id'],
            'fullname' => $user['fullname']
            );
        $this->session->set_userdata($data);
        return true;
    }

    public function isLogged()
    {
        $userId = $this->session->userdata('userId');
        return !empty($userId);
    }

    public function likeTip($userId, $tipId)
    {
        $userId = intval($userId);
        $tipId = intval($tipId);
        if ($userId === 0 || $tipId === 0) {
            return false;
        }

        $sql = "INSERT INTO user_like (user_id, tip_id) VALUES (?, ?)";
        return $this->db->query($sql, array($userId, $tipId));
    }
}
?>