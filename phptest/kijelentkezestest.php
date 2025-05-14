<?php
namespace App;

use PHPUnit\Framework\TestCase;

class KijelentkezesTest extends TestCase
{
    protected function setUp(): void
    {
        // Session inicializálása
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        session_unset();
        session_destroy();
    }

    public function testSessionCleared()
    {
        // Session adatok beállítása
        $_SESSION['felhasznalo_id'] = 1;
        $_SESSION['test_key'] = 'test_value';

        ob_start();
        include 'kijelentkezes.php';
        ob_get_clean();

        $this->assertEmpty($_SESSION);
    }

    public function testNoOutputBeforeRedirect()
    {
        ob_start();
        include 'kijelentkezes.php';
        $output = ob_get_clean();

        $this->assertEmpty($output);
    }
}