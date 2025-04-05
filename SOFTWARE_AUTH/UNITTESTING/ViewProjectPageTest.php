<?php
/*
-------------------------------------------------------------
File: ViewProjectPageTest.php
Description:
- PHPUnit tests for view-project-page.php in test mode.
- Verifies that:
    • An invalid project ID returns a JSON error message "Invalid project ID".
    • A nonexistent project ID (e.g. 99999) returns {"error": "Project not found"}.
    • A valid project ID returns the expected project details.
-------------------------------------------------------------
*/

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/BaseTestCase.php';
require_once __DIR__ . '/traits/Auth0SessionTrait.php';
require_once __DIR__ . '/traits/BufferedPageTestTrait.php';

class ViewProjectPageTest extends BaseTestCase
{
    use Auth0SessionTrait;
    use BufferedPageTestTrait;

    /**
     * Test: Invalid Project ID
     * Test that an empty or invalid project ID returns an error.
     */
    public function testInvalidProjectIdShowsError()
    {
        // Mimic a request with an empty ID
        $_GET['id'] = '';
        
        // Start output buffering, include the page, then get the output
        ob_start();
        include __DIR__ . '/test_files/view-project-page.php';
        $output = ob_get_clean();

        // Decode the JSON response from the test-mode branch
        $json = json_decode($output, true);
        $this->assertNotNull($json, "Output not valid JSON.");
        $this->assertEquals("Invalid project ID", $json['error']);
    }

    /**
     * Test: Nonexistent Project
     * Test that a nonexistent project ID returns a "Project not found" error.
     */
    public function testNonexistentProjectShowsError()
    {
        // Use project ID 99999 to simulate a project that doesn't exist
        $_GET['id'] = '99999';
        
        ob_start();
        include __DIR__ . '/test_files/view-project-page.php';
        $output = ob_get_clean();

        $json = json_decode($output, true);
        $this->assertNotNull($json);
        $this->assertEquals("Project not found", $json['error']);
    }

    /**
     * Test: View Project Page Displays Project
     * Test that a valid project ID returns the correct project details.
     */
    public function testViewProjectPageDisplaysProject()
    {
        // Use a valid project ID; in test mode, this should return mock data
        $_GET['id'] = '1';
        
        // Capture the output using our buffered page trait (which handles output capturing)
        $output = $this->captureOutput(__DIR__ . '/test_files/view-project-page.php');
        $json = json_decode($output, true);
        
        $this->assertNotNull($json);
        // Check that the JSON contains the correct project ID
        $this->assertEquals("1", $json['projectId']);
        // Verify that the returned project details contain our expected dummy values
        $this->assertStringContainsString("Test Project Name", $json['project']['project_name']);
        $this->assertStringContainsString("Test project description", $json['project']['description']);
    }
}