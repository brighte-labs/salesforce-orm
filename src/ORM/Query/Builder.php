<?php
namespace Salesforce\ORM\Query;

class Builder
{
    private $select = [];
    private $from;
    private $where = [];
    private $order = [];
    private $limit;

    /**
     * @param array|string $select select
     * @return $this
     */
    public function select($select)
    {
        if (is_array($select)) {
            $this->select += $select;
        } else {
            $this->select[] = $select;
        }

        return $this;
    }

    /**
     * @param string $objectType object type
     * @return $this
     */
    public function from($objectType)
    {
        $this->from = $objectType;

        return $this;
    }

    /**
     * @param string|array $predicate where
     * @return $this
     */
    public function where($predicate)
    {
        if (empty($predicate)) {
            return $this;
        }
        if (is_array($predicate)) {
            $this->where = $predicate;
        } else {
            $this->where[] = $predicate;
        }

        return $this;
    }

    /**
     * @param string $predicate where
     * @return $this
     */
    public function andWhere(string $predicate)
    {
        $this->where[] = ' AND ' . $predicate;

        return $this;
    }

    /**
     * @param string $predicate where
     * @return $this
     */
    public function orWhere(string $predicate)
    {
        $this->where[] = ' OR ' . $predicate;

        return $this;
    }

    /**
     * @param  int|null $limit limit
     * @return $this
     */
    public function limit(int $limit = null)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @param array|null $orders array
     * @return $this
     */
    public function order(array $orders = null)
    {
        if (empty($orders)) {
            return $this;
        }

        foreach ($orders as $order) {
            if (count($order) == 2 && in_array($order[1], ['ASC', 'DESC'])) {
                $this->order[] = $order[0] . ' ' . $order[1];
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        $query = 'SELECT ' . implode(',', $this->select) . ' FROM ' . $this->from;

        if (count($this->where) > 0) {
            $query .= $this->getWhereString();
        }
        if (!empty($this->order)) {
            $query .= ' ORDER BY ' . implode(",", $this->order);
        }
        if (null !== $this->limit) {
            $query .= ' LIMIT ' . $this->limit;
        }

        return $query;
    }

    /**
     * @return string|null
     */
    private function getWhereString()
    {
        if (empty($this->where)) {
            return null;
        }

        $whereString = ' WHERE ';
        foreach ($this->where as $where) {
            preg_match('/(AND |OR )?(\w+) *([!=><]+|LIKE|IN) *[\'"]?(.*)[\'"]?/i', $where, $matches);
            list($all, $connector, $field, $operator, $value) = $matches;
            if ($operator === 'IN') {
                $whereString .= $connector . ' ' . $field . ' ' . $operator . ' ' . $value . ' ';
            }else {
                $whereString .= $connector . ' ' . $field . ' ' . $operator . ' ' . $this->quote($value) . ' ';
            }
        }

        return rtrim($whereString);
    }

    /**
     * @param mixed $value value
     * @return string
     */
    private function quote($value)
    {
        if ($value == 'null') {
            return $value;
        }

        if (is_numeric($value)) {
            return $value;
        }
        
        return '\'' . $value . '\'';
    }
}
