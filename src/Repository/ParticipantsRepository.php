<?php

namespace App\Repository;

use App\Entity\Participants;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<Participants>
 *
 * @method Participants|null find($id, $lockMode = null, $lockVersion = null)
 * @method Participants|null findOneBy(array $criteria, array $orderBy = null)
 * @method Participants[]    findAll()
 * @method Participants[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParticipantsRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Participants::class);
    }

    public function save(Participants $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Participants $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Participants) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->save($user, true);
    }

    public function exists($nom, $prenom, $email){
        $con = $this->getEntityManager()->getConnection();
        
        $qb = $this->createQueryBuilder('p');
        $qb->where($qb->expr()->orX(
            $qb->expr()->andX($qb->expr()->eq('p.nom', ':nom'), $qb->expr()->eq('p.prenom', ':prenom')),
            $qb->expr()->eq('p.email', ':email')
        ))
            ->setParameter('nom',$nom)
            ->setParameter('prenom',$prenom)
            ->setParameter('email',$email);
            

        $query = $qb->getQuery();

        if($query->getResult()){
            return true;
        }
        
        return false;
    }
}
