<?php
/**
 * Description of dataBaseManager
 *
 * @author mrSpock
 */
class DataBase_Messages_Manager {
    
    private $dbConnet;
    
    function __construct() {
        $this->dbConnet = new DataBaseConnection(); 
    }
    /*
     *  Получение TypeID по стобцлу Name    
     */
    public function getTypeId($name){
        
        $getType = $this->dbConnet ->getConnect()->prepare('SELECT ID FROM Types WHERE Name = :name');
        $getType->execute(array('name' => $name));
        $id = $getType->fetchColumn();
        return $id;
    }
    /*
     *  Получение массива MessageID по значению TypeID    
     */
    public function getMessagesIdsByTypeId($name){

        $type = $this->getTypeId($name);
        $getMessagesId = $this->dbConnet->getConnect()->prepare('SELECT MessageID FROM FromTo WHERE TypeID = :type');
        $getMessagesId->execute(array('type' => $type));
        $data = $getMessagesId->fetchAll(PDO::FETCH_COLUMN);
        //print_r($data);
        return $data;
    }
    /*
     *  Получение сообщений по ID
     */
    public function getMessageById($id){

        $getMessage = $this->dbConnet->getConnect()->prepare('SELECT * FROM Messages WHERE ID = :id');
        $getMessage->execute(array('id' => $id));
        $data = $getMessage->fetch();
        //echo count($data);
        //print_r($data);
        return $data;
    }
    /*
     *  Получение массива сообщений
     */
    public function getMessages(){

        $getMessage = $this->dbConnet->getConnect()->prepare('SELECT * FROM Messages ORDER BY CreationDate DESC');
        $getMessage->execute();
        $data = $getMessage->fetchAll();
        //echo count($data);
        print_r($data);
        return $data;
    }
    /*
     *  Получение массива Отправителей(From) по значению MessageID
     */
    public function getUserFromByMessageId($id){

        $getUserFrom = $this->dbConnet->getConnect()->prepare('SELECT UserFrom FROM FromTo WHERE MessageID = :id');
        $getUserFrom->execute(array('id' => $id));
        $userID = $getUserFrom->fetchColumn();
        
        $getUser = $this->dbConnet->getConnect()->prepare('SELECT * FROM Users WHERE ID = :id');
        $getUser->execute(array('id' => $userID));
        $data = $getUser->fetch();
        //echo count($data);
        //print_r($data);
        return $data;
    }
    /*
     *  Получение массива Получателей(To) по значению MessageID
     */
    public function getUserToByMessageId($id){

        $getUserTo = $this->dbConnet->getConnect()->prepare('SELECT UserTo FROM FromTo WHERE MessageID = :id');
        $getUserTo->execute(array('id' => $id));
        $userID = $getUserTo->fetchColumn();
        
        $getUser = $this->dbConnet->getConnect()->prepare('SELECT * FROM Users WHERE ID = :id');
        $getUser->execute(array('id' => $userID));
        $data = $getUser->fetch();
        //echo count($data);
        //print_r($data);
        return $data;
    }
    
    public function getIsRead($id)
    {
        $sql = "select IsRead from FromTo where MessageID = :id";
        $isRead = $this->dbConnet->getConnect()->prepare($sql);
        $isRead->execute(array('id' => $id));
        $data = $isRead->fetch();
        return $data["IsRead"];
    }
    
    public function setIsRead($id)
    {
        $sql = "UPDATE FromTo SET IsRead=1 WHERE ID = :id";
        $isRead = $this->dbConnet->getConnect()->prepare($sql);
        $isRead->execute(array('id' => $id));
    }
    
    public function getMessagesToUser($UserID)
    {
        // ID 	CreationDate 	Title 	Body 	MessageID 	UserFrom 	UserTo 	ExpiredDate 	TypeID 	IsRead 
        //select * from Messages LEFT JOIN FromTo USING(ID) WHERE ID in (SELECT `MessageID` FROM `FromTo` WHERE `UserTo` = 1)
        //$sql = "SELECT * FROM Messages WHERE ID in (SELECT `MessageID` FROM `FromTo` WHERE `UserTo` = 1)";
        $sql = "select * from Messages LEFT JOIN FromTo USING(ID) WHERE ID in (SELECT `MessageID` FROM `FromTo` WHERE `UserTo` = 1)";
        $messages = $this->dbConnet->getConnect()->prepare($sql);
        $messages->execute();
        return $messages->fetchAll();
    }
}
