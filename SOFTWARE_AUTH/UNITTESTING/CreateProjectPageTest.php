<?php
/*
-------------------------------------------------------------
File: CreateProjectPageTest.php
Description:
- Tests the create-project-page.php functionality.
- Verifies access control, form validation, and creation.
-------------------------------------------------------------
*/

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/traits/Auth0SessionTrait.php';
require_once __DIR__ . '/traits/BufferedPageTestTrait.php';
require_once __DIR__ . '/traits/DatabaseTestTrait.php';

class CreateProjectPageTest extends TestCase
{
    use Auth0SessionTrait;
    use BufferedPageTestTrait;
    use DatabaseTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpDatabase();
    }

    protected function tearDown(): void
    {
        $this->conn->query("DELETE FROM projects WHERE project_name = 'Test Project'");
        $this->tearDownDatabase();
    }

    public function testAccessDeniedForGuest()
    {
        $this->clearAuth0Session();
        $output = $this->captureOutput(__DIR__ . '/../create-project-page.php');
        $json = json_decode($output, true);
        $this->assertEquals('Not authorized', $json['error']);
    }

    public function testAccessGrantedForAdmin()
    {
        $this->fakeAuth0User(['role' => 'admin']);
        $_POST = [];
        $output = $this->captureOutput(__DIR__ . '/../create-project-page.php');
        $this->assertEmpty($output, "Admin should be able to view the form without JSON output.");
    }

    public function testInvalidFormSubmissionShowsError()
    {
        $this->fakeAuth0User(['role' => 'admin']);
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'project_name' => '',
            'status' => 'New',
            'priority' => 'High',
            'description' => '',
            'due_date' => '2025-06-30'
        ];

        $output = $this->captureOutput(__DIR__ . '/../create-project-page.php');
        $json = json_decode($output, true);
        $this->assertEquals("All fields are required.", $json['error']);
    }

    public function testProjectCreationWorks()
    {
        $this->fakeAuth0User(['role' => 'admin']);
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'project_name' => 'Test Project',
            'status' => 'New',
            'priority' => 'High',
            'description' => 'Testing project creation',
            'due_date' => '2025-07-01'
        ];

        $output = $this->captureOutput(__DIR__ . '/../create-project-page.php');
        $json = json_decode($output, true);
        $this->assertEquals("Project created successfully.", $json['success']);

        $stmt = $this->conn->prepare("SELECT * FROM projects WHERE project_name = ?");
        $stmt->bind_param("s", $_POST['project_name']);
        $stmt->execute();
        $result = $stmt->get_result();
        $this->assertNotEmpty($result->fetch_assoc(), "Project should exist in DB.");
    }
}