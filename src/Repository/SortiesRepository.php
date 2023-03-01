<?php

namespace App\Repository;

use App\Entity\Sorties;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\Criteria;

/**
 * @extends ServiceEntityRepository<Sorties>
 *
 * @method Sorties|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sorties|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sorties[]    findAll()
 * @method Sorties[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortiesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sorties::class);
    }

    public function save(Sorties $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sorties $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function filter($idSite, $search, $dateMin, $dateMax, $organisateur, $participant, $notParticipant, $passee, $idUtilisateur): array{
        $con = $this->getEntityManager()->getConnection();
        
        $qb = $this->createQueryBuilder('s')
            ->addSelect('e')
            ->join('s.idEtat', 'e');

        if($idSite) {
            $qb->where('s.idSite = :site')
                ->setParameter('site', $idSite);
        }

        if ($search) {
            $qb->andWhere($qb->expr()->like('s.nom',':search'))
                ->setParameter('search',"%{$search}%");
        }
        if($dateMin && $dateMax){
            $qb->andWhere($qb->expr()->between('s.dateDebut',':dateMin',':dateMax'))
                ->setParameter('dateMin',$dateMin)
                ->setParameter('dateMax',$dateMax);
        }
        if($organisateur){
            $qb->andWhere($qb->expr()->eq('s.idOrganisateur', ':idOrga'))
                ->setParameter('idOrga',$idUtilisateur);
        }
        if($participant == true && $notParticipant == false){
            $qb->addSelect('p')
                ->join('s.idParticipant', 'p')
                ->andWhere($qb->expr()->isMemberOf(':participant', 's.idParticipant'))
                ->setParameter('participant', $participant);
        }
        if($participant == false && $notParticipant == true) {
            $qb->addSelect('p')
            ->leftJoin('s.idParticipant', 'p')
            ->andWhere(':participant NOT MEMBER OF s.idParticipant')
            ->setParameter('participant', $idUtilisateur);
        }
        if($passee == true){
            $qb->andWhere('s.idEtat = :etat')
                ->setParameter('etat',$passee);
        }

        $query = $qb->getQuery();

        return $query->getResult();
    }

}
