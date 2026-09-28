<?php

namespace App\Models;

use Atusan\Model\ModelBase;

class model_name extends ModelBase
{
  function selectAll() : array
  {
    $sql = 'SELECT cols FROM table_name';

    return $this->conn->query($sql, []);
  }

  function get(mixed $id) : array
  {
    $sql = 'SELECT cols FROM table_name WHERE id = ?';

    $r = $this->conn->query($sql, [$id]);

    return ($r) ? $r[0] : ['id' => 0, 'cols' => ''];
  }

  function list(mixed $id) : array
  {
    $sql = 'SELECT id as `value`, cols as `text`
      FROM table_name
      WHERE id = ? order by cols';

    return $this->conn->query($sql, [$id]);
  }

  public function save(array $data) : array
  {
    $this->conn->autocommit(false);

    $sql = 'call `procedure`(?[,?....])';

    $res = $this->conn->query($sql, $data);

    if ($res) {
      $this->conn->commit();
    } else {
      $this->conn->rollback();
    }

    return $res;
  }

  public function delete(array $data) : array
  {
    $this->conn->autocommit(false);

    $sql = 'call `procedure`(?[,?....])';

    $res = $this->conn->query($sql, $data);

    if ($res) {
      $this->conn->commit();
    } else {
      $this->conn->rollback();
    }

    return $res;
  }
}
