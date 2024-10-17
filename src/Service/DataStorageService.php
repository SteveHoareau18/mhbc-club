<?php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class DataStorageService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function storeData(array $data)
    {
        // Create YourEntity objects from the data and persist them
        foreach ($data as $row) {
            $entity = new YourEntity();
            // ... set entity properties from the row data
            $this->entityManager->persist($entity);
        }

        $this->entityManager->flush();
    }
}