<?php
namespace App;

use PHPUnit\Framework\TestCase;

class GetCartCountTest extends TestCase
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
        session_start();
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $this->conn->query("DROP TABLE tetelek");
        $this->conn->close();
        session_unset();
        session_destroy();
    }

    public function testCartCountLoggedInUser()
    {
        global $conn;
        $conn = $this->conn;
        $_SESSION['felhasznalo_id'] = 1;

        ob_start();
        include 'get_cart_count.php';
        $output = ob_get_clean();
        $result = json_decode($output, true);

        $this->assertEquals(2, $result['count']);
    }

    public function testCartCountGuestUser()
    {
        global $conn;
        $conn = $this->conn;
        unset($_SESSION['felhasznalo_id']);
        $_SESSION['kosar'] = ['1' => 3, '2' => 2];

        ob_start();
        include 'get_cart_count.php';
        $output = ob_get_clean();
        $result = json_decode($output, true);

        $this->assertEquals(5, $result['count']);
    }

    public function testCartCountEmpty()
    {
        global $conn;
        $conn = $this->conn;
        unset($_SESSION['felhasznalo_id']);
        unset($_SESSION['kosar']);

        ob_start();
        include 'get_cart_count.php';
        $output = ob_get_clean();
        $result = json_decode($output, true);

        $this->assertEquals(0, $result['count']);
    }

    public function testCartCountDatabaseError()
    {
        global $conn;
        $conn = new \mysqli('invalid_host', 'root', '', $this->testDbName);
        $_SESSION['felhasznalo_id'] = 1;

        ob_start();
        include 'get_cart_count.php';
        $output = ob_get_clean();
        $result = json_decode($output, true);

        $this->assertEquals(0, $result['count']);
    }
}