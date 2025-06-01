<?php
namespace App\Models;
use App\Models\BaseModel;
use PDO;

class Department extends BaseModel {
    protected $table = 'departments';

    public function createDepartment(array $data) {
        return $this->create([
            'name' => $data['name']
        ]);
    }

    public function findByName($name)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE name = ?");
        $stmt->execute([$name]);
        return $stmt->fetch(PDO::FETCH_ASSOC); 
    }

    public function updateDepartment($id, array $data) {
        return $this->update($id, [
            'name' => $data['name']
        ]);
    }

    public function deleteDepartment($id) {
        return $this->delete($id);
    }
}
