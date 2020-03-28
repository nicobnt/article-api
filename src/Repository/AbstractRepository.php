<?php


    namespace App\Repository;

    use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
    use Doctrine\ORM\QueryBuilder;
    use LogicException;
    use Pagerfanta\Adapter\DoctrineORMAdapter;
    use Pagerfanta\Pagerfanta;

    abstract class AbstractRepository extends ServiceEntityRepository
    {
        protected function paginate(QueryBuilder $qb, $limit = 20, $page = 1)
        {
            if (0 == $limit ) {
                throw new LogicException('$limit must be greater than 0.');
            }

            $pager = new Pagerfanta(new DoctrineORMAdapter($qb));
            $pager->setMaxPerPage((int) $limit);
            $pager->setCurrentPage((int) $page);

            return $pager;
        }
    }