<?php

namespace App\Tests\Unit;

use App\SQLQuery;
use PHPUnit\Framework\TestCase;

class SQLQueryTest extends TestCase
{
    public function testQueryFieldNull(): void
    {
        $target = new SQLQuery();
        $result = $target->query('table', ['field' => null]);
        $this->assertEquals('SELECT * FROM table WHERE field IS NULL', $result);
    }

    public function testQueryFieldArray(): void
    {
        $target = new SQLQuery();
        $result = $target->query('table', ['field' => ['value1', 'value2']]);
        $this->assertEquals("SELECT * FROM table WHERE field IN ('value1', 'value2')", $result);
    }

    public function testInsert(): void
    {
        $target = new SQLQuery();
        $result = $target->insert('table', [
            'field' => 'value',
            'name' => 'Test Tester'
        ]);
        $this->assertEquals("INSERT INTO table (field, name) VALUES ('value', 'Test Tester')", $result);
    }

    public function testUpdate(): void
    {
        $target = new SQLQuery();
        $result = $target->update('table', ['field' => 'value'], ['id' => 1]);
        $this->assertEquals("UPDATE table SET field = 'value' WHERE id = 1", $result);
    }

    public function testDelete(): void
    {
        $target = new SQLQuery();
        $result = $target->delete('table', ['id' => 1]);
        $this->assertEquals("DELETE FROM table WHERE id = 1", $result);
    }
}
