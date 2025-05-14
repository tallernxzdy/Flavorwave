<?php
namespace App;

use PHPUnit\Framework\TestCase;

class NavbarTest extends TestCase
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

        // Globális $conn beállítása
        global $conn;
        $conn = $this->conn;
    }

    protected function tearDown(): void
    {
        // Adatbázis tisztítása
        $this->conn->query('TRUNCATE TABLE tetelek');
        $this->conn->query('TRUNCATE TABLE felhasznalo');
        $this->conn->close();
        session_unset();
        session_destroy();
    }

    private function initializeTestDatabase()
    {
        // Teszt adatok feltöltése
        $this->conn->query("INSERT INTO felhasznalo (id, felhasznalo_nev, jog_szint) VALUES (1, 'user', 0)");
        $this->conn->query("INSERT INTO felhasznalo (id, felhasznalo_nev, jog_szint) VALUES (2, 'admin', 1)");
        $this->conn->query("INSERT INTO felhasznalo (id, felhasznalo_nev, jog_szint) VALUES (3, 'worker', 2)");
        $this->conn->query("INSERT INTO tetelek (felhasznalo_id, etel_id, darab) VALUES (1, 1, 2)");
    }

    public function testGetCartItemCountLoggedInUser()
    {
        // Bejelentkezett felhasználó beállítása
        $_SESSION['felhasznalo_id'] = 1;

        // getCartItemCount függvény tesztelése
        include 'navbar.php';
        $count = getCartItemCount($this->conn);

        // Ellenőrzés: kosárban lévő tételek száma
        $this->assertEquals(2, $count);
    }

    public function testGetCartItemCountGuestUser()
    {
        // Vendég felhasználó és session kosár beállítása
        unset($_SESSION['felhasznalo_id']);
        $_SESSION['kosar'] = [1 => 3, 2 => 1];

        // getCartItemCount függvény tesztelése
        include 'navbar.php';
        $count = getCartItemCount($this->conn);

        // Ellenőrzés: kosárban lévő tételek száma
        $this->assertEquals(4, $count);
    }

    public function testGetCartItemCountEmptyCart()
    {
        // Üres kosár beállítása
        unset($_SESSION['felhasznalo_id']);
        $_SESSION['kosar'] = [];

        // getCartItemCount függvény tesztelése
        include 'navbar.php';
        $count = getCartItemCount($this->conn);

        // Ellenőrzés: üres kosár
        $this->assertEquals(0, $count);
    }

    public function testRenderNavbarGuestUser()
    {
        // Vendég felhasználó beállítása
        unset($_SESSION['felhasznalo_id']);
        unset($_SESSION['felhasznalo_nev']);
        unset($_SESSION['jog_szint']);

        // Kimenet rögzítése
        ob_start();
        include 'navbar.php';
        $output = ob_get_clean();

        // Ellenőrzés: HTML tartalom
        $this->assertStringContainsString('FlavorWave', $output);
        $this->assertStringContainsString('Bejelentkezés', $output);
        $this->assertStringContainsString('Kosár', $output);
        $this->assertStringContainsString('cart-count">0</span>', $output);
        $this->assertStringContainsString('Menü', $output);
        $this->assertStringContainsString('Rendeléseim', $output);
        $this->assertStringNotContainsString('Kijelentkezés', $output);
        $this->assertStringNotContainsString('Admin felület', $output);
        $this->assertStringNotContainsString('Dolgozoi felület', $output);
    }

    public function testRenderNavbarLoggedInUser()
    {
        // Bejelentkezett normál felhasználó beállítása
        $_SESSION['felhasznalo_id'] = 1;
        $_SESSION['felhasznalo_nev'] = 'user';
        $_SESSION['jog_szint'] = 0;

        // Kimenet rögzítése
        ob_start();
        include 'navbar.php';
        $output = ob_get_clean();

        // Ellenőrzés: HTML tartalom
        $this->assertStringContainsString('FlavorWave', $output);
        $this->assertStringContainsString('Kijelentkezés', $output);
        $this->assertStringContainsString('Kosár', $output);
        $this->assertStringContainsString('cart-count">2</span>', $output);
        $this->assertStringContainsString('Menü', $output);
        $this->assertStringContainsString('Rendeléseim', $output);
        $this->assertStringNotContainsString('Bejelentkezés', $output);
        $this->assertStringNotContainsString('Admin felület', $output);
        $this->assertStringNotContainsString('Dolgozoi felület', $output);
    }

    public function testRenderNavbarAdminUser()
    {
        // Bejelentkezett admin felhasználó beállítása
        $_SESSION['felhasznalo_id'] = 2;
        $_SESSION['felhasznalo_nev'] = 'admin';
        $_SESSION['jog_szint'] = 1;

        // Kimenet rögzítése
        ob_start();
        include 'navbar.php';
        $output = ob_get_clean();

        // Ellenőrzés: HTML tartalom
        $this->assertStringContainsString('FlavorWave', $output);
        $this->assertStringContainsString('Kijelentkezés', $output);
        $this->assertStringContainsString('Kosár', $output);
        $this->assertStringContainsString('cart-count">0</span>', $output);
        $this->assertStringContainsString('Menü', $output);
        $this->assertStringContainsString('Rendeléseim', $output);
        $this->assertStringContainsString('Admin felület', $output);
        $this->assertStringNotContainsString('Bejelentkezés', $output);
        $this->assertStringNotContainsString('Dolgozoi felület', $output);
    }

    public function testRenderNavbarWorkerUser()
    {
        // Bejelentkezett dolgozó felhasználó beállítása
        $_SESSION['felhasznalo_id'] = 3;
        $_SESSION['felhasznalo_nev'] = 'worker';
        $_SESSION['jog_szint'] = 2;

        // Kimenet rögzítése
        ob_start();
        include 'navbar.php';
        $output = ob_get_clean();

        // Ellenőrzés: HTML tartalom
        $this->assertStringContainsString('FlavorWave', $output);
        $this->assertStringContainsString('Kijelentkezés', $output);
        $this->assertStringContainsString('Kosár', $output);
        $this->assertStringContainsString('cart-count">0</span>', $output);
        $this->assertStringContainsString('Menü', $output);
        $this->assertStringContainsString('Rendeléseim', $output);
        $this->assertStringContainsString('Dolgozoi felület', $output);
        $this->assertStringNotContainsString('Bejelentkezés', $output);
        $this->assertStringNotContainsString('Admin felület', $output);
    }

    public function testRenderHamburgerMenuGuestUser()
    {
        // Vendég felhasználó beállítása
        unset($_SESSION['felhasznalo_id']);
        unset($_SESSION['felhasznalo_nev']);
        unset($_SESSION['jog_szint']);

        // Kimenet rögzítése
        ob_start();
        include 'navbar.php';
        $output = ob_get_clean();

        // Ellenőrzés: hamburger menü tartalma
        $this->assertStringContainsString('menubar', $output);
        $this->assertStringContainsString('Menü', $output);
        $this->assertStringContainsString('Rendeléseim', $output);
        $this->assertStringContainsString('Bejelentkezés', $output);
        $this->assertStringContainsString('Kosár', $output);
        $this->assertStringContainsString('Profil', $output);
        $this->assertStringNotContainsString('Kijelentkezés', $output);
        $this->assertStringNotContainsString('Admin felület', $output);
        $this->assertStringNotContainsString('Dolgozoi felület', $output);
    }
}