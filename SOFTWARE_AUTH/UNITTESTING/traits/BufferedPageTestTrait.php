<?php
// -------------------------------------------------------------
// Trait: BufferedPageTestTrait
// Purpose: Capture page output safely without leaking output buffers.
// -------------------------------------------------------------

trait BufferedPageTestTrait
{
    public function captureOutput(string $file): string
    {
        $currentBufferLevel = ob_get_level();

        ob_start();
        try {
            include $file;
        } catch (\Throwable $e) {
            // Ensure output buffer is properly cleaned on error
            while (ob_get_level() > $currentBufferLevel) {
                ob_end_clean();
            }
            throw $e;
        }

        $output = ob_get_clean();

        while (ob_get_level() > $currentBufferLevel) {
            ob_end_clean();
        }

        return $output;
    }
}