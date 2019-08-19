<?php

/**
 * Setting Database connection
 */
class DB
{

    // Hold the class instance.
    private static $instance = null;

    private $conn;

    private $host = '';

    private $user = '';

    private $pass = '';

    private $name = '';

    // The db connection is established in the private constructor.
    private function __construct()
    {
        try {
            $this->conn = new PDO("sqlsrv:server=$this->host;Database=$this->name", $this->user, $this->pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::SQLSRV_ATTR_ENCODING, PDO::SQLSRV_ENCODING_UTF8);
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }

    // Magic method clone is empty to prevent duplication of connection
    private function __clone()
    {}

    /**
     * Creating singleton for our Database
     * @return instance
     */
    public static function getInstance()
    {
        if (! self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * get connection
     * @return connection
     */
    public function getConnection()
    {
        return $this->conn;
    }

    /**
     * Clean up after yourself and close connection
     */
    public function db_close()
    {
        if (isset($this->conn)) {
            $this->conn = null;
            unset($this->conn);
        }
    }
}
