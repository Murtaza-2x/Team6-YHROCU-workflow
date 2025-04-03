<?php
// -------------------------------------------------------------
// File: UNITTESTING/TestOutputHelper.php
// Purpose: Suppresses any direct output (echo/var_dump/print_r) during tests
// -------------------------------------------------------------

class TestOutputHelper
{
    // Prevent output during the test execution
    public static function suppressOutput(): void
    {
        ob_start();
        // Suppress any error output during tests as well
        ini_set('display_errors', 0);
    }

    // Restore the original state (if you need to display anything after)
    public static function restoreOutput(): void
    {
        ob_end_clean();
        ini_restore('display_errors');
    }
}
