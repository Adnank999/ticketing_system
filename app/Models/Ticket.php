<?php
namespace App\Models;

use App\Models\BaseModel;

class Ticket extends BaseModel {
    protected $table = 'tickets';

    public function createTicket(array $data, $user_id) {
        return $this->create([
            'title' => $data['title'],
            'description' => $data['description'],
            'user_id' => $user_id,
            'department_id' => $data['department_id'],
            'status' => 'open',
            'ticket_attachment' => $data['ticket_attachment'] ?? null

        ]);
    }

    public function assignToAgent($ticketId, $agentId) {
        return $this->update($ticketId, [
            'status' => 'in_progress',
            'assigned_user_id' => $agentId
        ]);
    }

    public function updateStatus($ticketId, $status) {
        return $this->update($ticketId, [
            'status' => $status
        ]);
    }

    public function getAllTickets() {
        return $this->all();
    }

    public function getTicketById($id) {
        return $this->find($id);
    }
}
