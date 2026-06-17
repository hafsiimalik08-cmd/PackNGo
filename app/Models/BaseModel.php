<?php
/**
 * PackNGo — Base Model
 * All models extend this class to get shared DB helpers.
 */

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config/Database.php';

abstract class BaseModel
{
    protected Database $db;
    protected string   $table  = '';
    protected string   $pk     = 'id';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ── Generic CRUD ───────────────────────────────────────

    public function findById(int $id): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM `{$this->table}` WHERE `{$this->pk}` = :id LIMIT 1",
            [':id' => $id]
        );
    }

    public function findAll(string $orderBy = 'id', string $dir = 'ASC'): array
    {
        $dir = strtoupper($dir) === 'DESC' ? 'DESC' : 'ASC';
        return $this->db->fetchAll(
            "SELECT * FROM `{$this->table}` ORDER BY `{$orderBy}` {$dir}"
        );
    }

    public function findWhere(array $conditions, string $orderBy = 'id', string $dir = 'ASC'): array
    {
        $dir    = strtoupper($dir) === 'DESC' ? 'DESC' : 'ASC';
        $wheres = [];
        $params = [];
        foreach ($conditions as $col => $val) {
            $wheres[] = "`{$col}` = :{$col}";
            $params[":{$col}"] = $val;
        }
        $where = implode(' AND ', $wheres);
        return $this->db->fetchAll(
            "SELECT * FROM `{$this->table}` WHERE {$where} ORDER BY `{$orderBy}` {$dir}",
            $params
        );
    }

    public function insert(array $data): int
    {
        $cols   = array_keys($data);
        $placeholders = array_map(fn($c) => ":{$c}", $cols);
        $colList = implode(', ', array_map(fn($c) => "`{$c}`", $cols));
        $phList  = implode(', ', $placeholders);
        $params  = [];
        foreach ($data as $col => $val) {
            $params[":{$col}"] = $val;
        }
        $this->db->execute(
            "INSERT INTO `{$this->table}` ({$colList}) VALUES ({$phList})",
            $params
        );
        return $this->db->lastInsertId();
    }

    public function update(int $id, array $data): int
    {
        $sets   = [];
        $params = [':pk' => $id];
        foreach ($data as $col => $val) {
            $sets[] = "`{$col}` = :{$col}";
            $params[":{$col}"] = $val;
        }
        $setList = implode(', ', $sets);
        return $this->db->execute(
            "UPDATE `{$this->table}` SET {$setList} WHERE `{$this->pk}` = :pk",
            $params
        );
    }

    public function delete(int $id): int
    {
        return $this->db->execute(
            "DELETE FROM `{$this->table}` WHERE `{$this->pk}` = :id",
            [':id' => $id]
        );
    }

    public function count(array $conditions = []): int
    {
        if (empty($conditions)) {
            $row = $this->db->fetchOne("SELECT COUNT(*) AS cnt FROM `{$this->table}`");
        } else {
            $wheres = [];
            $params = [];
            foreach ($conditions as $col => $val) {
                $wheres[] = "`{$col}` = :{$col}";
                $params[":{$col}"] = $val;
            }
            $where = implode(' AND ', $wheres);
            $row   = $this->db->fetchOne(
                "SELECT COUNT(*) AS cnt FROM `{$this->table}` WHERE {$where}",
                $params
            );
        }
        return (int)($row['cnt'] ?? 0);
    }

    // ── Pagination helper ──────────────────────────────────

    public function paginate(int $page, int $perPage, array $conditions = [], string $orderBy = 'id', string $dir = 'DESC'): array
    {
        $dir    = strtoupper($dir) === 'ASC' ? 'ASC' : 'DESC';
        $offset = ($page - 1) * $perPage;
        $params = [':limit' => $perPage, ':offset' => $offset];
        $where  = '';

        if (!empty($conditions)) {
            $wheres = [];
            foreach ($conditions as $col => $val) {
                $wheres[] = "`{$col}` = :{$col}";
                $params[":{$col}"] = $val;
            }
            $where = 'WHERE ' . implode(' AND ', $wheres);
        }

        $total = $this->count($conditions);
        $rows  = $this->db->fetchAll(
            "SELECT * FROM `{$this->table}` {$where} ORDER BY `{$orderBy}` {$dir} LIMIT :limit OFFSET :offset",
            $params
        );

        return [
            'data'         => $rows,
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => (int) ceil($total / $perPage),
        ];
    }
}
