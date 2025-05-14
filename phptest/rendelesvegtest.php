<?php
namespace App;

use PHPUnit\Framework\TestCase;

class RendelesVegTest extends TestCase
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

        // Táblák létrehozása
        $this->conn->query("CREATE TABLE IF NOT EXISTS tetelek (
            felhasznalo_id INT,
            etel_id INT,
            darab INT NOT NULL,
            PRIMARY KEY (felhasznalo_id, etel_id)
        )");
        $this->conn->query("CREATE TABLE IF NOT EXISTS etel (
            id INT PRIMARY KEY AUTO_INCREMENT,
            nev VARCHAR(255),
            egyseg_ar INT
        )");
        $this->conn->query("CREATE TABLE IF NOT EXISTS megrendeles (
            id INT PRIMARY KEY AUTO_INCREMENT,
            felhasznalo_id INT,
            leadas_megjegyzes TEXT,
            kezbesites VARCHAR(255),
            leadas_allapota INT,
            leadasdatuma DATETIME
        )");
        $this->conn->query("CREATE TABLE IF NOT EXISTS rendeles_tetel (
            rendeles_id INT,
            termek_id INT,
            mennyiseg INT,
            PRIMARY KEY (rendeles_id, termek_id)
        )");

        // Teszt adatok
        $this->conn->query("INSERT INTO etel (id, nev, egyseg_ar) VALUES (1, 'Margherita Pizza', 2000)");
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
        $this->conn->query("DROP TABLE IF EXISTS rendeles_tetel, megrendeles, tetelek, etel");
        $this->conn->close();
        session_unset();
        session_destroy();
    }

    public function testOrderCreationAndCartClearing()
    {
        $_SESSION['felhasznalo_id'] = 1;

        ob_start();
        include 'rendelesveg.php';
        $output = ob_get_clean();

        // Ellenőrizzük a kimenetet
        $this->assertStringContainsString('Rendelés sikeresen leadva!', $output);

        // Ellenőrizzük a rendelést
        $order = $this->conn->query("SELECT * FROM megrendeles WHERE felhasznalo_id = 1")->fetch_assoc();
        $this->assertNotNull($order);
        $this->assertEquals('szállítás', $order['kezbesites']);
        $this->assertEquals(0, $order['leadas_allapota']);

        // Ellenőrizzük a rendelés tételeit
        $item = $this->conn->query("SELECT * FROM rendeles_tetel WHERE rendeles_id = {$order['id']} AND termek_id = 1")->fetch_assoc();
        $this->assertEquals(2, $item['mennyiseg']);

        // Ellenőrizzük, hogy a kosár üres
        $cart = $this->conn->query("SELECT * FROM tetelek WHERE felhasznalo_id = 1");
        $this->assertEquals(0, $cart->num_rows);
    }

    public function testEmptyCartOrder()
    {
        $_SESSION['felhasznalo_id'] = 2; // Felhasználó üres kosárral

        ob_start();
        include 'rendelesveg.php';
        $output = ob_get_clean();

        $this->assertStringContainsString('Rendelés sikeresen leadva!', $output);

        $order = $this->conn->query("SELECT * FROM megrendeles WHERE felhasznalo_id = 2")->fetch_assoc();
        $this->assertNotNull($order);

        $items = $this->conn->query("SELECT * FROM rendeles_tetel WHERE rendeles_id = {$order['id']}");
        $this->assertEquals(0, $items->num_rows);
    }

    public function testRedirectForGuestUser()
    {
        unset($_SESSION['felhasznalo_id']);

        ob_start();
        include 'rendelesveg.php';
        $output = ob_get_clean();

        $this->assertEmpty($output); // Nincs kimenet átirányítás miatt
        $headers = headers_list();
        $locationHeader = array_filter($headers, fn($header) => strpos($header, 'Location: bejelentkezes.php') !== false);
        $this->assertNotEmpty($locationHeader);
    }

    public function testContentTypeHeader()
    {
        $_SESSION['felhasznalo_id'] = 1;

        ob_start();
        include 'rendelesveg.php';
        $output = ob_get_clean();

        $headers = headers_list();
        $contentTypeHeader = array_filter($headers, fn($header) => strpos($header, 'Content-Type: text/html') !== false);
        $this->assertNotEmpty($contentTypeHeader);
    }
}