<?php

namespace Truelab\KottiModelBundle\Tests\Repository\Criteria;
use Truelab\KottiModelBundle\Repository\Criteria\DefaultCriteriaManager;

/**
 * Class DefaultCriteriaManagerTest
 * @package Truelab\KottiModelBundle\Tests\Repository\Criteria
 */
class DefaultCriteriaManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testAdd()
    {
        $stateDefaultCriteria = $this->getStateDefaultCriteriaStub();
        $manager = new DefaultCriteriaManager();

        $manager->add($stateDefaultCriteria);

        $defaultCriterias = $manager->process();

        $this->assertCount(1, $defaultCriterias);
    }

    /**
     * @expectedException \Truelab\KottiModelBundle\Repository\Criteria\Exception\NotUniqueDefaultCriteriaNameException
     */
    public function testAddThrowsExceptionNotUniqueName()
    {
        $stateDefaultCriteria = $this->getStateDefaultCriteriaStub();
        $stateDefaultCriteria2 = $this->getStateDefaultCriteriaStub();
        $manager = new DefaultCriteriaManager();

        $manager->add($stateDefaultCriteria);
        $manager->add($stateDefaultCriteria2);
    }

    /**
     * @expectedException \Truelab\KottiModelBundle\Repository\Criteria\Exception\InvalidDefaultCriteriaNameException
     */
    public function testAddThrowsInvalidName()
    {
        $invalidCriteria = $this->getInvalidDefaultCriteriaStub();
        $manager = new DefaultCriteriaManager();
        $manager->add($invalidCriteria);
    }

    protected function getInvalidDefaultCriteriaStub()
    {
        $invalidCriteria = $this
            ->getMock('Truelab\KottiModelBundle\Repository\Criteria\DefaultCriteriaInterface');

        $invalidCriteria
            ->expects($this->any())
            ->method('getName')
            ->willReturn('');

        $invalidCriteria
            ->expects($this->any())
            ->method('getCriteria')
            ->willReturn(['contents.state = ?' => 'public']);

        return $invalidCriteria;
    }

    protected function getStateDefaultCriteriaStub()
    {
        $stateDefaultCriteria = $this
            ->getMock('Truelab\KottiModelBundle\Repository\Criteria\DefaultCriteriaInterface');

        $stateDefaultCriteria
            ->expects($this->any())
            ->method('getName')
            ->willReturn('state');

        $stateDefaultCriteria
            ->expects($this->any())
            ->method('getCriteria')
            ->willReturn(['contents.state = ?' => 'public']);

        return $stateDefaultCriteria;
    }
}
