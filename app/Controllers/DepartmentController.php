<?php

namespace App\Controllers;

use App\Models\Department;


class DepartmentController
{
    public static function create()
    {

        $data = json_decode(file_get_contents("php://input"), true);

       

        if (empty($data['name'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Department name is required']);
            return;
        }


        $department = new Department();


        $existingDepartment = $department->findByName($data['name']);

        if ($existingDepartment) {
            http_response_code(409);
            echo json_encode(['error' => 'Department name already exists']);
            return;
        }

        if ($department->create($data)) {
            http_response_code(201);
            echo json_encode(['message' => 'Department created successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create department']);
        }
    }


    public static function update($id)
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $department = new Department();
        $updatedDepartment = $department->update($id, $data);

        if ($updatedDepartment) {
            http_response_code(200);
            echo json_encode([
                'message' => 'Department updated successfully'
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'error' => 'Failed to update department'
            ]);
        }
    }


    public static function delete($id)
    {
        $department = new Department();
        $deletedDepartment = $department->delete($id);

        if ($deletedDepartment) {
            http_response_code(200);
            echo json_encode([
                'message' => 'Department deleted successfully'
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'error' => 'Failed to delete department'
            ]);
        }
    }
}
