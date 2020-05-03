<?php

namespace SAF\Helpers\Features;

use SAF\Helpers\Interfaces\NativeQuery as InterfacesNativeQuery;
use DB;

class NativeQuery implements InterfacesNativeQuery
{
  private $operators = ["=", "<>", "<", ">", "<=", ">="];
  private $stringQuery = "";
  private $params = [];
  private $selectionField = [];
  private $table = "";
  private $joinTable = [];
  private $selectionFilter = [];
  private $groupBy = null;
  private $limit = null;
  private $offset = null;
  private $orderBy = null;

  public function __construct()
  {
    $this->stringQuery = "";
  }

  public function select($string)
  {
    $this->selectionField[] = $string;
  }

  public static function table($table)
  {
    $this->table = " FROM " . $table;
  }

  public function join($table, $foreign, $primary)
  {
    $this->joinTable[] = " JOIN " . $table . " ON " . $foreign . " = " . $primary;
  }

  public function rightJoin($table, $foreign, $primary)
  {
    $this->joinTable[] = " RIGHT JOIN " . $table . " ON " . $foreign . " = " . $primary;
  }

  public function leftJoin($table, $foreign, $primary)
  {
    $this->joinTable[] = " LEFT JOIN " . $table . " ON " . $foreign . " = " . $primary;
  }

  public function where($field, $operator, $comparison = null, $comparison2 = null)
  {
    if (count($this->selectionFilter) == 0) {
      $filter = " WHERE ";
    } else {
      $filter = " AND ";
    }
    if (is_null($comparison)) {
      $param = $this->setParams($field);
      $this->params[][$field] = $operator;
      $this->selectionFilter[] = $filter . $field . " = " . $param;
    } else {
      switch (strtoupper($operator)) {
        case in_array($operator, $this->operators):
        $param = $this->setParams($field);
          $this->params[][$field] = $comparison;
          $this->selectionFilter[] = $filter . $field . " " . $operator . " " . $param;
          break;
        case "BETWEEN":
          $param1 = $this->setParams($field);
          $param2 = $this->setParams($field);
          $this->params[][$field."1"] = $comparison;
          $this->params[][$field."2"] = $comparison2;
          $this->selectionFilter[] = $filter . $field . " " . $operator . " " . $param1 . " AND " . $param2;
          break;
        case "IN" :
          if (is_array($comparison)) {
            $in = implode(",",$comparison);
          } else {
            $in = $comparison;
          }
          $this->selectionFilter[] = $filter . $field . " " . $operator . " ($in) ";
          break;
        case "NOT IN" :
          if (is_array($comparison)) {
            $in = implode(",",$comparison);
          } else {
            $in = $comparison;
          }
          $this->selectionFilter[] = $filter . $field . " " . $operator . " ($in) ";
          break;
        default:
          $param = $this->setParams($field);
          $this->params[][$field] = $comparison;
          $this->selectionFilter[] = $filter . $field . " " . $operator . " " . $param;
          break;
      }
    }
  }

  public function orWhere($field, $operator, $comparison = null, $comparison2 = null)
  {
    if (count($this->selectionFilter) == 0) {
      $filter = " WHERE ";
    } else {
      $filter = " OR ";
    }
    if (is_null($comparison)) {
      $param = $this->setParams($field);
      $this->params[][$field] = $operator;
      $this->selectionFilter[] = $filter . $field . " = " . $param;
    } else {
      switch (strtoupper($operator)) {
        case in_array($operator, $this->operators):
        $param = $this->setParams($field);
          $this->params[][$field] = $comparison;
          $this->selectionFilter[] = $filter . $field . " " . $operator . " " . $param;
          break;
        case "BETWEEN":
          $param1 = $this->setParams($field);
          $param2 = $this->setParams($field);
          $this->params[][$field."1"] = $comparison;
          $this->params[][$field."2"] = $comparison2;
          $this->selectionFilter[] = $filter . $field . " " . $operator . " " . $param1 . " AND " . $param2;
          break;
        case "IN" :
          if (is_array($comparison)) {
            $in = implode(",",$comparison);
          } else {
            $in = $comparison;
          }
          $this->selectionFilter[] = $filter . $field . " " . $operator . " ($in) ";
          break;
        case "NOT IN" :
          if (is_array($comparison)) {
            $in = implode(",",$comparison);
          } else {
            $in = $comparison;
          }
          $this->selectionFilter[] = $filter . $field . " " . $operator . " ($in) ";
          break;
        default:
          $param = $this->setParams($field);
          $this->params[][$field] = $comparison;
          $this->selectionFilter[] = $filter . $field . " " . $operator . " " . $param;
          break;
      }
    }
  }

  public function groupBy($groupBy)
  {
    $this->groupBy = " GROUP BY " . $groupBy;
  }

  public function limit($limit)
  {
    $this->limit = " LIMIT " . $limit;
  }

  public function offset($offset)
  {
    $this->offset = " OFFSET " . $offset;
  }

  public function orderBy($orderBy)
  {
    $this->orderBy = " ORDER BY  " . $orderBy;
  }

  public function having($having)
  {
    $this->orderBy = " HAVING  " . $having;
  }

  public function getStringQuery()
  {
    $this->stringQuery = "SELECT ";
    foreach ($this->selectionField as $key => $value) {
      if ($key == count($this->selectionField) - 1) {
        $this->stringQuery .= $value;
      } else {
        $this->stringQuery .= $value . ", ";
      }
    }
    $this->stringQuery .= $this->table;
    foreach ($this->joinTable as $key => $value) {
      $this->stringQuery .= $value;
    }
    foreach ($this->selectionFilter as $key => $value) {
      $this->stringQuery .= $value;
    }
    if (!is_null($this->groupBy)) {
      $this->stringQuery .= $this->groupBy;
    }
    if (!is_null($this->limit)) {
      $this->stringQuery .= $this->limit;
    }
    if (!is_null($this->offset)) {
      $this->stringQuery .= $this->offset;
    }
    if (!is_null($this->orderBy)) {
      $this->stringQuery .= $this->orderBy;
    }

    return $this->stringQuery;
  }

  public function getParams()
  {
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
}
