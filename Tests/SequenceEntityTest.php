<?php
namespace CartBundle\Tests;
use LazyRecord\Testing\ModelTestCase;
use CartBundle\Model\SequenceEntitySchema;
use CartBundle\Model\SequenceEntity;

class SequenceEntityTest extends ModelTestCase
{

    public $driver = 'testing';

    public function getModels()
    {
        return [new SequenceEntitySchema];
    }

    public function testIncrementPrefix()
    {
        $entity = new SequenceEntity;
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
        $entity = new SequenceEntity;
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



