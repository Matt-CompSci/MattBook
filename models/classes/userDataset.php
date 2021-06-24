<?php
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
    require_once("../models/classes/connectionmanager.php");
    require_once("../models/classes/user.php");

    class UserDataSet {
        static function getUserByID($userID) {
            return new User($userID);
        }

        static function getUserFromTable($tblRow) {
            return new User($tblRow);
        }

        static function getUsersByName($name, $limit=5) {
            $db = ConnectionManager::getInstance()->getConnection();
            $regexExpression = strtolower("%" . $name . "%");
            $db->query("SELECT * FROM users WHERE LOWER(firstName) LIKE ? LIMIT ?", $regexExpression, $limit);
            $queryResult = $db->fetchArray();
            return array_map("self::getUserFromTable", $queryResult);
        }
    }