<?php

namespace App\Repository;

use App\Entity\Item;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Item>
 *
 * @method Item|null find($id, $lockMode = null, $lockVersion = null)
 * @method Item|null findOneBy(array $criteria, array $orderBy = null)
 * @method Item[]    findAll()
 * @method Item[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Item::class);
    }

    /** @return Item[] */
    public function findAllWithPrice(): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT i, p
            FROM App\Entity\Item i
            INNER JOIN i.ItemPrice p'
        );

        return $query->getArrayResult();
    }

    public function truncate(): void
    {
        $this->createQueryBuilder('i')
            ->delete()
            ->getQuery()
            ->execute();
    }

    /** @param Item[] $itemEntities */
    public function save(array $itemEntities): void
    {
        $entityManager = $this->getEntityManager();
        foreach ($itemEntities as $entity) {
            $entityManager->persist($entity);
        }
        $entityManager->flush();
        $entityManager->clear();
    }
}
