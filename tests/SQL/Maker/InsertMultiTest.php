<?php

error_reporting(E_ALL);

require_once(__DIR__ . "/../../../src/SQL/Maker/InsertMulti.php");

final class SQL_Maker_InsertMulti_Test extends \PHPUnit_Framework_TestCase
{
    function test_functional__in_case_simple(){
        $builder = new SQL_Maker_InsertMulti('example_table', array('fields' => array('foo', 'bar', 'baz')));
        for ( $i = 1; $i <= 3; $i++ ) {
            $builder->bindRow(array(
                'foo' => $i * 1,
                'bar' => $i * 2,
                'baz' => $i * 3,
            ));
        }
        $this->assertSame($builder->toQuery(), "INSERT INTO `example_table` (`foo`, `bar`, `baz`) VALUES (?, ?, ?), (?, ?, ?), (?, ?, ?)", "query OK");
        $this->assertSame(3*3, count($builder->binds()), "binds count OK");
    }

}
