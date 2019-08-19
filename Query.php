<?php

/**
 * Most use query
 * Or any other related query that is used by the project
 * @param $sql holds the connection with database
 */
class Query extends DB
{

    private $sql;

    private $db;

    public function __construct()
    {
        $this->db = DB::getInstance();
        $this->sql = $this->db->getConnection();
    }

    /**
     * Get data from Database
     * @param $query takes sql code
     * @return false if we don't get any data back
     */
    public function getData($query)
    {
        try {
            $params = [];
            if ($result = $this->sql->query($query, PDO::FETCH_ASSOC)) {
                foreach ($result as $row) {
                    $params[] = $row;
                }
                return $params;
            }
            return false;
        } catch (PDOException $pdoex) {
            return false;
        } finally {
            parent::db_close();
        }
    }

    /**
     * Update table in our Database
     * @param $table takes table name that we want to update
     * @param $id takes id of the particullar row
     * @param $fields set collumns names
     * @return
     */
    public function update($table, $id, $fields)
    {
        $set = '';
        $x = 1;
        foreach ($fields as $name => $value) {
            $set .= "{$name} ='" . $value . "'";
            if ($x < count($fields)) {
                $set .= ',';
            }
            $x ++;
        }
        $query = "UPDATE {$table} SET {$set} WHERE id = {$id}";
        $sth = $this->sql->prepare($query);
        if (! empty($sth)) {
            $sth->execute();
            return true;
        }
        return false;
    }

    /**
     * Insert data do Database
     * @param $table table name
     * @param $column array of columns names
     * @return boolean value if is successfull or not
     */
    public function insert($table, $column)
    {
        $row = array();
        $arr = array();
        foreach ($column as $columns => $value) {
            $row[] = $columns;
            $arr[] = "'" . $value . "'";
        }
        $result = $this->sql->query("INSERT INTO " . $table . "(" . implode(',', $row) . ")
                       VALUES (" . implode(',', $arr) . ")");
        if ($result) {
            return true;
        }
        return false;
    }

    /**
     * Delete particullar row
     * @param $table table name
     * @param $id id of the row that we want to delete
     * @return boolean
     */
    public function delete($table, $id)
    {
        $query = "DELETE FROM {$table} WHERE id = {$id}";
        $result = $this->sql->query($query);
        if ($result) {
            return true;
        }
        return false;
    }
