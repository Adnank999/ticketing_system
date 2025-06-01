<?php
namespace helpers;
require_once __DIR__ . '/Database.php';


class DatabaseSeeder
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function seed()
    {
        $this->seedUsers();
        $this->seedDepartments();
        $this->seedTickets();
        $this->seedTicketNotes();
        echo "Database seeded successfully.\n";
    }

    private function seedUsers()
    {
        $password = password_hash('password', PASSWORD_DEFAULT);

        $roles = ['admin', 'agent', 'agent', 'user', 'user', 'user', 'user', 'user', 'user', 'user'];
        foreach ($roles as $index => $role) {
            $name = ucfirst($role) . $index;
            $email = strtolower($role) . $index . '@example.com';
            $stmt = $this->db->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $password, $role]);
        }
    }

    private function seedDepartments()
    {
        for ($i = 1; $i <= 10; $i++) {
            $name = "Department $i";
            $stmt = $this->db->prepare("INSERT INTO departments (name) VALUES (?)");
            $stmt->execute([$name]);
        }
    }

    private function seedTickets()
    {
        for ($i = 1; $i <= 10; $i++) {
            $title = "Sample Ticket $i";
            $description = "This is the description of ticket $i";
            $status = ['open', 'in_progress', 'closed'][rand(0, 2)];
            $userId = rand(4, 10);
            $assignedUserId = rand(2, 3);
            $departmentId = rand(1, 10);
            $attachment = null;

            $stmt = $this->db->prepare("INSERT INTO tickets (title, description, status, user_id, assigned_user_id, department_id, ticket_attachment) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $description, $status, $userId, $assignedUserId, $departmentId, $attachment]);
        }
    }

    private function seedTicketNotes()
    {
        for ($i = 1; $i <= 10; $i++) {
            $ticketId = rand(1, 10);
            $userId = rand(1, 10);
            $note = "Note $i for ticket $ticketId";
            $stmt = $this->db->prepare("INSERT INTO ticket_notes (ticket_id, user_id, note) VALUES (?, ?, ?)");
            $stmt->execute([$ticketId, $userId, $note]);
        }
    }
}


$seeder = new DatabaseSeeder();
$seeder->seed();
