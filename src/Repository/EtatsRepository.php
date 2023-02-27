<?php

namespace App\Repository;

use App\Entity\Etats;
use App\Entity\Sorties;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Etats>
 *
 * @method Etats|null find($id, $lockMode = null, $lockVersion = null)
 * @method Etats|null findOneBy(array $criteria, array $orderBy = null)
 * @method Etats[]    findAll()
 * @method Etats[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EtatsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Etats::class);
    }

    public function save(Etats $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Etats $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function updateEtat(Sorties $sorties)
    {
        $query = $this->createQueryBuilder('e')->select('s');
    }

}
