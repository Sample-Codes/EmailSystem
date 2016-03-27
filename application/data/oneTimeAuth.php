<?php
    class oneTimeAuth
    {
        private $db;

        public function __construct(PDO $db)
        {
            $this->db = $db;
        }

        public function remember($user_id, $expire = null)
        {
            $sql = 'INSERT INTO OneTimeAuth (token, user_id, expire)'
                    . 'VALUES (:token, :user_id, :expire)';
            $stmt = $this->db->prepare($sql);

            while (true) {
                try {
                    $stmt->execute(array(
                        ':token' => $token = $this->generateToken(),
                        'user_id' => $user_id,
                        'expire' => $expire
                    ));
                    break;
                } catch (PDOException $e) {}
            }
            return $token;
        }
        
        public function remind($token)
        {
            $sql = 'SELECT user_id FROM OneTimeAuth WHERE token = :token AND (expire IS NULL OR expire <= NOW()) LIMIT 1';
            $stmt = $this->db->prepare($sql);

            $stmt->execute(array('token' => $token));

            if ($row = $stmt->fetch()) {
                $stmt = $this->db->prepare('DELETE FROM OneTimeAuth WHERE token = :token');
                $stmt->execute(array('token' => $token));

                return $row['user_id'];
            }
        }

        private function generateToken()
        {
            return md5(uniqid('', true));
        }
    }