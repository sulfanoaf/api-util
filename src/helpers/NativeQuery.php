<?php
namespace DAI\Utils\Helpers;

use DAI\Utils\Interfaces\NativeQuery as InterfacesNativeQuery;
use DB;
use Exception;

class NativeQuery implements InterfacesNativeQuery
{
  private $stringQuery = "";
  private $withQuery = "";
  private $params = [];
  private $selectionField = [];
  private $table = "";
  private $joinTable = [];
  private $selectionFilter = [];
  private $groupBy = null;
  private $limit = null;
  private $offset = null;
  private $orderBy = null;
  private $filterEmpty = false;
  private $enableLog = false;
  private $subQueryLength = 1;
  private $unionQuery = "";

  public function __construct($table, $filterEmpty = false)
  {
    $this->table = " FROM " . $table;
    $this->filterEmpty = $filterEmpty;
    $this->stringQuery = "";
  }

  public function select($string)
  {
    $this->selectionField[] = $string;
    return $this;
  }

  public static function table($table, $filterEmpty = false)
  {
    return new NativeQuery($table, $filterEmpty);
  }

  public function with($alias, $query)
  {
    if ($query instanceof NativeQuery) {
      $prefix = " WITH ";
      if ($this->withQuery !== "") {
        $prefix = ", ";
      }
      $params = $query->getParams();
      $stringQuery = $query->getStringQuery();
      foreach ($params as $key => $value) {
        $stringQuery = str_replace(":" . $key, ":" . $this->subQueryLength . "_" . $key, $stringQuery);
        $this->params[$this->subQueryLength . "_" . $key] = $value;
      }
      $this->withQuery .= $prefix . $alias . "AS (" . $stringQuery . ")";
      $this->subQueryLength = $this->subQueryLength + 1;
    } else {
      throw new Exception("INVALID QUERY FOR WITH, IT MUST BE INSTACE OF NATIVEQUERY");
    }
    return $this;
  }

  public function union($query)
  {
    if ($query instanceof NativeQuery) {
      $params = $query->getParams();
      $stringQuery = $query->getStringQuery();
      foreach ($params as $key => $value) {
        $stringQuery = str_replace(":" . $key, ":" . $this->subQueryLength . "_" . $key, $stringQuery);
        $this->params[$this->subQueryLength . "_" . $key] = $value;
      }
      $this->unionQuery = "\n UNION \n" . $stringQuery;
      $this->subQueryLength = $this->subQueryLength + 1;
    } else {
      throw new Exception("INVALID QUERY FOR UNION, IT MUST BE INSTACE OF NATIVEQUERY");
    }
    return $this;
  }

  public function unionAll($query)
  {
    if ($query instanceof NativeQuery) {
      $params = $query->getParams();
      $stringQuery = $query->getStringQuery();
      foreach ($params as $key => $value) {
        $stringQuery = str_replace(":" . $key, ":" . $this->subQueryLength . "_" . $key, $stringQuery);
        $this->params[$this->subQueryLength . "_" . $key] = $value;
      }
      $this->unionQuery = "\n UNION ALL \n" . $stringQuery;
      $this->subQueryLength = $this->subQueryLength + 1;
    } else {
      throw new Exception("INVALID QUERY FOR UNION ALL, IT MUST BE INSTACE OF NATIVEQUERY");
    }
    return $this;
  }

  public function join($table, $foreign, $primary)
  {
    $this->joinTable[] = " JOIN " . $table . " ON " . $foreign . " = " . $primary;
    return $this;
  }

  public function rightJoin($table, $foreign, $primary)
  {
    $this->joinTable[] = " RIGHT JOIN " . $table . " ON " . $foreign . " = " . $primary;
    return $this;
  }

  public function leftJoin($table, $foreign, $primary)
  {
    $this->joinTable[] = " LEFT JOIN " . $table . " ON " . $foreign . " = " . $primary;
    return $this;
  }

  public function where($fields, $operator, $comparison = null)
  {
    if (is_null($comparison)) {
      $c = $operator;
      $o = "=";
    } else {
      $c = $comparison;
      $o = $operator;
    }

    if (!$this->isAdd($c)) {
      return $this;
    }

    $fieldList = explode(",", $fields);
    if (count($fieldList) == 1) {
      $param = $this->setParams($fields);
      $this->params[$param] = $c;
      $this->selectionFilter[] = $this->setFilter() . $fields . " " . $o . " " . $param;
    } else {
      $query = "(";
      foreach ($fieldList as $key => $field) {
        $param = $this->setParams($field);
        $this->params[$param] = $c;
        if ($key == 0) {
          $query .= $field . " " . $o . " " . $param;
        } else {
          $query .= " OR " . $field . " " . $o . " " . $param;
        }
      }
      $param = $this->setParams($fields);
      $this->selectionFilter[] = $this->setFilter() . $query . ")";
    }

    return $this;
  }

  public function whereNull($field)
  {
    $this->selectionFilter[] = $this->setFilter() . $field . " IS NULL ";
    return $this;
  }

  public function whereNotNull($field)
  {
    $this->selectionFilter[] = $this->setFilter() . $field . " IS NOT NULL ";
    return $this;
  }

  public function whereIn($field, $list)
  {
    if (!$this->isAdd($list)) {
      return $this;
    }
    if (is_array($list)) {
      $in = implode(",", $list);
    } else {
      $in = $list;
    }
    $this->selectionFilter[] = $this->setFilter() . $field . " IN ($in) ";
    return $this;
  }

  public function whereNotIn($field, $list)
  {
    if (!$this->isAdd($list)) {
      return $this;
    }
    if (is_array($list)) {
      $in = implode(",", $list);
    } else {
      $in = $list;
    }
    $this->selectionFilter[] = $this->setFilter() . $field . " NOT IN ($in) ";
    return $this;
  }

  public function whereBetween($field, $start, $end)
  {
    $param1 = $this->setParams($field . "1");
    $param2 = $this->setParams($field . "2");
    $this->params[$param1] = $start;
    $this->params[$param2] = $end;
    $this->selectionFilter[] = $this->setFilter() . $field . " BETWEEN " . $param1 . " AND " . $param2;
  }

  public function orWhere($fields, $operator, $comparison = null)
  {
    if (is_null($comparison)) {
      $c = $operator;
      $o = "=";
    } else {
      $c = $comparison;
      $o = $operator;
    }

    if (!$this->isAdd($c)) {
      return $this;
    }

    $fieldList = explode(",", $fields);
    if (count($fieldList) == 1) {
      $param = $this->setParams($fields);
      $this->params[$param] = $c;
      $this->selectionFilter[] = $this->setOrFilter() . $fields . " " . $o . " " . $param;
    } else {
      $query = "(";
      foreach ($fieldList as $key => $field) {
        $param = $this->setParams($field);
        $this->params[$param] = $c;
        if ($key == 0) {
          $query .= $field . " " . $o . " " . $param;
        } else {
          $query .= " OR " . $field . " " . $o . " " . $param;
        }
      }
      $param = $this->setParams($fields);
      $this->selectionFilter[] = $this->setOrFilter() . $query . ")";
    }

    return $this;
  }

  public function orWhereNull($field)
  {
    $this->selectionFilter[] = $this->setOrFilter() . $field . " IS NULL ";
    return $this;
  }

  public function orWhereNotNull($field)
  {
    $this->selectionFilter[] = $this->setOrFilter() . $field . " IS NOT NULL ";
    return $this;
  }

  public function orWhereIn($field, $list)
  {
    if (!$this->isAdd($list)) {
      return $this;
    }
    if (is_array($list)) {
      $in = implode(",", $list);
    } else {
      $in = $list;
    }
    $this->selectionFilter[] = $this->setOrFilter() . $field . " IN ($in) ";
    return $this;
  }

  public function orWhereNotIn($field, $list)
  {
    if (!$this->isAdd($list)) {
      return $this;
    }
    if (is_array($list)) {
      $in = implode(",", $list);
    } else {
      $in = $list;
    }
    $this->selectionFilter[] = $this->setOrFilter() . $field . " NOT IN ($in) ";
    return $this;
  }

  public function orWhereBetween($field, $start, $end)
  {
    $param1 = $this->setParams($field . "1");
    $param2 = $this->setParams($field . "2");
    $this->params[$param1] = $start;
    $this->params[$param2] = $end;
    $this->selectionFilter[] = $this->setOrFilter() . $field . " BETWEEN " . $param1 . " AND " . $param2;
  }


  public function groupBy($groupBy)
  {
    $this->groupBy = " GROUP BY " . $groupBy;
    return $this;
  }

  public function limit($limit)
  {
    $this->limit = " LIMIT " . $limit;
    return $this;
  }

  public function offset($offset)
  {
    $this->offset = " OFFSET " . $offset;
    return $this;
  }

  public function orderBy($orderBy)
  {
    $this->orderBy = " ORDER BY  " . $orderBy;
    return $this;
  }

  public function having($having)
  {
    $this->orderBy = " HAVING  " . $having;
    return $this;
  }

  public function getStringQuery()
  {
    $stringQuery = "";
    if ($this->withQuery != "") {
      $stringQuery .= $this->withQuery;
    }
    $stringQuery .= " SELECT ";
    if (count($this->selectionField) > 0) {
      foreach ($this->selectionField as $key => $value) {
        if ($key == count($this->selectionField) - 1) {
          $stringQuery .= $value;
        } else {
          $stringQuery .= $value . ", ";
        }
      }
    } else {
      $stringQuery .= " * ";
    }
    $stringQuery .= $this->table;
    foreach ($this->joinTable as $key => $value) {
      $stringQuery .= $value;
    }
    foreach ($this->selectionFilter as $key => $value) {
      $stringQuery .= $value;
    }
    if ($this->unionQuery != "") {
      $stringQuery .= $this->unionQuery;
    }
    if (!is_null($this->groupBy)) {
      $stringQuery .= $this->groupBy;
    }
    if (!is_null($this->orderBy)) {
      $stringQuery .= $this->orderBy;
    }
    if (!is_null($this->limit)) {
      $stringQuery .= $this->limit;
    }
    if (!is_null($this->offset)) {
      $stringQuery .= $this->offset;
    }

    if ($this->enableLog) TailLog::debug($this, __FUNCTION__, $stringQuery);
    return $stringQuery;
  }

  public function getParams()
  {
    if ($this->enableLog) TailLog::debug($this, __FUNCTION__, $this->params);
    return $this->params;
  }

  public function get()
  {
    return DB::SELECT($this->getStringQuery(), $this->getParams());
  }

  public function first()
  {
    return collect(DB::SELECT($this->getStringQuery(), $this->getParams()))->first();
  }

  private function setParams($field)
  {
    $arr = explode(".", $field);
    if (count($arr) == 1) {
      return ":" . $field;
    } else {
      return ":" . $arr[1];
    }
  }

  private function isAdd($value)
  {
    if ($this->filterEmpty && (is_null($value) || $value == "")) {
      return false;
    }
    return true;
  }

  private function setFilter()
  {
    if (count($this->selectionFilter) == 0) {
      return " WHERE ";
    } else {
      return " AND ";
    }
  }

  private function setOrFilter()
  {
    if (count($this->selectionFilter) == 0) {
      return " WHERE ";
    } else {
      return " AND ";
    }
  }

  public function enableLog()
  {
    $this->enableLog = true;
  }
}
