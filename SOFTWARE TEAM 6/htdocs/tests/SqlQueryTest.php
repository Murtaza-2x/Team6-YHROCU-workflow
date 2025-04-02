<?php

use PHPUnit\Framework\TestCase;

class SqlQueryTest extends TestCase
{
    public function testSqlQueryForUserClearance()
    {
        $clearance = 'User';
        $id = 5;  // Simulating an example user ID

        // Your logic to generate the SQL query
        if ($clearance === 'User') {
            $sql = "
                SELECT
                    t.id,
                    t.subject,
                    t.project_id,
                    p.project_name,
                    t.status,
                    t.priority,
                    c.username AS creator_name,
                    GROUP_CONCAT(u.username SEPARATOR ', ') AS assigned_users
                FROM tasks AS t
                LEFT JOIN projects p ON t.project_id = p.id
                JOIN task_assigned_users AS tau ON t.id = tau.task_id
                JOIN users AS u ON tau.user_id = u.id
                LEFT JOIN users AS c ON t.created_by = c.id
                WHERE tau.user_id = {$id}
                GROUP BY t.id
            ";
        } else {
            $sql = "
                SELECT
                    t.id,
                    t.subject,
                    t.project_id,
                    p.project_name,
                    t.status,
                    t.priority,
                    c.username AS creator_name,
                    GROUP_CONCAT(u.username SEPARATOR ', ') AS assigned_users
                FROM tasks AS t
                LEFT JOIN projects p ON t.project_id = p.id
                LEFT JOIN task_assigned_users AS tau ON t.id = tau.task_id
                LEFT JOIN users AS u ON tau.user_id = u.id
                LEFT JOIN users AS c ON t.created_by = c.id
                GROUP BY t.id
            ";
        }

        // Expected SQL query for 'User' clearance
        $expectedSql = "
            SELECT
                t.id,
                t.subject,
                t.project_id,
                p.project_name,
                t.status,
                t.priority,
                c.username AS creator_name,
                GROUP_CONCAT(u.username SEPARATOR ', ') AS assigned_users
            FROM tasks AS t
            LEFT JOIN projects p ON t.project_id = p.id
            JOIN task_assigned_users AS tau ON t.id = tau.task_id
            JOIN users AS u ON tau.user_id = u.id
            LEFT JOIN users AS c ON t.created_by = c.id
            WHERE tau.user_id = 5
            GROUP BY t.id
        ";

        // Assert that the SQL query generated for 'User' clearance matches the expected SQL
        $this->assertEquals($expectedSql, $sql);
    }

    public function testSqlQueryForOtherClearance()
    {
        $clearance = 'Admin';  // Any clearance other than 'User'
        $id = 5;

        // Your logic to generate the SQL query
        if ($clearance === 'User') {
            $sql = "
                SELECT
                    t.id,
                    t.subject,
                    t.project_id,
                    p.project_name,
                    t.status,
                    t.priority,
                    c.username AS creator_name,
                    GROUP_CONCAT(u.username SEPARATOR ', ') AS assigned_users
                FROM tasks AS t
                LEFT JOIN projects p ON t.project_id = p.id
                JOIN task_assigned_users AS tau ON t.id = tau.task_id
                JOIN users AS u ON tau.user_id = u.id
                LEFT JOIN users AS c ON t.created_by = c.id
                WHERE tau.user_id = {$id}
                GROUP BY t.id
            ";
        } else {
            $sql = "
                SELECT
                    t.id,
                    t.subject,
                    t.project_id,
                    p.project_name,
                    t.status,
                    t.priority,
                    c.username AS creator_name,
                    GROUP_CONCAT(u.username SEPARATOR ', ') AS assigned_users
                FROM tasks AS t
                LEFT JOIN projects p ON t.project_id = p.id
                LEFT JOIN task_assigned_users AS tau ON t.id = tau.task_id
                LEFT JOIN users AS u ON tau.user_id = u.id
                LEFT JOIN users AS c ON t.created_by = c.id
                GROUP BY t.id
            ";
        }

        // Expected SQL query for any clearance other than 'User'
        $expectedSql = "
            SELECT
                t.id,
                t.subject,
                t.project_id,
                p.project_name,
                t.status,
                t.priority,
                c.username AS creator_name,
                GROUP_CONCAT(u.username SEPARATOR ', ') AS assigned_users
            FROM tasks AS t
            LEFT JOIN projects p ON t.project_id = p.id
            LEFT JOIN task_assigned_users AS tau ON t.id = tau.task_id
            LEFT JOIN users AS u ON tau.user_id = u.id
            LEFT JOIN users AS c ON t.created_by = c.id
            GROUP BY t.id
        ";

        // Assert that the SQL query generated for any clearance other than 'User' matches the expected SQL
        $this->assertEquals($expectedSql, $sql);
    }
}
