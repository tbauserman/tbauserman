<?php
require_once 'pdo.php'; 
$db = new DB(); 
class Account {
    public $id; 
    private $name; 
    private $authenticated; 
    public function __construct() {
        $this->id = NULL; 
        $this->email = NULL;
        $this->authenticated = False; 
    }

    public function __destruct() { 

    }
    public function getAccounts($id,$search_term=False) {
        global $db; 
       
        $id = intval($id); 

        
        if($id) {
            $sql="select  
            accounts.account_id as account_id,
            regions.region,  
            first_name,
            last_name,
            account_email, 
            account_enabled, 
            `admin`,
            phone, 
            GROUP_CONCAT(account_types.account_type) as account_type
            FROM
                accounts 
            LEFT JOIN 
                (
                    account_account_types JOIN account_types on account_account_types.account_type_id=account_types.id
                )
            ON 
                accounts.account_id = account_account_types.account_id
            LEFT JOIN 
                regions 
            ON accounts.region_id = regions.id
            WHERE accounts.account_id=:id
            GROUP BY accounts.account_id"; 
            $args = array(":id"=>$id); 
            try{
                $res = $db->run($sql, $args); 
            } catch(PDOException $e) {
                echo $e->getMessage(); 
                throw new Exception("Unable to fetch account."); 
            }
        } else if($search_term) { 
            $search_term = ":search_term";
            $sql_term = "SET @term = :term";
            $stmt_term = $db->pdo->prepare($sql_term); 
            $stmt_term->bindValue(":term","%".$_GET['term']."%",PDO::PARAM_STR); 
            $stmt_term->execute();
            $sql="SELECT 
                accounts.account_id,
                regions.region,
                first_name,
                last_name,
                account_email, 
                account_enabled, 
                `admin`,
                phone,
                GROUP_CONCAT(account_types.account_type) as account_type
                FROM 
                    accounts 
                    LEFT JOIN 
                    (
                        account_account_types JOIN account_types on account_account_types.account_type_id=account_types.id
                    )
                ON 
                    accounts.account_id = account_account_types.account_id
                LEFT JOIN 
                    regions 
                ON accounts.region_id = regions.id
                WHERE 
                    account_email like @term
                    or phone like @term 
                    or first_name like @term
                    or last_name like @term
            GROUP BY accounts.account_id
            ORDER BY regions.region,first_name,last_name
            "; 
            try { 
                $res = $db->run($sql); 
            } catch(PDOException $e) {
                throw new Exception($e->getMessage()); 
            }
        } else { 
            $sql="SELECT 
                id as account_id,
                regions.region,
                first_name,
                last_name,
                account_email, 
                account_enabled, 
                `admin`,
                phone,
                account_types.account_type
                FROM 
                accounts
                LEFT JOIN 
                    account_types 
                ON 
                    accounts.account_type_id = account_types.id
                LEFT JOIN 
                    regions 
                ON accounts.region_id = regions.id"; 
                try { 
                    $res = $db->run($sql); 
                } catch(PDOException $e) {
                    throw new Exception("Unable to fetch account."); 
                }
        }
       return $res->fetchAll(); 
    }
    public function AddAccount(string $first_name,string $last_name,string $email, string $password): int {

        global $db; 
        
        $email = trim($email); 
        $password = trim($password); 
        
        if(!$this->isEmailValid($email)) {
            throw new Exception('Invalid Email Address'); 
        }
        
        if(!$this->isPasswordValid($password)) { 
            throw new Exception('Invalid Password'); 
        }
    
        echo $this->getIdFromEmail($email); 
        if($this->getIdFromEmail($email)) {
            throw new Exception('Email is in use'); 
        }

        $query = "INSERT INTO accounts(first_name,last_name,account_email,account_password) VALUES (:first_name,:last_name,:email,:password,:account_enabled)"; 

        $hash = password_hash($password, PASSWORD_DEFAULT); 
        $args = array(':first_name' => $first_name,':last_name' => $last_name,':email'=>$email, ':password'=>$hash,':account_enabled' => False); 
        
        try {
            $res = $db->run($query,$args); 
        } catch(PDOException $e) { 
            throw new Exception('Error creating account'); 
        }

        return $db->pdo->lastInsertId(); 
    }

    public function isEmailValid(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL); 
    }

    public function isPasswordValid($password) {
        $number = preg_match('@[0-9]@', $password);
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $specialChars = preg_match('@[^\w]@', $password);
        if(strlen($password) < 8 || !$number || !$uppercase || !$lowercase || !$specialChars) {
            return false; 
        } else { 
            return true; 
        }
  
    }

    public function getIdFromEmail($email) {
        global $db; 

        if(!$this->isEmailValid($email)) {
            throw new Exception('Invalid E-Mail');
        }

        $id = NULL; 

        $query ="select account_id from accounts WHERE account_email = :email"; 
        $args = array(':email' => $email); 

        try {
            $res = $db->run($query,$args); 
        } catch (PDOException $e) {
            echo $e->getMessage(); 
            throw new Exception('Database error');
        }
        $row = $res->fetch(); 
        if($row) {
            return intval($row['account_id'],10); 
        } else {
            return false;
        }
    }

    public function editAccount(int $id, string $first_name, string $last_name, string $email, string $password, bool $enabled) {
        global $db; 
        $email = trim($email); 

        if(!$this->isEmailValid($email)) {
            throw new Exception('Invalid EMail'); 
        }
        if(!$this->isPasswordValid($password)) { 
            throw new Exception('Invalid Password'); 
        }

        $idFromName = $this->getIdFromEmail($email);
        if(!is_null($idFromName) && ($idFromName != $id)) {
            throw new Exception('EMail is in use');
        }

        $query = "UPDATE accounts SET first_name = :first_name, last_name = :last_name, account_email = :email, account_password = :password, account_enabled = :enabled WHERE account_id=:account_id"; 
        $hash = password_hash($password,PASSWORD_DEFAULT); 
        $intEnabled - $enabled ? 1 : 0; 
        $args = array(":first_name" => $first_name,":last_name" => $last_name,":email" => $email, ":password" => $hash, ":enabled" => $intEnabled, ":account_id" => $id) ; 
        try { 
            $db->run($query,$args); 
        } catch (PDOException $e) {
            throw new Exception('Database error'); 
        }
    }

    public function deleteAccount(int $id) {
        global $db; 
        $query = "DELETE FROM accounts WHERE account_id = :account_id"; 
        $args = array(":id"=>$id); 
        try { 
            $res = $db->run($query,$args);
        } catch (PDOException $e) { 
            throw new Exception("Database error"); 
        }

        $query = "DELETE FROM account_sessions WHERE account_id = :account_id"; 
        $args = array(":account_id" => $id); 

        try { 
            $res = $db->run($query,$args); 
        } catch (PDOException $e) {
            throw new Exception("Database error."); 
        }
    }

    public function login(string $email, string $password): bool {
        global $db; 
        $email = trim($email); 
        $password = trim($password); 

        if(!$this->isEmailValid($email)) {
            throw new Exception('Invalid EMail'); 
        }
        if(!$this->isPasswordValid($password)) { 
            throw new Exception('Invalid Password'); 
        }

        $query = "SELECT * FROM accounts where account_email = :account_email AND account_enabled = 1"; 
        $args = array(":account_email" => $email); 

        try {
            $res = $db->run($query,$args); 
        } catch (PDOException $e) {
            throw new Exception("Database error"); 
        }

        $row = $res->fetch(); 
        
        if(is_array($row)) {
            if(password_verify($password, $row['account_password'])) {
                $this->id = intval($row['account_id'], 10); 
                $this->admin = intval($row['admin'],1); 
                $this->email = $email; 
                $this->authenticated = TRUE; 
                $this->registerLoginSession(); 
                return TRUE; 
            }
        }
        return FALSE; 
    }

    private function registerLoginSession() {
        global $db; 
        if(session_status() == PHP_SESSION_ACTIVE) { 
            $query = "REPLACE INTO account_sessions(session_id, account_id, login_time) values(:sid, :account_id, now())"; 
            $args = array(":sid" => session_id(), ":account_id"=> $this->id); 

            try {
                $res = $db->run($query,$args); 
            } catch (PDOException $e) { 
                throw new Exception('Database error'); 
            }
        }
    }

    public function sessionLogin(): bool {
        global $db; 
        if(session_status() == PHP_SESSION_ACTIVE) {
            $query = "SELECT * FROM 
                    account_sessions, 
                    accounts 
                WHERE 
                    (account_sessions.session_id = :sid)
                AND (account_sessions.login_time >= (NOW() - INTERVAL 7 DAY))
                AND (account_sessions.account_id = accounts.account_id)
                AND (accounts.account_enabled = 1)";

            $args = array(':sid' => session_id()); 
            try { 
                $res = $db->run($query,$args); 
            } catch (PDOException $e) { 
                throw new Exception('Database error'); 
            }

            $row = $res->fetch(); 

            if(is_array($row)) {
                $this->id = intval($row['account_id'],10); 
                $this->email = $row['account_email']; 
                $this->authenticated = TRUE; 
                return TRUE; 
            }
        }
        return FALSE; 
    }

    public function logout() {
        global $db; 
        
        if(is_null($this->id)) {
            return; 
        }

        $this->id = NULL; 
        $this->email = NULL; 
        $this->authenticated = FALSE; 

        if(session_status() == PHP_SESSION_ACTIVE) {
            $query = "DELETE FROM account_sessions WHERE (session_id = :sid)"; 
            $args = array(":sid" => session_id()); 
            try {
                $res = $db->run($query,$args);
            } catch (PDOException $e) {
                throw new Exception("Database error"); 
            }
        }
    }

    public function isAuthenticated(): bool { 
        return $this->authenticated; 
    }
}
?>