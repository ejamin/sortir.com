<?php

namespace App\Repository;

use App\Entity\Incriptions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Incriptions>
 *
 * @method Incriptions|null find($id, $lockMode = null, $lockVersion = null)
 * @method Incriptions|null findOneBy(array $criteria, array $orderBy = null)
 * @method Incriptions[]    findAll()
 * @method Incriptions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IncriptionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Incriptions::class);
    }

    public function save(Incriptions $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Incriptions $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

}
