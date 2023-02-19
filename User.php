<?php

session_start();

class User
{
    private $id;
    public $login;
    public $email;
    public $firstname;
    public $lastname;
    private $conn;

    public function __construct() {
        $db_host = 'localhost';
        $db_username = 'root';
        $db_password = '';
        $db_name = 'classes';

        $this->conn = new mysqli($db_host, $db_username, $db_password, $db_name);

        if($this->conn->connect_error){
            die('Erreur : ' . $this->conn->connect_error);
        }
        echo 'Connection à la bdd réussie<br>';
    }

    public function Register($login, $password, $passwordConfirm, $email, $firstname, $lastname) {

        $sql = "Select * from utilisateurs where login='$login'";
        
        $result = $this->conn->query($sql);
        $row = $result->num_rows;
        
        
        if($row <= 0) {

            if(($password == $passwordConfirm)) { 
                
                $hash = password_hash($password, PASSWORD_DEFAULT);

                $sql = "INSERT INTO `utilisateurs` (`login`, `password`, `email`, `firstname`, `lastname`) VALUES ('$login', '$hash', '$email','$firstname', '$lastname')";
        
                $result = $this->conn->query($sql);
        
                if ($result) {  
                    echo '<strong>Success!</strong> Your account is now created and you can login.';
                    
                    $userData = [
                        'login' => $login,
                        'password' => $hash,
                        'email' => $email,
                        'firstname' => $firstname,
                        'lastname' => $lastname,
                    ];

                    return $userData;

                }

            }else{ echo "Passwords do not match"; }
            
        }else{ echo '<strong>Error!</strong> The login already exist.'; }

    }

    public function Connect($login, $password) {

        $sql = "select * from utilisateurs where login = '$login'";

        $result = $this->conn->query($sql);
        $row = $result->num_rows;
        
        if($row == 1){    

            $row = $result->fetch_assoc();
            $dataPass = $row['password'];
            $id = $row['id'];

            if(password_verify($password,$dataPass)){  

                $_SESSION['id'] = $id;
                $_SESSION['login'] = $login;
                $_SESSION['password'] = $dataPass;
                $_SESSION['email'] = $row['email'];
                $_SESSION['firstname'] = $row['firstname'];
                $_SESSION['lastname'] = $row['lastname'];

                $this->id = $_SESSION['id'];
                $this->login = $_SESSION['login'];
                $this->email = $_SESSION['email'];
                $this->firstname = $row['firstname'];
                $this->lastname = $row['lastname'];


                echo '<strong>Success!</strong> You\'re connected';

            }else{  
                echo '<strong>Error!</strong> Wrong password';
            }
            echo '<strong>Error!</strong> The login do not exist. You don\'t have an account? <a href=\"inscription.php\">Signup</a>';
        }

    }

    public function Disconnect() {

        session_destroy();
        exit('Vous avez bien été déconnecté');

    }

    public function Delete() {

        if($_SESSION){

            $sessionId = $_SESSION['id'];

            $sql = "DELETE FROM `utilisateurs` WHERE id = '$sessionId'";
            $result = $this->conn->query($sql);

            session_destroy();
            exit('<strong>Success!</strong> You have deleted your account');


        }else{
            echo 'Please login to delete your account<br>';
        }

    }

    public function Update($login, $password, $passwordNew, $passwordNewConfirm, $email, $firstname, $lastname) {

        if ($_SESSION){

            $sessionId = $_SESSION['id'];
            $passwordTrue = $_SESSION['password'];

            $sql = "SELECT * FROM utilisateurs WHERE id = '$sessionId'";
            $result = $this->conn->query($sql);
            $row = $result->num_rows;

            if(password_verify($password,$passwordTrue)){

                if ($_SESSION['login'] != $login){

                    if($row!=1){
        
                        echo '<strong>Error!</strong> The login already exist';
        
                    }else{
        
                        $sqlLog = "UPDATE utilisateurs SET login = '$login' WHERE id = '$sessionId'";
                        $rs = $this->conn->query($sqlLog);
        
                        $_SESSION['login'] = $login;
                        $this->login = $login;
        
                        echo '<strong>Success!</strong> Your login has been edited<br>';
        
                    }
        
                }

                if(!empty($passwordNew) && !empty($passwordNewConfirm) && $passwordNew == $passwordNewConfirm){

                    $hashNew = password_hash($passwordNewConfirm, PASSWORD_DEFAULT);

                    $sqlPass = "UPDATE utilisateurs SET password = '$hashNew' WHERE id = '$sessionId'";
                    $rs = $this->conn->query($sqlPass);
                    $_SESSION['password'] = $hashNew;
                    echo '<strong>Success!</strong> Your password has been edited<br>';

                }elseif (!empty($passwordNew) && empty($passwordNewConfirm)){

                    echo "<strong>Error!</strong> Please confirm password<br>";

                }elseif($passwordNew != $passwordNewConfirm){

                    echo '<strong>Error</strong> The passwords are differents<br>';

                }

                if ($_SESSION['email'] != $email){

                    $sqlMail = "UPDATE utilisateurs SET email = '$email' WHERE id = '$sessionId'";
                    $rs = $this->conn->query($sqlMail);

                    $_SESSION['email'] = $email;
                    $this->email = $email;

                    echo '<strong>Success!</strong> Your email has been edited<br>';

                }
                    
                if ($_SESSION['firstname'] != $firstname){

                    $sqlFirstN = "UPDATE utilisateurs SET firstname = '$firstname' WHERE id = '$sessionId'";
                    $rs = $this->conn->query($sqlFirstN);

                    $_SESSION['firstname'] = $firstname;
                    $this->firstname = $firstname;

                    echo '<strong>Success!</strong> Your first name has been edited<br>';

                }
                    
                if ($_SESSION['lastname'] != $lastname){

                    $sqlLastN = "UPDATE utilisateurs SET lastname = '$lastname' WHERE id = '$sessionId'";
                    $rs = $this->conn->query($sqlLastN);

                    $_SESSION['lastname'] = $lastname;
                    $this->lastname = $lastname;

                    echo '<strong>Success!</strong> Your last name has been edited<br>';

                }

            }else{ echo '<strong>Error!</strong> Wrong password<br>'; }

        }else{ echo '<strong>Error!</strong> Please login to change your infos'; }
        
    }

    public function IsConnected() {

        if($_SESSION){
            return true;
        }else{
            return false;
        }

    }

    public function GetAllInfos() {

        if($_SESSION){
            return $_SESSION;
        }else{
            echo 'Please login to view your infos<br>';
        }

    }

    public function GetLogin() {

        if($_SESSION){
            return $_SESSION['login'];
        }else{
            echo 'Please login to view your login<br>';
        }

    }

    public function GetEmail() {

        if($_SESSION){
            return $_SESSION['email'];
        }else{
            echo 'Please login to view your email<br>';
        }

    }

    public function GetFirstname() {

        if($_SESSION){
            return $_SESSION['firstname'];
        }else{
            echo 'Please login to view your first name<br>';
        }

    }

    public function GetLastname() {

        if($_SESSION){
            return $_SESSION['lastname'];
        }else{
            echo 'Please login to view your last name<br>';
        }

    }

}

$newUser = new User();


?>