<?php
namespace App\Models;

use App\Models\BaseModel;
use PDO;

class TicketNote extends BaseModel {
    protected $table = 'ticket_notes';

    public function addNote($ticket_id, $user_id, $note) {
        return $this->create([
            'ticket_id' => $ticket_id,
            'user_id' => $user_id,
            'note' => $note
        ]);
    }

    public function getNotesByTicket($ticket_id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE ticket_id = ? ORDER BY created_at DESC");
        $stmt->execute([$ticket_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
