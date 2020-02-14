<?php

declare(strict_types=1);

/*
 * This file is part of the FOSOAuthServerBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\OAuthServerBundle\Tests\Document;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use FOS\OAuthServerBundle\Document\ClientManager;
use FOS\OAuthServerBundle\Model\ClientInterface;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class ClientManagerTest.
 *
 * @author Nikola Petkanski <nikola@petkanski.com>
 */
class ClientManagerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var MockObject|DocumentManager
     */
    protected $documentManager;

    /**
     * @var string
     */
    protected $className;

    /**
     * @var MockObject|DocumentRepository
     */
    protected $repository;

    /**
     * @var ClientManager
     */
    protected $instance;

    public function setUp(): void
    {
        if (!class_exists(DocumentManager::class)) {
            $this->markTestSkipped('Doctrine MongoDB ODM has to be installed for this test to run.');
        }

        $this->documentManager = $this->getMockBuilder(DocumentManager::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->repository = $this->getMockBuilder(DocumentRepository::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->className = 'RandomClassName'.\random_bytes(5);

        $this->documentManager
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->className)
            ->willReturn($this->repository)
        ;

        $this->instance = new ClientManager($this->documentManager, $this->className);

        parent::setUp();
    }

    public function testConstructWillSetParameters(): void
    {
        self::assertSame($this->documentManager, $this->instance->getDocumentManager());
        self::assertSame($this->repository, $this->instance->getRepository());
        self::assertSame($this->className, $this->instance->getClass());
    }

    public function testGetClass(): void
    {
        self::assertSame($this->className, $this->instance->getClass());
    }

    public function testFindClientBy(): void
    {
        $randomResult = new \stdClass();
        $criteria = [
            \random_bytes(5),
        ];

        $this->repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with($criteria)
            ->willReturn($randomResult)
        ;

        self::assertSame($randomResult, $this->instance->findClientBy($criteria));
    }

    public function testUpdateClient()
    {
        $client = $this->getMockBuilder(ClientInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->documentManager
            ->expects($this->once())
            ->method('persist')
            ->with($client)
            ->willReturn(null)
        ;

        $this->documentManager
            ->expects($this->once())
            ->method('flush')
            ->with()
            ->willReturn(null)
        ;

        self::assertNull($this->instance->updateClient($client));
    }

    public function testDeleteClient()
    {
        $client = $this->getMockBuilder(ClientInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->documentManager
            ->expects($this->once())
            ->method('remove')
            ->with($client)
            ->willReturn(null)
        ;

        $this->documentManager
            ->expects($this->once())
            ->method('flush')
            ->with()
            ->willReturn(null)
        ;

        self::assertNull($this->instance->deleteClient($client));
    }
}
