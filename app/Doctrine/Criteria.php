<?php

namespace App\Doctrine;

use Doctrine\Common\Collections\Criteria as Base;
use Doctrine\Common\Collections\ExpressionBuilder;

class Criteria
{
    public const DESC = 'DESC';
    public const ASC = 'ASC';

    /** @var Base */
    protected $criteria;

    protected const MAP = [
        '=' => 'eq',
        '>' => 'lt',
        '<' => 'gt',
        '>=' => 'lte',
        '<=' => 'gte',
        '!=' => 'neq',
        'null' => 'isNull',
        'in' => 'in'
    ];

    public function __construct()
    {
        $this->criteria = Base::create();
    }

    public static function where($field, $operator, $value = null)
    {
        $instance = new self;

        $instance->andWhere($field, $operator, $value);

        return $instance;
    }

    public function andWhere($field, $operator, $value = null)
    {
        if ($value === null && $operator !== '!=') {
            $value = $operator;
            $operator = '=';
        }

        $this->criteria->andWhere(Base::expr()->{self::MAP[$operator]}($field, $value));

        return $this;
    }

    public function orderBy($order)
    {
        $this->criteria->orderBy($order);

        return $this;
    }

    public function get()
    {
        return $this->criteria;
    }

    /**
     * @return Base
     */
    public static function create()
    {
        return Base::create();
    }

    /**
     * @return ExpressionBuilder
     */
    public static function expr()
    {
        return Base::expr();
    }
}
