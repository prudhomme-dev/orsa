<?php

namespace App\Repository;

use App\Entity\Setting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Setting>
 *
 * @method Setting|null find($id, $lockMode = null, $lockVersion = null)
 * @method Setting|null findOneBy(array $criteria, array $orderBy = null)
 * @method Setting[]    findAll()
 * @method Setting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SettingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Setting::class);
    }

    public function add(Setting $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Setting $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return $result[] Returns an associated array of Setting value
     */
    public function findBykey($value): array
    {
        $extract = $this->createQueryBuilder('s')
            ->andWhere('s.keySetting LIKE :val')
            ->setParameter('val', $value . "_%")
            ->orderBy('s.id', 'ASC')
            ->getQuery()
            ->getResult();
        $result = [];
        foreach ($extract as $data) {
            $result[$data->getKeySetting()] = $data->getValue();
        }
        return $result;
    }

    /**
     * @return $result[] Returns an associated array of Setting value
     */
    public function findBykeyObj($value): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.keySetting LIKE :val')
            ->setParameter('val', $value . "_%")
            ->orderBy('s.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

//    public function findOneBySomeField($value): ?Setting
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
