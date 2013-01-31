<?php

error_reporting(E_ALL);

require_once(__DIR__ . "/../../../src/SQL/Maker/InsertMulti.php");

final class SQL_Maker_InsertMulti_Test extends \PHPUnit_Framework_TestCase
{
    function test_bindRow__in_case_success()
    {
        $builder = new SQL_Maker_InsertMulti('example_table', array('fields' => array('foo', 'bar', 'baz')));
        $this->assertEquals(count($builder->binds()), 0, "binds should be empty");
        $builder->bindRow(array(
            'foo' => 'foo',
            'bar' => 'bar',
            'baz' => 'baz',
        ));
        $this->assertEquals(count($builder->binds()), 3, "binds should not be empty");
    }

    /**
     *
     * @expectedException InvalidArgumentException
     */
    function test_bindRow__in_case_wronge_number_of_fields()
    {
        $builder = new SQL_Maker_InsertMulti('example_table', array('fields' => array('foo', 'bar', 'baz')));
        $builder->bindRow(array(
            'foo' => 'foo',
            'bar' => 'bar',
        ));
        $this->assertTrue(false, "should raise InvalidArgumentException");
    }

    function test_bindRow__in_case_null_of_fields()
    {
        $builder = new SQL_Maker_InsertMulti('example_table', array('fields' => array('foo', 'bar', 'baz')));
        $this->assertEquals(count($builder->binds()), 0, "binds should be empty");
        $builder->bindRow(array(
            'foo' => 'foo',
            'bar' => 'bar',
            'baz' => null,
        ));
        $this->assertEquals(count($builder->binds()), 3, "binds should not be empty");
    }

    function test_functional__in_case_simple(){
        $builder = new SQL_Maker_InsertMulti('example_table', array('fields' => array('foo', 'bar', 'baz'), 'appendCallerComment' => false));
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

    /**
     * @expectedException InvalidArgumentException
     */
    function test_quoteIdentifier__in_case_contains_quoteIdentifierChar(){
        $builder = new SQL_Maker_InsertMulti('example_table', array('fields' => array('foo', 'bar', 'baz')));
        $builder->quoteIdentifier("`");
        $this->assertTrue(false, "should raise InvalidArgumentException");
    }

    function test_quoteIdentifier__in_case_normal(){
        $builder = new SQL_Maker_InsertMulti('example_table', array('fields' => array('foo', 'bar', 'baz')));
        $result = $builder->quoteIdentifier("test");
        $this->assertSame($result, "`test`", "should quoted.");
    }
}
