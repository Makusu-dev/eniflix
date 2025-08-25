<?php

namespace App\Repository;

use App\Entity\Serie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use PhpParser\Node\Expr\Array_;

/**
 * @extends ServiceEntityRepository<Serie>
 */
class SerieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Serie::class);
    }

    public function findSeriesCustom(float $popularity, float $vote, int $nbMaxResults, int $offset): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.popularity > :popularity')
            ->andWhere('s.vote > :vote')
            ->orderBy('s.popularity', 'DESC')
            ->addOrderBy('s.firstAirDate', 'DESC')
            ->setParameter('popularity',$popularity)
            ->setParameter('vote',$vote)
            ->setMaxResults($nbMaxResults)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }



        public function findSeriesWithDQL(float $popularity, float $vote, int $nbMaxResults, int $offset): array {
            $dql = "SELECT s FROM App\Entity\Serie s WHERE (s.popularity > :popularity) OR s.vote > :vote
                    ORDER BY s.firstAirDate DESC ";
            return $this->getEntityManager()->createQuery($dql)
                ->setMaxResults($nbMaxResults)
                ->setFirstResult($offset)
                ->setParameter('popularity',$popularity)
                ->setParameter('vote',$vote)
                ->execute();

        }

        public function getSeriesWithRawSQL(float $popularity, float $vote): array
        {
            $sql= <<<SQL
            SELECT * FROM serie s 
                WHERE (s.popularity > :popularity OR s.first_air_date > :date)
                AND s.vote > :vote
            ORDER BY s.first_air_date DESC, s.popularity DESC
            LIMIT 10 OFFSET 0            
            SQL;


            $conn = $this->getEntityManager()->getConnection();
            return $conn->prepare($sql)->executeQuery(['popularity'=>$popularity,'date'=> (new \DateTime('- 5 years'))->format('Y-m-d'),'vote'=>$vote])
                ->fetchAllAssociative();

        }

        public function getSeriesWithSeasons(int $nbParPage, int $offset) : Paginator {
        $q = $this->createQueryBuilder('s')
            ->orderBy('s.popularity', 'DESC')
            ->leftJoin('s.seasons','se')
            ->addSelect('se')
            ->setMaxResults($nbParPage)
            ->setFirstResult($offset)
            ->getQuery();


            return new Paginator($q);
        }




//    /**
//     * @return Serie[] Returns an array of Serie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Serie
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
