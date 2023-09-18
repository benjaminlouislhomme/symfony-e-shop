<?php

namespace App\Test\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private ProductRepository $repository;
    private string $path = '/product/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()
                ->get('doctrine')
                ->getRepository(Product::class)
            ;

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Product index');
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        // $this->markTestIncomplete();

        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'product[name]' => 'My Name',
            'product[description]' => 'My Description',
            'product[image]' => 'my-image-url',
            'product[stock]' => 10,
            'product[price]' => 20.00,
        ]);

        self::assertResponseRedirects('/product/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        // $this->markTestIncomplete();

        $fixture = new Product();
        $fixture->setName('My Name');
        $fixture->setDescription('My Description');
        $fixture->setImage('my-image-url');
        $fixture->setStock(10);
        $fixture->setPrice(20.00);

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Product');
    }

    public function testEdit(): void
    {
        // $this->markTestIncomplete();

        $fixture = new Product();
        $fixture->setName('Something New');
        $fixture->setDescription('Something New');
        $fixture->setImage('my-image-new-url');
        $fixture->setStock(10);
        $fixture->setPrice(20.00);

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'product[name]' => 'Something New',
            'product[description]' => 'Something New',
            'product[image]' => 'my-image-new-url',
            'product[stock]' => 15,
            'product[price]' => 25.78,
        ]);

        self::assertResponseRedirects('/product/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getName());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('my-image-new-url', $fixture[0]->getImage());
        self::assertSame(15, $fixture[0]->getStock());
        self::assertSame('25.78', $fixture[0]->getPrice());
    }

    public function testRemove(): void
    {
        // $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Product();
        $fixture->setName('My Name');
        $fixture->setDescription('My Description');
        $fixture->setImage('my-image-url');
        $fixture->setStock(10);
        $fixture->setPrice(20.00);

        $this->repository->save($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/product/');
    }
}
