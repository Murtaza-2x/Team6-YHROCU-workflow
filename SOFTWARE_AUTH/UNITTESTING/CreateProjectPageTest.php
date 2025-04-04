<?php
/*
-------------------------------------------------------------
File: CreateProjectPageTest.php
Description:
- Tests the create-project-page.php functionality.
- Ensures only authorized users can access, that form validation
  works correctly, and that successful submissions create new projects.
-------------------------------------------------------------*/

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/traits/Auth0SessionTrait.php';
require_once __DIR__ . '/traits/BufferedPageTestTrait.php';
require_once __DIR__ . '/traits/DatabaseTestTrait.php';

class CreateProjectPageTest extends TestCase
{
    use Auth0SessionTrait;
    use BufferedPageTestTrait;
    use DatabaseTestTrait;

    /**
     * setUp() is called before each test. 
     * Here, I'm establishing a new database connection for isolation.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpDatabase();
    }

    /**
     * tearDown() is called after each test.
     * This removes any test data we created (projects named 'Test Project'),
     * then closes the DB connection.
     */
    protected function tearDown(): void
    {
        // Clean up any projects might have created for this test.
        $this->conn->query("DELETE FROM projects WHERE project_name = 'Test Project'");
        $this->tearDownDatabase();
    }

    /**
     * Test: Access Denied for Guest
     * 
     * This scenario checks if a user who isn't logged in tries to load the page.
     * We expect a JSON error of 'Not authorized' from test mode.
     */
    public function testAccessDeniedForGuest()
    {
        // Clearing session simulates a guest (not logged in).
        $this->clearAuth0Session();
        // Capture the page output.
        $output = $this->captureOutput(__DIR__ . '/../create-project-page.php');
        $json = json_decode($output, true);

        // expect a 'Not authorized' error in JSON.
        $this->assertEquals('Not authorized', $json['error']);
    }

    /**
     * Test: Access Granted for Admin
     *
     * Here, I'm making sure an admin can view the form. 
     * If the user is admin, we expect no JSON error, meaning it outputs normal HTML.
     */
    public function testAccessGrantedForAdmin()
    {
        // Simulate an admin user.
        $this->fakeAuth0User(['role' => 'admin']);
        $_POST = []; // Not submitting yet, just viewing form.

        $output = $this->captureOutput(__DIR__ . '/../create-project-page.php');

        // For an admin loading the page, we expect no JSON, so the output is empty or plain HTML.
        $this->assertEmpty($output, "Admin should be able to view the form without JSON output.");
    }

    /**
     * Test: Invalid Form Submission
     *
     * This checks if we pass incomplete data. 
     * We expect a JSON error "All fields are required." in test mode.
     */
    public function testInvalidFormSubmissionShowsError()
    {
        // Again, assume admin role so we pass the authorization check.
        $this->fakeAuth0User(['role' => 'admin']);
        $_SERVER['REQUEST_METHOD'] = 'POST';
        // Missing some required fields (project_name and description are empty).
        $_POST = [
            'project_name' => '',
            'status'       => 'New',
            'priority'     => 'High',
            'description'  => '',
            'due_date'     => '2025-06-30'
        ];

        $output = $this->captureOutput(__DIR__ . '/../create-project-page.php');
        $json = json_decode($output, true);

        // We expect a JSON error message from test mode.
        $this->assertEquals("All fields are required.", $json['error']);
    }

    /**
     * Test: Project Creation
     *
     * Here, I submit valid data as an admin and expect
     * "Project created successfully." in the JSON, then confirm
     * the project actually exists in the database.
     */
    public function testProjectCreationWorks()
    {
        // Admin user so we can create a project.
        $this->fakeAuth0User(['role' => 'admin']);
        $_SERVER['REQUEST_METHOD'] = 'POST';

        // Valid input data for project creation.
        $_POST = [
            'project_name' => 'Test Project',
            'status'       => 'New',
            'priority'     => 'High',
            'description'  => 'Testing project creation',
            'due_date'     => '2025-07-01'
        ];

        $output = $this->captureOutput(__DIR__ . '/../create-project-page.php');
        $json = json_decode($output, true);

        // Expect success in JSON response.
        $this->assertEquals("Project created successfully.", $json['success']);

        // Now verify the project is in the DB.
        $stmt = $this->conn->prepare("SELECT * FROM projects WHERE project_name = ?");
        $stmt->bind_param("s", $_POST['project_name']);
        $stmt->execute();
        $result = $stmt->get_result();

        // Asserting we found a record with that name.
        $this->assertNotEmpty($result->fetch_assoc(), "Project should exist in DB.");
    }
}