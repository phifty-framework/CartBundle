<?php
namespace CartBundle\Tests;
use LazyRecord\Testing\ModelTestCase;
use CartBundle\Model\IncrementEntitySchema;
use CartBundle\Model\IncrementEntity;

class IncrementEntityTest extends ModelTestCase
{

    public $driver = 'testing';

    public function getModels()
    {
        return [new IncrementEntitySchema];
    }

    public function testIncrementPrefix()
    {
        $entity = new IncrementEntity;
        $result = $entity->create([
            'prefix' => 'Y',
            'pad_length' => 6,
            'pad_char' => '0',
            'start_id' => 1,
            'last_id' => 1,
        ]);
        $this->assertResultSuccess($result);

        $nextId = $entity->getNextId();
        $this->assertEquals('2016000002', $nextId);

        $nextId = $entity->getNextId();
        $this->assertEquals('2016000003', $nextId);

        $nextId = $entity->getNextId();
        $this->assertEquals('2016000004', $nextId);
    }

    public function testIncrementId()
    {
        $entity = new IncrementEntity;
        $result = $entity->create([
            'start_id' => 1,
            'last_id' => 1,
        ]);
        $this->assertResultSuccess($result);
        $nextId = $entity->getNextId();
        $this->assertEquals(2, $nextId);

        $nextId = $entity->getNextId();
        $this->assertEquals(3, $nextId);
    }


}



