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

    /**
     * get all hotels according to booking source id that is set in config file
     * For our select menu in the search
     * @param int $bookingSourceID takes booking source id
     * @return array of all hotels
     */
    public function getHotels($bookingSourceID)
    {
        $query = "SELECT p.Name Display, p.PropertyID
                  from property p
                  inner join contract c on p.PropertyID = c.PropertyID and c.AppliesTo = 'Booking Source' and c.AppliesToID = $bookingSourceID and c.BookingSourceID = $bookingSourceID
                  where p.ExcludeFromRes = 0 AND p.CurrentProperty = 1 AND p.PropertyGroupID != $bookingSourceID
                  group by p.propertyid, Name
                  order by p.name";
        $data = $this->getData($query);
        if (isset($data)) {
            return $data;
        }
        return false;
    }

    /**
     * Get meal basis description return string of meal basis
     * @param int $mealId takes meal basis id
     * @return string meal basis
     */
    public function getMealBasis($mealId)
    {
        $query = "SELECT MealBasis  from MealBasis where MealBasisID = $mealId";
        $data = $this->getData($query);
        if (isset($data)) {
            return $data[0]['MealBasis'];
        }
        return;
    }

    /**
     * @param [string] $currency name of the currency that we search for example(GBP, USD etc.)
     * @return [float] current rate of the currency
     */
    public function getCurrencyRate($currency)
    {
        $query = "SELECT[Rate],[UpdateDate]
                  FROM [Serenity].[dbo].[ExchangeRates]
                  where Currency = '" . $currency . "'";
        $data = $this->getData($query);
        if (isset($data)) {
            return $data[0]['Rate'];
        }
        return;
    }
}
