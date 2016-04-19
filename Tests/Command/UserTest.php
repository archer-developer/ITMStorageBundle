<?php

namespace ITM\StorageBundle\Tests\Command;

use ITM\StorageBundle\Command\UserCreateCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Created by PhpStorm.
 * User: archer
 * Date: 9.9.15
 * Time: 19.23
 */
class UserTest extends WebTestCase
{
    /**
     * @var EntityManager
     */
    private $_em;
    private $container;

    /**
     * Загружаем контейнер и стартуем транзакцию, чтобы внесенные тестом изменения не повредили данные базы
     */
    protected function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $this->container = $kernel->getContainer();
        $this->_em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $this->_em->beginTransaction();
    }

    /**
     * Rollback changes.
     */
    public function tearDown()
    {
        if($this->_em){
            $this->_em->rollback();
        }
    }

    public function testAdd()
    {
        $application = new Application();
        $command = new UserCreateCommand();
        $command->setContainer($this->container);

        $application->add($command);

        $command = $application->find('itm:storage:user-create');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));

        $this->assertRegExp('/User token:.+/im', $commandTester->getDisplay());
    }
}