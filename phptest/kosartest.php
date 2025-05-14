<?php
namespace App;

use PHPUnit\Framework\TestCase;

class KosarTest extends TestCase
{
    private $conn;
    private $testDbName = 'flavorwave';

    protected function setUp(): void
    {
        // Adatbázis kapcsolat inicializálása
        $this->conn = new \mysqli('localhost', 'root', '', $this->testDbName);
        if ($this->conn->connect_error) {
            $this->fail('Adatbázis kapcsolat sikertelen: ' . $this->conn->connect_error);
        }

        // Teszt adatbázis inicializálása
        $this->initializeTestDatabase();

        // Session inicializálása
        session_start();
        $_SESSION = [];
        $_COOKIE = [];
    }

    protected function tearDown(): void
    {
        // Adatbázis tisztítása
        $this->conn->query('TRUNCATE TABLE tetelek');
        $this->conn->query('TRUNCATE TABLE etel');
        $this->conn->query('TRUNCATE TABLE felhasznalo');
        $this->conn->close();
        session_unset();
        session_destroy();
    }

    private function initializeTestDatabase()
    {
        // Teszt adatok feltöltése
        $this->conn->query("INSERT INTO felhasznalo (id, felhasznalo_nev, jog_szint) VALUES (1, 'testuser', 0)");
        $this->conn->query("INSERT INTO etel (id, nev, egyseg_ar, kep_url, kategoria_id) 
                            VALUES (1, 'Margherita', 2000, 'margherita.jpg', 4)");
    }

    public function testAddItemToCartLoggedInUser()
    {
        // Bejelentkezett felhasználó beállítása
        $_SESSION['felhasznalo_id'] = 1;

        // POST kérés szimulálása
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['etel_id'] = 1;

        // Kimenet rögzítése
        ob_start();
        include 'kosar.php';
        $output = ob_get_clean();

        // Ellenőrzés: átirányítás
        $this->assertTrue(headers_sent());
        $this->assertEquals('Location: kosar.php', headers_list()[0]);

        // Adatbázis ellenőrzése
        $result = $this->conn->query("SELECT darab FROM tetelek WHERE felhasznalo_id = 1 AND etel_id = 1");
        $this->assertEquals(1, $result->num_rows);
        $row = $result->fetch_assoc();
        $this->assertEquals(1, $row['darab']);
    }

    public function testAddExistingItemToCartLoggedInUser()
    {
        // Bejelentkezett felhasználó és meglévő tétel beállítása
        $_SESSION['felhasznalo_id'] = 1;
        $this->conn->query("INSERT INTO tetelek (felhasznalo_id, etel_id, darab) VALUES (1, 1, 1)");

        // POST kérés szimulálása
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['etel_id'] = 1;

        // Kimenet rögzítése
        ob_start();
        include 'kosar.php';
        $output = ob_get_clean();

        // Ellenőrzés: átirányítás
        $this->assertTrue(headers_sent());
        $this->assertEquals('Location: kosar.php', headers_list()[0]);

        // Adatbázis ellenőrzése
        $result = $this->conn->query("SELECT darab FROM tetelek WHERE felhasznalo_id = 1 AND etel_id = 1");
        $row = $result->fetch_assoc();
        $this->assertEquals(2, $row['darab']);
    }

    public function testAddItemToCartGuestUser()
    {
        // Vendég felhasználó (nincs felhasznalo_id)
        unset($_SESSION['felhasznalo_id']);
        $_SESSION['kosar'] = [];

        // POST kérés szimulálása
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['etel_id'] = 1;

        // Kimenet rögzítése
        ob_start();
        include 'kosar.php';
        $output = ob_get_clean();

        // Ellenőrzés: átirányítás
        $this->assertTrue(headers_sent());
        $this->assertEquals('Location: kosar.php', headers_list()[0]);

        // Session és cookie ellenőrzése
        $this->assertArrayHasKey('kosar', $_SESSION);
        $this->assertEquals(1, $_SESSION['kosar'][1]);
        $this->assertArrayHasKey('guest_cart', $_COOKIE);
        $this->assertEquals(json_encode(['1' => 1]), $_COOKIE['guest_cart']);
    }

    public function testRenderCartLoggedInUser()
    {
        // Bejelentkezett felhasználó és kosár tétel beállítása
        $_SESSION['felhasznalo_id'] = 1;
        $this->conn->query("INSERT INTO tetelek (felhasznalo_id, etel_id, darab) VALUES (1, 1, 2)");

        // Kimenet rögzítése
        ob_start();
        include 'kosar.php';
        $output = ob_get_clean();

        // Ellenőrzés: HTML tartalom
        $this->assertStringContainsString('<h1>Kosár</h1>', $output);
        $this->assertStringContainsString('Margherita', $output);
        $this->assertStringContainsString('4000 Ft', $output);
        $this->assertStringContainsString('Végösszeg: 4000 Ft', $output);
        $this->assertStringContainsString('clearCartBtn', $output);
        $this->assertStringContainsString('Rendelés', $output);
        $this->assertStringNotContainsString('Bejelentkezés', $output);
    }

    public function testRenderCartGuestUser()
    {
        // Vendég felhasználó és kosár beállítása
        unset($_SESSION['felhasznalo_id']);
        $_SESSION['kosar'] = [1 => 3];

        // Kimenet rögzítése
        ob_start();
        include 'kosar.php';
        $output = ob_get_clean();

        // Ellenőrzés: HTML tartalom
        $this->assertStringContainsString('<h1>Kosár</h1>', $output);
        $this->assertStringContainsString('Margherita', $output);
        $this->assertStringContainsString('6000 Ft', $output);
        $this->assertStringContainsString('Végösszeg: 6000 Ft', $output);
        $this->assertStringContainsString('clearCartBtn', $output);
        $this->assertStringContainsString('Bejelentkezés', $output);
        $this->assertStringNotContainsString('Rendelés', $output);
    }

    public function testRenderEmptyCartLoggedInUser()
    {
        // Bejelentkezett felhasználó üres kosárral
        $_SESSION['felhasznalo_id'] = 1;

        // Kimenet rögzítése
        ob_start();
        include 'kosar.php';
        $output = ob_get_clean();

        // Ellenőrzés: HTML tartalom
        $this->assertStringContainsString('<h1>Kosár</h1>', $output);
        $this->assertStringContainsString('A kosár üres, rendeléshez adjon hozzá termékeket!', $output);
        $this->assertStringNotContainsString('clearCartBtn', $output);
        $this->assertStringNotContainsString('Rendelés', $output);
    }

    public function testRenderEmptyCartGuestUser()
    {
        // Vendég felhasználó üres kosárral
        unset($_SESSION['felhasznalo_id']);
        $_SESSION['kosar'] = [];

        // Kimenet rögzítése
        ob_start();
        include 'kosar.php';
        $output = ob_get_clean();

        // Ellenőrzés: HTML tartalom
        $this->assertStringContainsString('<h1>Kosár</h1>', $output);
        $this->assertStringContainsString('A kosár üres, rendeléshez adjon hozzá termékeket!', $output);
        $this->assertStringContainsString('Bejelentkezés', $output);
        $this->assertStringNotContainsString('clearCartBtn', $output);
        $this->assertStringNotContainsString('Rendelés', $output);
    }

    public function testInvalidItemIdForGuestUser()
    {
        // Vendég felhasználó és érvénytelen étel ID
        unset($_SESSION['felhasznalo_id']);
        $_SESSION['kosar'] = [999 => 1];

        // Kimenet rögzítése
        ob_start();
        include 'kosar.php';
        $output = ob_get_clean();

        // Ellenőrzés: üres kosár
        $this->assertStringContainsString('<h1>Kosár</h1>', $output);
        $this->assertStringContainsString('A kosár üres, rendeléshez adjon hozzá termékeket!', $output);
        $this->assertStringContainsString('Bejelentkezés', $output);
        $this->assertStringNotContainsString('clearCartBtn', $output);
    }
}