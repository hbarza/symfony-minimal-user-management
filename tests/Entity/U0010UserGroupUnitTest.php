<?php

namespace App\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use PHPUnit\Framework\TestCase;
use App\Entity\UserGroup;

class U0010UserGroupUnitTest extends KernelTestCase
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    private $doctrine;

    /**
     * List of properteis to test their setter and getter
     * 
     */
    private $_properties = [
        'name'     => 'Test Group',
    ];

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $kernel = self::bootKernel();

        $this->doctrine      = $kernel->getContainer()->get('doctrine');
        $this->entityManager = $this->doctrine->getManager();
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks

        $this->doctrine = null; // avoid memory leaks
    }

    /**
     * Test UserGroup properties by setting a value and get it and make suer 
     * those are same
     */
    public function testPropertiesSetterGetter()
    {
        $userGroup = new UserGroup;
        foreach ($this->_properties as $property => $value) {
            $setter = "set$property";
            $getter = "get$property";
            $userGroup->$setter($value);
            $this->assertSame($userGroup->$getter(), $value);
        }
    }

    /**
     * Test repository collection
     */
    public function testGetRepositoryCollection()
    {
        $count  = 5;
        for ($i = 1; $i <= $count; $i++) {
            $random = $this->_getRandomNumber();
            $name  = "Test $random$i";
            $this->_addGroupEntity($name);
        }

        $groupsRepo = $this->entityManager
            ->getRepository(UserGroup::class)
            ->findAll();

        $this->assertCount($count + 1, $groupsRepo);
    }
    
    /**
     * Test add new group entity and find it
     */
    public function testGroupEntityAddAndFind()
    {
        $random     = $this->_getRandomNumber();
        $groupId    = $this->_addGroupEntity("Test $random")->getId();
        $this->assertGreaterThanOrEqual(1, $groupId);

        $groupEntity = $this->entityManager
            ->getRepository(UserGroup::class)
            ->find($groupId);

        $this->assertSame($groupId, $groupEntity->getId());
    }

    /**
     * Remove group entity row from db
     */
    public function testGroupEntityAddAndRemove()
    {
        $random     = $this->_getRandomNumber();
        $groupEntity = $this->_addGroupEntity("Test $random");
        $this->assertGreaterThanOrEqual(1, $groupEntity->getId());

        $this->entityManager->remove($groupEntity);
        $this->entityManager->persist($groupEntity);
        $this->entityManager->flush();
        
        $this->assertInstanceOf(UserGroup::class, $groupEntity);
    }

    /**
     * internal method to add group for tests
     * 
     * @param   string  $name
     */
    protected function _addGroupEntity(string $name)
    {
        $groupEntity = new UserGroup;
        $groupEntity->setName("$name Group");
        $groupEntity->setCreateAt(new \DateTime);
        $groupEntity->setUpdateAt(new \DateTime);

        $this->entityManager->persist($groupEntity);
        $this->entityManager->flush();

        return $groupEntity;
    }

    protected function _getRandomNumber()
    {
        return random_int(1, 9999999999);
    }
}
