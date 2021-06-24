<?php
    require_once(__DIR__ . "/connectionmanager.php");

    class User {
        private $data;

        function __construct($dataRow) {
            if(is_numeric($dataRow)) {
                $db = ConnectionManager::getInstance()->getConnection();
                $db->query("SELECT * FROM users WHERE userid = ? LIMIT 1", $dataRow);
                $this->data = $db->fetchArray();
            } else {
                $this->data = $dataRow;
            }
        }

        function getUserID() {
            return $this->data["userid"];
        }

        function getEmail() {
            return $this->data["email"];
        }

        function getFirstName() {
            return $this->data["firstName"];
        }

        function getSecondName() {
            return $this->data["secondName"];
        }

        function getBirthday() {
            return $this->data["birthday"];
        }

        function getTimeCreated() {
            return $this->data["timeCreated"];
        }

        function getAvatarPath() {
            if(file_exists(dirname("files/" . $this->data["avatar"]))) {
                return "files/" . $this->data["avatar"];
            } else {
                return "images/missing.png";
            }
        }

        function isFriendsWith($user) {
            $db = ConnectionManager::getInstance()->getConnection();
            $db->query("SELECT * FROM friends WHERE userID = ? AND friendID = ?", $this->getUserID(), $user->getUserID());
            $friend = $db->fetchArray();
            return count($friend) > 0;
        }

        function updateInfo() {
            $db = ConnectionManager::getInstance()->getConnection();
            $db->query("SELECT * FROM users WHERE userid = ? LIMIT 1", $this->data["userid"]);
            $this->data = $db->fetchArray();
        }
    }