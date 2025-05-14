<?php
namespace App;

use PHPUnit\Framework\TestCase;

class KezdolapTest extends TestCase
{
    private $conn;
    private $testDbName = 'flavorwave';

    protected function setUp(): void
    {
        // Adatbázis kapcsolat
        $this->conn = new \mysqli('localhost', 'root', '', $this->testDbName);
        if ($this->conn->connect_error) {
            $this->fail('Adatbázis kapcsolat sikertelen: ' . $this->conn->connect_error);
        }
        $this->conn->set_charset("utf8mb4");

        // Globális $conn
        global $conn;
        $conn = $this->conn;

        // Session inicializálása
        session_start();
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $this->conn->close();
        session_unset();
        session_destroy();
    }

    public function testKezdolapLoggedInUser()
    {
        $_SESSION['felhasznalo_id'] = 1;

        ob_start();
        include 'kezdolap.php';
        $output = ob_get_clean();

        $this->assertStringContainsString('Tekintsd meg a rendeléseidet', $output);
        $this->assertStringContainsString('Küldd el a véleményed!', $output);
        $this->assertStringNotContainsString('Regisztrálj most', $output);
        $this->assertStringContainsString('<h1>Friss, Forró, Finom</h1>', $output);
        $this->assertStringContainsString('Népszerű Ételeink', $output);
    }

    public function testKezdolapGuestUser()
    {
        unset($_SESSION['felhasznalo_id']);

        ob_start();
        include 'kezdolap.php';
        $output = ob_get_clean();

        $this->assertStringContainsString('Regisztrálj most', $output);
        $this->assertStringContainsString('jelentkezz be', $output);
        $this->assertStringNotContainsString('Tekintsd meg a rendeléseidet', $output);
        $this->assertStringContainsString('<h1>Friss, Forró, Finom</h1>', $output);
        $this->assertStringContainsString('Népszerű Ételeink', $output);
    }

    public function testKezdolapHtmlStructure()
    {
        ob_start();
        include 'kezdolap.php';
        $output = ob_get_clean();

        $this->assertStringContainsString('<title>FlavorWave</title>', $output);
        $this->assertStringContainsString('coupon-slider', $output);
        $this->assertStringContainsString('weekly-deals', $output);
        $this->assertStringContainsString('steps-container', $output);
        $this->assertStringContainsString('popular-foods-gallery', $output);
        $this->assertStringContainsString('shaker-master-container', $output);
        $this->assertStringContainsString('why-us', $output);
        $this->assertStringContainsString('footer', $output);
    }
}