<?php
// -------------------------------------------------------------
// Trait: BufferedPageTestTrait
// Purpose: Capture page output safely without printing to STDOUT.
// -------------------------------------------------------------

trait BufferedPageTestTrait
{
    public function captureOutput(string $file): string
    {
        $currentBufferLevel = ob_get_level();
        ob_start();
        include $file;
        $output = ob_get_clean();
        while (ob_get_level() > $currentBufferLevel) {
            @ob_end_clean();
        }
        return $output;
    }
}