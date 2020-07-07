<?php
class User {

    /**
     * @var integer
     */
    private $id = null;

    /**
     * @var mixed|mysqli
     */
    private $db = null;

    /**
     * User constructor.
     *
     * @param $id
     */
    public function __construct($id) {
        $this->db = $GLOBALS["db"];
        $this->id = $id;
    }

    /**
     * Insert user to database
     *
     * @param string $name
     *
     * @return bool
     */
    public function create($name) {
        if ($this->db->query("SELECT n FROM users WHERE id = '$this->id'")->num_rows) return true;
        else return $this->db->query("INSERT INTO users (`id`, `name`, `created_at`) VALUES ('$this->id', '$name', UNIX_TIMESTAMP())");
    }

    /**
     * Get user specified columns
     *
     * @param array $columns
     *
     * @return bool|array
     */
    public function get(array $columns = []) {
        $data = "";
        if (!empty($columns)) {
            foreach ($columns as $column)
                $data .= "`$column`, ";

            $data = rtrim($data, ", ");
        }
        else $data = "*";

        $getDetails = $this->db->query("SELECT $data FROM users WHERE id = '$this->id'");
        if ($getDetails->num_rows)
            return count($columns) == 1 ? $getDetails->fetch_assoc()[$columns[0]] : $getDetails->fetch_assoc();
        else
            return false;
    }

    /**
     * Update user specified columns
     *
     * @param array $data
     *
     * @return bool
     */
    public function set(array $data = []) {
        $query = [];
        foreach ($data as $key => $value) $query[] = "`$key` = \"$value\"";
        $parsed = implode(", ", $query);

        return $this->db->query("UPDATE users SET $parsed WHERE id = '$this->id'");
    }

    /**
     * Insert data
     *
     * @param string $table
     * @param array $data
     *
     * @return bool
     */
    function insert($table, $data) {
        $columns = [];
        $values = [];

        foreach ($data as $key => $value) {
            $columns[] = "`$key`";
            $values[] = (is_int($value) ? $value : (substr($value, -1) == ")" ? "$value" : "'$value'"));
        }
        $columns = implode(", ", $columns);
        $values = implode(", ", $values);

        $query = "INSERT INTO `$table` ($columns) VALUES ($values)";

        return $this->db->query($query);
    }
}
