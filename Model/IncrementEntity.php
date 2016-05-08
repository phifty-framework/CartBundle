<?php

namespace CartBundle\Model;

use CartBundle\Model\IncrementEntityBase;
use PDO;
use DateTime;

class IncrementEntity
    extends IncrementEntityBase
{

    public function getNextId()
    {
        /*
        SELECT counter_field FROM child_codes FOR UPDATE;
        UPDATE child_codes SET counter_field = counter_field + 1;
        */
        $table = $this->getTable();
        $conn = $this->getWriteConnection();
        $conn->query("BEGIN");
        $stm = $conn->prepare("SELECT last_id, increment FROM {$table} WHERE id = :id FOR UPDATE");
        $stm->execute([':id' => $this->id]);
        $row = $stm->fetch(PDO::FETCH_ASSOC);
        $stm = $conn->prepare("UPDATE {$table} SET last_id = last_id + increment WHERE id = :id");
        $stm->execute([':id' => $this->id]);
        $conn->query("COMMIT");
        $this->last_id = intval($row['last_id']) + intval($row['increment']);

        $id = strval($this->last_id);
        if ($len = $this->pad_length) {
            $c   = $this->pad_char ?: '0';
            $id = str_pad($id, $len, $c, STR_PAD_LEFT);
        }
        if ($this->prefix) {
            return date($this->prefix) . $id;
        }
        return $id;
    }
}
