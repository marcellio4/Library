<?php
/**
 * Setting Database connection
 */
class DB
{
	// Hold the class instance.
	private static $instance = null;
	private $conn;
	private $setAttributes = array(
		PDO::ATTR_EMULATE_PREPARES   => false, // turn off emulation mode for "real" prepared statements
		PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC //make the default fetch be an associative array
	);
	
	// The db connection is established in the private constructor.
	private function __construct()
	{
	    $dsn = "mysql:host=" . Config::setDB('host') . ":3306; dbname=" . Config::setDB('name') . ";charset=utf8mb4";
		try {
			$this->conn = new PDO($dsn, Config::setDB('user'), Config::setDB('password'), $this->setAttributes);
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
	 * Clean up after yourself and close connection
	 */
	public function db_close()
	{
		if (isset($this->conn)) {
			$this->conn = null;
			unset($this->conn);
		}
	}
    
    /**
     * select from database
     * @param string $query using placeholders "SELECT * FROM REGISTRY where name LIKE ?"
     * @param array $array array for bind params in query array("%$_GET[name]%")
     * @return array|bool
     */
    public function find($query, $array) {
        try {
            if ($stmt = $this->conn->prepare($query)) {
                $stmt->execute($array);
                return $stmt->fetchAll();
            }
            return false;
        } catch (PDOException $pdoex) {
            return false;
        } finally {
            $this->db_close();
        }
	}
    
    /**
     * Get all data in row from the table depends on condition
     * @param string $table name
     * @param string $condition of sql statement to find data
     * @return array|bool
     */
    public function findAll($table, $condition) {
        try {
            if ($stmt = $this->conn->prepare("SELECT * FROM {$table} WHERE {$condition}")) {
                $stmt->execute();
                return $stmt->fetchAll();
            }
            return false;
        } catch (PDOException $pdoex) {
            return false;
        } finally {
            $this->db_close();
        }
	}
    
    /**
     * insert into database
     * @param string $table name
     * @param array $column name of the column and value
     * @return bool
     */
    public function create($table, $column) {
        $row = array();
        $arr = array();
        $values = array();
        foreach ($column as $columns => $value) {
            $row[] = $columns;
            $arr[] = "?";
            $values[] = $value;
        }
        $stmt = $this->conn->prepare("INSERT INTO " . $table . "(" . implode(',', $row) . ")
                       VALUES (" . implode(',', $arr) . ")");
        return $stmt->execute($values);
	}
    
    /**
     * delete from database
     * @param  string $table name
     * @param int $id column
     * @return bool
     */
    public function delete($table, $id) {
        $stmt = $this->conn->prepare("DELETE FROM myTable WHERE id = ?");
        return $stmt->execute([$id]);
	}
    
    /**
     * update database
     * @param string $table name
     * @param int $id column
     * @param array $fields set column names with value
     * @return bool
     */
    public function save($table, $id, $fields) {
        $set = '';
        $x = 1;
        $values = array();
        foreach ($fields as $name => $value) {
            $set .= "{$name} = ?";
            $values[] = $value;
            if ($x < count($fields)) {
                $set .= ',';
            }
            $x ++;
        }
        $stmt = $this->conn->prepare("UPDATE {$table} SET {$set} WHERE id = {$id}");
        return $stmt->execute($values);
	}
	
}
