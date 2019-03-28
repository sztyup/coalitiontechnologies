<?php

namespace App\Doctrine;

use Doctrine\Common\Collections\Criteria as DoctrineCriteria;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\EntityRepository as Base;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\QueryException;
use Illuminate\Support\Collection;
use LaravelDoctrine\ORM\Pagination\PaginatesFromRequest;

class EntityRepository extends Base
{
    use PaginatesFromRequest;

    /**
     * @param $id
     * @return null|\object
     * @throws EntityNotFoundException
     */
    public function findOrFail($id)
    {
        $entity = $this->find($id);

        if ($entity === null) {
            throw EntityNotFoundException::fromClassNameAndIdentifier(static::class, $id);
        }

        return $entity;
    }

    /**
     * @return Collection
     */
    public function findAll()
    {
        return Collection::make(parent::findAll());
    }

    /**
     * @param array $by
     * @return mixed
     */
    public function latest($by = [])
    {
        return $this->findOneBy($by, [
            'createdAt' => 'DESC'
        ]);
    }

    /**
     * @param array $by
     * @return mixed
     */
    public function oldest($by = [])
    {
        return $this->findOneBy($by, [
            'createdAt' => 'ASC'
        ]);
    }

    /**
     * @param array $criteria
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     * @return Collection
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return Collection::make(parent::findBy($criteria, $orderBy, $limit, $offset));
    }

    /**
     * @param DoctrineCriteria $criteria
     * @return Collection
     */
    public function findByCriteria(DoctrineCriteria $criteria) : Collection
    {
        $persister = $this->_em->getUnitOfWork()->getEntityPersister($this->_entityName);

        return Collection::make($persister->loadCriteria($criteria));
    }

    /**
     * @param array $criteria
     * @param array|null $orderBy
     * @return mixed
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return parent::findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @return mixed
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws QueryException
     */
    public function findOneRandom(array $criteria = [])
    {
        $where = Criteria::create();

        foreach ($criteria as $field => $value) {
            $where = $where->andWhere(Criteria::where($field, $value)->get()->getWhereExpression());
        }

        $qb = $this
            ->createQueryBuilder('c')
            ->addSelect('RAND() as HIDDEN rand')
            ->orderBy('rand')
            ->setMaxResults(1)
            ->addCriteria($where)
        ;

        $query = $qb->getQuery();

        return $query->getSingleResult();
    }

    /**
     * @param array $criteria
     * @return int|mixed
     * @throws NonUniqueResultException
     * @throws QueryException
     */
    public function count(array $criteria = []) : int
    {
        $where = Criteria::create();

        foreach ($criteria as $field => $value) {
            $where = $where->andWhere(Criteria::where($field, $value)->get()->getWhereExpression());
        }

        $qb = $this->createQueryBuilder('e')
            ->select('count (e)')
            ->addCriteria($where)
        ;

        $query = $qb->getQuery();

        return (int) $query->getSingleScalarResult();
    }

    /**
     * @param $fieldToSum
     * @param array $criteria
     * @return mixed
     * @throws NonUniqueResultException
     * @throws QueryException
     */
    public function sum($fieldToSum, array $criteria = []) : int
    {
        $where = Criteria::create();

        foreach ($criteria as $field => $value) {
            $where = $where->andWhere(
                Criteria::where($field, $value)->get()->getWhereExpression()
            );
        }

        $qb = $this->createQueryBuilder('e')
            ->select('sum (e.' . $fieldToSum . ')')
            ->addCriteria($where)
        ;

        $query = $qb->getQuery();

        return (int) $query->getSingleScalarResult();
    }

    /**
     * @param array $criteria
     * @return bool
     * @throws QueryException
     * @throws NonUniqueResultException
     */
    public function exists(array $criteria = []) : bool
    {
        return $this->count($criteria) > 0;
    }

    /**
     * @param $order
     *
     * @return array
     */
    public function orderBy($order)
    {
        return parent::findBy([], $order);
    }

    /**
     * @param $field
     * @param $operator
     * @param $value
     *
     * @return Collection
     */
    public function where($field, $operator, $value)
    {
        return $this->matching(
            Criteria::where($field, $operator, $value)->get()
        );
    }

    /**
     * @param DoctrineCriteria $criteria
     *
     * @return Collection
     */
    public function matching(DoctrineCriteria $criteria)
    {
        return Collection::make(
            parent::matching($criteria)
        );
    }
}
