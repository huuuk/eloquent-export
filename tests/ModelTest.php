<?php
use PHPUnit\Framework\TestCase;
use AdvancedEloquent\Export\Model;
use AdvancedEloquent\Export\Test\DummyClass;

class ModelTest extends TestCase
{
    public function testModelCreation()
    {
        $model = new Model;
        $dummy = new DummyClass;
        $this->assertInstanceOf(Model::class, $model);
        $this->assertInstanceOf(Model::class, $dummy);
    }
}