<?php

namespace App\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Entity\UserEntity;
use App\Entity\UserGroup;

class U0020UserEntityUnitTest extends KernelTestCase
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    private $doctrine;

    /**
     * List of properteis to test their setter and getter
     * 
     * @var array
     */
    private $_properties = [
        'email'     => 'test@tester.com',
        'firstname' => 'Omid',
        'lastname'  => 'Barza'
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
     * Test UserEntity properties by setting a value and get it and make suer 
     * those are same
     */
    public function testPropertiesSetterGetter()
    {
        $userEntity = new UserEntity;
        foreach ($this->_properties as $property => $value) {
            $setter = "set$property";
            $getter = "get$property";
            $userEntity->$setter($value);
            $this->assertSame($userEntity->$getter(), $value);
        }
    }

    /**
     * Test repository collection
     */
    public function testGetRepositoryCollection()
    {
        $count  = 10;
        for ($i = 1; $i <= $count; $i++) {
            $random = $this->_getRandomNumber();
            $email  = "repoTest$random$i";
            $this->_addUserEntity($email);
        }

        $usersRepo = $this->entityManager
            ->getRepository(UserEntity::class)
            ->findAll();

        $this->assertCount($count + 1, $usersRepo);
    }
    
    // /**
    //  * @depends UserEntityKernelTest::testRepository
    //  */
    /**
     * Test add new user entity and find it
     */
    public function testUserEntityAddAndFind()
    {
        $random     = $this->_getRandomNumber();
        $userId     = $this->_addUserEntity("test$random")->getId();
        $this->assertGreaterThanOrEqual(1, $userId);

        $userEntity = $this->entityManager
            ->getRepository(UserEntity::class)
            ->find($userId);

        $this->assertSame($userId, $userEntity->getId());
    }

    /**
     * Adding groups to user entity test and update those to new groups
     */
    public function testAssignGroupToUser()
    {
        $userGroups = $this->entityManager
            ->getRepository(UserGroup::class)
            ->findAll();

        $random     = $this->_getRandomNumber();
        $userEntity = $this->_addUserEntity("testassign$random");
        $result     = null;
        $i = 0;
        foreach ($userGroups as $id => $userGrop) {
            if ($i >= 3) {
                break;
            }
            $result = $userEntity->addUserGroup($userGrop);
            $this->assertInstanceOf(UserEntity::class, $result);
            unset($userGroups[$id]);
        }
        $updateGroupsIds = [];
        $i = 0;
        foreach ($userGroups as $userGroup) {
            if ($i >= 3) {
                break;
            }
            $updateGroupsIds[] = $userGroup->getId();
        }
        $result = $userEntity->saveUserGroups($updateGroupsIds, $this->doctrine);
        $this->assertInstanceOf(UserEntity::class, $result);
    }

    /**
     * Remove user entity row from db
     */
    public function testUserEntityAddAndRemove()
    {
        $random     = $this->_getRandomNumber();
        $userEntity = $this->_addUserEntity("test$random");
        $this->assertGreaterThanOrEqual(1, $userEntity->getId());

        $this->entityManager->remove($userEntity);
        $this->entityManager->persist($userEntity);
        $this->entityManager->flush();
            
        $this->assertInstanceOf(UserEntity::class, $userEntity);
    }

    /**
     * internal method to add user for tests
     * 
     * @param   string  $email
     */
    protected function _addUserEntity(string $email)
    {
        $userEntity = new UserEntity();
        $userEntity->setEmail("$email@tester.com");
        $userEntity->setFirstname('Omid');
        $userEntity->setLastname('Barza');
        $userEntity->setCreateAt(new \DateTime);
        $userEntity->setUpdateAt(new \DateTime);

        $this->entityManager->persist($userEntity);
        $this->entityManager->flush();

        return $userEntity;
    }

    protected function _getRandomNumber()
    {
        return random_int(1, 9999999999);
    }
}
