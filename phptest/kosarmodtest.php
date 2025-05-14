<?php
namespace App;

use PHPUnit\Framework\TestCase;

class UpdateCartTest extends TestCase
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
    }

    public function testIncreaseCartItemLoggedIn()
    {
        $_SESSION['felhasznalo_id'] = 1;
        $input = json_encode(['itemId' => 1, 'action' => 'increase']);
        $GLOBALS['HTTP_RAW_POST_DATA'] = $input;

        ob_start();
        include 'update_cart.php';
        $output = ob_get_clean();
        $result = json_decode($output, true);

        $this->assertTrue($result['success']);
        $this->assertEquals(3, $result['newQuantity']);

        $check = $this->conn->query("SELECT darab FROM tetelek WHERE felhasznalo_id = 1 AND etel_id = 1")->fetch_assoc();
        $this->assertEquals(3, $check['darab']);
    }

    public function testDecreaseCartItemLoggedInToZero()
    {
        $_SESSION['felhasznalo_id'] = 1;
        $input = json_encode(['itemId' => 1, 'action' => 'decrease']);
        $GLOBALS['HTTP_RAW_POST_DATA'] = $input;

        ob_start();
        include 'update_cart.php';
        $output = ob_get_clean();
        $result = json_decode($output, true);

        $this->assertTrue($result['success']);
        $this->assertEquals(1, $result['newQuantity']);

        $input = json_encode(['itemId' => 1, 'action' => 'decrease']);
        $GLOBALS['HTTP_RAW_POST_DATA'] = $input;

        ob_start();
        include 'update_cart.php';
        $output = ob_get_clean();
        $result = json_decode($output, true);

        $this->assertTrue($result['success']);
        $this->assertEquals(0, $result['newQuantity']);

        $check = $this->conn->query("SELECT darab FROM tetelek WHERE felhasznalo_id = 1 AND etel_id = 1");
        $this->assertEquals(0, $check->num_rows);
    }

    public function testIncreaseCartItemGuest()
    {
        unset($_SESSION['felhasznalo_id']);
        $_SESSION['kosar'] = ['1' => 2];
        $input = json_encode(['itemId' => 1, 'action' => 'increase']);
        $GLOBALS['HTTP_RAW_POST_DATA'] = $input;

        ob_start();
        include 'update_cart.php';
        $output = ob_get_clean();
        $result = json_decode($output, true);

        $this->assertTrue($result['success']);
        $this->assertEquals(3, $result['newQuantity']);
        $this->assertEquals(3, $_SESSION['kosar']['1']);
    }

    public function testDecreaseCartItemGuestToZero()
    {
        unset($_SESSION['felhasznalo_id']);
        $_SESSION['kosar'] = ['1' => 1];
        $input = json_encode(['itemId' => 1, 'action' => 'decrease']);
        $GLOBALS['HTTP_RAW_POST_DATA'] = $input;

        ob_start();
        include 'update_cart.php';
        $output = ob_get_clean();
        $result = json_decode($output, true);

        $this->assertTrue($result['success']);
        $this->assertEquals(0, $result['newQuantity']);
        $this->assertArrayNotHasKey('1', $_SESSION['kosar']);
    }

    public function testInvalidInput()
    {
        $_SESSION['felhasznalo_id'] = 1;
        $GLOBALS['HTTP_RAW_POST_DATA'] = json_encode(['itemId' => null, 'action' => 'increase']);

        ob_start();
        include 'update_cart.php';
        $output = ob_get_clean();
        $result = json_decode($output, true);

        $this->assertFalse($result['success']);
        $this->assertEquals(0, $result['newQuantity']);
    }
}