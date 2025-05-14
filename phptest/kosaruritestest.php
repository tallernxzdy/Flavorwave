<?php
namespace App;

use PHPUnit\Framework\TestCase;

class ClearCartTest extends TestCase
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

        // Tábla létrehozása
        $this->conn->query("CREATE TABLE IF NOT EXISTS tetelek (
            felhasznalo_id INT,
            etel_id INT,
            darab INT NOT NULL,
            PRIMARY KEY (felhasznalo_id, etel_id)
        )");
        $this->conn->query("INSERT INTO tetelek (felhasznalo_id, etel_id, darab) VALUES (1, 1, 2)");

        // Session inicializálása
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];

        // Globális $conn
        global $conn;
        $conn = $this->conn;
    }

    protected function tearDown(): void
    {
        $this->conn->query("DROP TABLE tetelek");
        $this->conn->close();
        session_unset();
        session_destroy();
        $_COOKIE = [];
    }

    public function testClearCartLoggedInUser()
    {
        $_SESSION['felhasznalo_id'] = 1;

        ob_start();
        include 'clear_cart.php';
        $output = ob_get_clean();
        $result = json_decode($output, true);

        $this->assertTrue($result['success']);
        $check = $this->conn->query("SELECT * FROM tetelek WHERE felhasznalo_id = 1");
        $this->assertEquals(0, $check->num_rows);

        $headers = headers_list();
        $contentTypeHeader = array_filter($headers, fn($header) => strpos($header, 'Content-Type: application/json') !== false);
        $this->assertNotEmpty($contentTypeHeader);
    }

    public function testClearCartGuestUser()
    {
        unset($_SESSION['felhasznalo_id']);
        $_SESSION['kosar'] = ['1' => 2];
        $_COOKIE['guest_cart'] = 'some_data';

        ob_start();
        include 'clear_cart.php';
        $output = ob_get_clean();
        $result = json_decode($output, true);

        $this->assertTrue($result['success']);
        $this->assertArrayNotHasKey('kosar', $_SESSION);
        $this->assertArrayHasKey('guest_cart', $_COOKIE);
        $this->assertEquals('', $_COOKIE['guest_cart']);

        $headers = headers_list();
        $contentTypeHeader = array_filter($headers, fn($header) => strpos($header, 'Content-Type: application/json') !== false);
        $this->assertNotEmpty($contentTypeHeader);
    }

    public function testClearCartNoItems()
    {
        unset($_SESSION['felhasznalo_id']);
        unset($_SESSION['kosar']);

        ob_start();
        include 'clear_cart.php';
        $output = ob_get_clean();
        $result = json_decode($output, true);

        $this->assertTrue($result['success']);
        $this->assertArrayNotHasKey('kosar', $_SESSION);

        $headers = headers_list();
        $contentTypeHeader = array_filter($headers, fn($header) => strpos($header, 'Content-Type: application/json') !== false);
        $this->assertNotEmpty($contentTypeHeader);
    }
}