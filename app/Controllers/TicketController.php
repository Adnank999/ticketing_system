<?php

namespace App\Controllers;

use App\Models\Ticket;
use App\Models\TicketNote;

class TicketController
{
    // public static function create()
    // {
    //     $data = json_decode(file_get_contents("php://input"), true);

    //     $ticket = new Ticket();

    //     if ($ticket->create($data)) {
    //         http_response_code(201);
    //         echo json_encode(['message' => 'Ticket created successfully']);
    //     } else {
    //         http_response_code(500);
    //         echo json_encode(['error' => 'Failed to create ticket']);
    //     }
    // }

    public static function create()
    {
        $data = $_POST; 
        $user = $_REQUEST['auth_user'] ?? null;

        if (!$user) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $filePath = null;

        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../storage/upload/';
            $filename = uniqid() . '_' . basename($_FILES['attachment']['name']);
            $targetPath = $uploadDir . $filename;

            if (move_uploaded_file($_FILES['attachment']['tmp_name'], $targetPath)) {
                $filePath = 'storage/upload/' . $filename;
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to upload attachment']);
                return;
            }
        }

        $data['ticket_attachment'] = $filePath;

        $ticket = new Ticket();
        $result = $ticket->createTicket($data, $user['id']);

        if ($result) {
            http_response_code(201);
            echo json_encode(['message' => 'Ticket created successfully', 'ticket' => $result]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create ticket']);
        }
    }



    public static function assign($id)
    {
        if (empty($id) || !is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid ticket ID']);
            return;
        }

        $ticket = new Ticket();
        $existingTicket = $ticket->getTicketById($id);
        if (!$existingTicket) {
            http_response_code(404);
            echo json_encode(['error' => 'Ticket not found']);
            return;
        }
        $success = $ticket->assignToAgent($id, $_REQUEST['auth_user']['id']);

        if ($success) {
            http_response_code(200);
            echo json_encode(['message' => 'Ticket assigned successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to assign ticket']);
        }
    }


    public static function updateStatus($id)
    {
        if (empty($id) || !is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid ticket ID']);
            return;
        }

        $ticket = new Ticket();
        $existingTicket = $ticket->getTicketById($id);
        if (!$existingTicket) {
            http_response_code(404);
            echo json_encode(['error' => 'Ticket not found']);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);
        $success = $ticket->updateStatus($id, $data['status']);

        if ($success) {
            http_response_code(200);
            echo json_encode(['message' => 'Ticket status updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update ticket status']);
        }
    }

    public static function addNote($id)
    {
        if (empty($id) || !is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid ticket ID']);
            return;
        }

        $ticket = new Ticket();
        $existingTicket = $ticket->getTicketById($id);
        if (!$existingTicket) {
            http_response_code(404);
            echo json_encode(['error' => 'Ticket not found']);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);
        $note = new TicketNote();
        $success = $note->addNote($id, $_REQUEST['auth_user']['id'], $data['note']);

        if ($success) {
            http_response_code(200);
            echo json_encode(['message' => 'Ticket note added successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to add ticket note']);
        }
    }

   
}
