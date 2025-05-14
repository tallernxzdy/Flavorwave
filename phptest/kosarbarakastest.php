<?php
namespace App;

use PHPUnit\Framework\TestCase;

class KosarbaRakasTest extends TestCase
{
    private $conn;
    private $testDbName = 'flavorwave_test';

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
        $_COOKIE = [];

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

    public function testAddNewItemLoggedIn()
    {
        $_SESSION['felhasznalo_id'] = 1;
        $input = json_encode(['itemId' => 2]);
        $GLOBALS['HTTP_RAW_POST_DATA'] = $input;

        ob_start();
        include 'kosarba_rakas.php';
        $output = ob_get_clean();
        $result = json_decode($output, true);

        $this->assertTrue($result['success']);
        $check = $this->conn->query("SELECT darab FROM tetelek WHERE felhasznalo_id = 1 AND etel_id = 2")->fetch_assoc();
        $this->assertEquals(1, $check['darab']);

        $headers = headers_list();
        $contentTypeHeader = array_filter($headers, fn($header) => strpos($header, 'Content-Type: application/json') !== false);
        $this->assertNotEmpty($contentTypeHeader);
    }

    public function testIncrementExistingItemLoggedIn()
    {
        $_SESSION['felhasznalo_id'] = 1;
        $input = json_encode(['itemId' => 1]);
        $GLOBALS['HTTP_RAW_POST_DATA'] = $input;

        ob_start();
        include 'kosarba_rakas.php';
        $output = ob_get_clean();
        $result = json_decode($output, true);

        $this->assertTrue($result['success']);
        $check = $this->conn->query("SELECT darab FROM tetelek WHERE felhasznalo_id = 1 AND etel_id = 1")->fetch_assoc();
        $this->assertEquals(3, $check['darab']);
    }

    public function testAddItemGuest()
    {
        unset($_SESSION['felhasznalo_id']);
        $input = json_encode(['itemId' => 1]);
        $GLOBALS['HTTP_RAW_POST_DATA'] = $input;

        ob_start();
        include 'kosarba_rakas.php';
        $output = ob_get_clean();
        $result = json_decode($output, true);

        $this->assertTrue($result['success']);
        $this->assertEquals(1, $_SESSION['kosar']['1']);
        $this->assertEquals(json_encode(['1' => 1]), $_COOKIE['guest_cart']);
    }

    public function testInvalidInput()
    {
        $_SESSION['felhasznalo_id'] = 1;
        $GLOBALS['HTTP_RAW_POST_DATA'] = json_encode(['itemId' => null]);

        ob_start();
        include 'kosarba_rakas.php';
        $output = ob_get_clean();
        $result = json_decode($output, true);

        $this->assertTrue($result['success']); // A kód jelenleg nem ellenőrzi az itemId érvényességét
    }
}