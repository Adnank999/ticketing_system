<?php

namespace App\Models;
use PDO;
use helpers\Database;

class BaseModel {
    protected $table;
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function all() {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(array $data) {
        $keys = array_keys($data);
        $fields = implode(',', $keys);
        $placeholders = implode(',', array_fill(0, count($keys), '?'));

        $stmt = $this->db->prepare("INSERT INTO {$this->table} ($fields) VALUES ($placeholders)");
        $stmt->execute(array_values($data));

        return $this->find($this->db->lastInsertId());
    }

    public function update($id, array $data) {
        $fields = implode(', ', array_map(fn($key) => "$key = ?", array_keys($data)));
        $stmt = $this->db->prepare("UPDATE {$this->table} SET $fields WHERE id = ?");
        $stmt->execute([...array_values($data), $id]);

        return $this->find($id);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

