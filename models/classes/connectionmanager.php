<?php


class ConnectionManager
{
    private static $instance = null;
    private $conn;

    private function __construct($inModelFile=false) {
        require_once("db.php");
        $this->conn = new db("doNotTouchThisGodDamnCode", $inModelFile);
    }

    public static function getInstance($inModelFile=false) {
        if (!self::$instance) {
            self::$instance = new ConnectionManager($inModelFile);
        }

        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }
}