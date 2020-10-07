<?php

namespace App\Repository;

use App\Entity\Property;
use App\Entity\Search;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Property|null find($id, $lockMode = null, $lockVersion = null)
 * @method Property|null findOneBy(array $criteria, array $orderBy = null)
 * @method Property[]    findAll()
 * @method Property[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PropertyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Property::class);
    }

    /**
     * @param Search $search
     * @return Query
     */
    public function queryFindAllVisible(Search $search):Query
    {
        $query = $this->queryFindVisible();


        if ($search->getTitle()){
            $query = $query
                ->andWhere('property.title LIKE :title')
                ->setParameter('title', '%'.$search->getTitle().'%');
        }

        if ($search->getOptions()!== null ){
            $i = 0;
            foreach ($search->getOptions() as $option){
                $query = $query
                    ->andWhere(":option$i MEMBER OF property.options")
                    ->setParameter("option$i", $search->getOptions());
            }
        }

        if ($search->getCity()){

            if ($search->getLat() && $search->getLng() && $search->getDistance()){

                $query =  $query
                    ->select('property')
                    ->andWhere("(6378 * acos(cos(radians(:lat)) * cos(radians(property.lat)) * cos(radians(property.lng) - radians(:lng)) + sin(radians(:lat)) * sin(radians(property.lat)))) < :distance")
                    ->setParameter('lat', $search->getLat())
                    ->setParameter('lng', $search->getLng())
                    ->setParameter('distance', $search->getDistance())
                ;

            }else{

                if(is_numeric($search->getCity())){

                    $query= $query
                        ->andWhere('property.postal_code = :city')
                        ->setParameter('city', $search->getCity());
                }else{
                    $tabSearch = str_word_count($search->getCity(),1);
                    dump($tabSearch);
                    $query= $query
                        ->andWhere('property.city = :city')
                        ->setParameter('city', $tabSearch[0]);
                }

            }


        }

        if ($search->getCategory()!== null ){
            $query= $query
                ->andWhere('property.category = :cat')
                ->setParameter('cat', $search->getCategory());
        }

        if ($search->getMaxPrice()){
            $query = $query
                ->andWhere('property.price < :maxprice')
                ->setParameter('maxprice', $search->getMaxPrice());
        }

        if ($search->getMinSurface()){
            $query = $query
                ->andWhere('property.surface > :minsurface')
                ->setParameter('minsurface', $search->getMinSurface());
        }

        if ($search->getFeatured()){
            $query = $query
                ->andWhere('property.featured = true');
        }


        return $query->getQuery();
    }

    /**
     * @return Query
     */
    public function queryFindAll():Query
    {
        return $this->queryFindVisible()
            ->getQuery();
    }

    /**
     * @param int $maxResult
     * @return Property[]
     */
    public function findLast(int $maxResult=3) : array
    {
        return $this->queryFindVisible()
            ->setMaxResults($maxResult)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param int $maxResult
     * @return Property[]
     */
    public function findFeatured(int $maxResult=3 ) : array
    {
        return $this->queryFindVisible()
            ->andWhere('property.featured = true')
            ->setMaxResults($maxResult)
            ->orderBy('RAND()')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param int $maxResult
     * @param UserInterface $user
     * @return Property[]
     */
    public function findBookmarks(UserInterface $user, int $maxResult=12 ) : array
    {
        return $this->queryFindVisible()
            ->innerJoin('property.favoris', "fav")
            ->andWhere('fav = :user')
            ->setParameter("user", $user->getId())
            ->setMaxResults($maxResult)
            ->getQuery()
            ->getResult()
            ;
    }

    private function queryFindVisible(){
        return $this->createQueryBuilder('property')
            ->orderBy('property.created_at', 'DESC')
            ->andWhere('property.sold = false');
    }


    // /**
    //  * @return Property[] Returns an array of Property objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Property
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
