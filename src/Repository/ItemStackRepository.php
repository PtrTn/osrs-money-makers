<?php

namespace App\Repository;

use App\Entity\ItemStack;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ItemStack>
 *
 * @method ItemStack|null find($id, $lockMode = null, $lockVersion = null)
 * @method ItemStack|null findOneBy(array $criteria, array $orderBy = null)
 * @method ItemStack[]    findAll()
 * @method ItemStack[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ItemStackRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ItemStack::class);
    }
}
