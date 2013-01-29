<?php

/**
 * @example
 *
 *      // simple case.
 *      $builer = new SQL_Maker_InsertMulti("your_table", array('fields' => array('id', 'name', 'created_at'));
 *      foreach ( $data as $row ) {
 *          $builer->bind($row['id']);
 *          $builer->bind($row['name']);
 *          $builer->bind($row['created_at']);
 *      }
 *
 *      $stmt = $pdo->prepare($builer->toQuery());
 *      $pdo->execute($builer->binds());
 *
 *      // you can also use bindParam.
 *      $builer = new SQL_Maker_InsertMulti("your_table", array('fields' => array(
 *          'id' => \PDO::PARAM_INT, 
 *          'name' => \PDO::PARAM_STRING, 
 *          'created_at' => \PDO::PARAM_STRING,
 *      ));
 *      foreach ( $data as $row ) {
 *          $builer->bindRow($row);
 *      }
 *
 *      $stmt = $pdo->prepare($builer->toQuery());
 *      $builer->bindParams($stmt);
 *      $pdo->execute();
 */
class SQL_Maker_InsertMulti {
    private $binds;
    private $quoteChar;
    private $tableName;
    private $fields;

    function __construct($tableName, $options) {
        $default_options = array(
            'quoteChar' => '`',
        );
        $options += $default_options;

        $this->binds = array();
        $this->tableName = $tableName;

        if ( !isset($options['fields']) ) {
            throw new InvalidArgumentException("fields options is required");
        }
        $this->fields = $options['fields'];

        $this->quoteChar = $options['quoteChar'];
    }

    public function bind($value) {
        array_push($this->binds, $value);
    }

    public function binds(){
        return $this->binds;
    }

    public function toQuery()
    {
        $result = "INSERT INTO " . $this->quote($this->tableName) . " ";
        $quoted_fields = array();

        // generate fields expression
        foreach ( $this->fields as $field ) {
            array_push($quoted_fields, $this->quote($field));
        }
        $result .= "(" . implode(", ", $quoted_fields) . ")";
        $result .= " VALUES ";
        
        // generate value expression
        $row_strs = array();
        for ($i = 0; $i < (count($this->binds) / count($this->fields)); $i++ ) {
            $row = array();
            for ($j = 0; $j < count($this->fields); $j++ ) {
                array_push($row, '?');
            }
            $row_str = "(" . implode(", ", $row) . ")";
            array_push($row_strs, $row_str);
        }
        $result .= implode(", ", $row_strs);

        return $result;
    }

    public function quote($arg)
    {
        return $this->quoteChar . $arg . $this->quoteChar;
    }
}
