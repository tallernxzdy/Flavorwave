<?php
namespace App;

use PHPUnit\Framework\TestCase;
use Mockery;

class BejelentkezesTest extends TestCase
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
    }

    protected function tearDown(): void
    {
        // Adatbázis tisztítása
        $this->conn->query('TRUNCATE TABLE felhasznalo');
        $this->conn->query('TRUNCATE TABLE etel');
        $this->conn->query('TRUNCATE TABLE tetelek');
        $this->conn->close();
        session_unset();
        session_destroy();
        Mockery::close();
    }

    private function initializeTestDatabase()
    {
        // Teszt adatok feltöltése
        $hashedPassword = password_hash('1', PASSWORD_DEFAULT);
        $this->conn->query("INSERT INTO felhasznalo (id, felhasznalo_nev, jelszo, jog_szint) VALUES (1, 'main', '$hashedPassword', 0)");
        $this->conn->query("INSERT INTO etel (id, nev, egyseg_ar) VALUES (1, 'Margherita', 2000)");
    }

    public function testSuccessfulLogin()
    {
        // Mock reCAPTCHA válasz
        $mock = Mockery::mock('overload:file_get_contents');
        $mock->shouldReceive('file_get_contents')->andReturn(json_encode(['success' => true]));

        // POST kérés szimulálása
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['g-recaptcha-response'] = 'valid-token';
        $_POST['username'] = 'main';
        $_POST['password'] = '1';

        // Kimenet rögzítése
        ob_start();
        include 'bejelentkezes.php';
        $output = ob_get_clean();

        // Ellenőrzés: sikeres bejelentkezés
        $this->assertArrayHasKey('felhasznalo_id', $_SESSION);
        $this->assertEquals(1, $_SESSION['felhasznalo_id']);
        $this->assertEquals('main', $_SESSION['felhasznalo_nev']);
        $this->assertEquals(0, $_SESSION['jog_szint']);
        $this->assertStringNotContainsString('error', $output);
    }

    public function testFailedLoginInvalidPassword()
    {
        // Mock reCAPTCHA válasz
        $mock = Mockery::mock('overload:file_get_contents');
        $mock->shouldReceive('file_get_contents')->andReturn(json_encode(['success' => true]));

        // POST kérés szimulálása
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['g-recaptcha-response'] = 'valid-token';
        $_POST['username'] = 'main';
        $_POST['password'] = 'wrong';

        // Kimenet rögzítése
        ob_start();
        include 'bejelentkezes.php';
        $output = ob_get_clean();

        // Ellenőrzés: hibás jelszó
        $this->assertStringContainsString('Hibás jelszó vagy felhasználónév!', $output);
        $this->assertArrayNotHasKey('felhasznalo_id', $_SESSION);
    }

    public function testFailedLoginNonExistentUser()
    {
        // Mock reCAPTCHA válasz
        $mock = Mockery::mock('overload:file_get_contents');
        $mock->shouldReceive('file_get_contents')->andReturn(json_encode(['success' => true]));

        // POST kérés szimulálása
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['g-recaptcha-response'] = 'valid-token';
        $_POST['username'] = 'nonexistent';
        $_POST['password'] = '1';

        // Kimenet rögzítése
        ob_start();
        include 'bejelentkezes.php';
        $output = ob_get_clean();

        // Ellenőrzés: nem létező felhasználó
        $this->assertStringContainsString('Nincs ilyen névvel rendelkező felhasználó!', $output);
        $this->assertArrayNotHasKey('felhasznalo_id', $_SESSION);
    }

    public function testFailedLoginInvalidRecaptcha()
    {
        // Mock reCAPTCHA válasz
        $mock = Mockery::mock('overload:file_get_contents');
        $mock->shouldReceive('file_get_contents')->andReturn(json_encode(['success' => false]));

        // POST kérés szimulálása
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['g-recaptcha-response'] = 'invalid-token';
        $_POST['username'] = 'main';
        $_POST['password'] = '1';

        // Kimenet rögzítése
        ob_start();
        include 'bejelentkezes.php';
        $output = ob_get_clean();

        // Ellenőrzés: sikertelen reCAPTCHA
        $this->assertStringContainsString('reCAPTCHA ellenőrzés sikertelen', $output);
        $this->assertArrayNotHasKey('felhasznalo_id', $_SESSION);
    }

    public function testCartMergeWithSessionCart()
    {
        // Mock reCAPTCHA válasz
        $mock = Mockery::mock('overload:file_get_contents');
        $mock->shouldReceive('file_get_contents')->andReturn(json_encode(['success' => true]));

        // Vendég kosár beállítása session-ben
        $_SESSION['kosar'] = [1 => 2]; // 2 db Margherita

        // POST kérés szimulálása
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['g-recaptcha-response'] = 'valid-token';
        $_POST['username'] = 'main';
        $_POST['password'] = '1';

        // Kimenet rögzítése
        ob_start();
        include 'bejelentkezes.php';
        $output = ob_get_clean();

        // Ellenőrzés: sikeres bejelentkezés és kosár egyesítés
        $this->assertArrayHasKey('felhasznalo_id', $_SESSION);
        $this->assertEquals(1, $_SESSION['felhasznalo_id']);

        // Adatbázis ellenőrzése: tetelek tábla
        $result = $this->conn->query("SELECT darab FROM tetelek WHERE felhasznalo_id = 1 AND etel_id = 1");
        $this->assertEquals(1, $result->num_rows);
        $row = $result->fetch_assoc();
        $this->assertEquals(2, $row['darab']);

        // Ellenőrzés: vendég kosár törlése
        $this->assertArrayNotHasKey('kosar', $_SESSION);
    }

    public function testCartMergeWithCookieCart()
    {
        // Mock reCAPTCHA válasz
        $mock = Mockery::mock('overload:file_get_contents');
        $mock->shouldReceive('file_get_contents')->andReturn(json_encode(['success' => true]));

        // Vendég kosár beállítása cookie-ban
        $_COOKIE['guest_cart'] = json_encode([1 => 3]); // 3 db Margherita

        // POST kérés szimulálása
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['g-recaptcha-response'] = 'valid-token';
        $_POST['username'] = 'main';
        $_POST['password'] = '1';

        // Kimenet rögzítése
        ob_start();
        include 'bejelentkezes.php';
        $output = ob_get_clean();

        // Ellenőrzés: sikeres bejelentkezés és kosár egyesítés
        $this->assertArrayHasKey('felhasznalo_id', $_SESSION);
        $this->assertEquals(1, $_SESSION['felhasznalo_id']);

        // Adatbázis ellenőrzése: tetelek tábla
        $result = $this->conn->query("SELECT darab FROM tetelek WHERE felhasznalo_id = 1 AND etel_id = 1");
        $this->assertEquals(1, $result->num_rows);
        $row = $result->fetch_assoc();
        $this->assertEquals(3, $row['darab']);

        // Ellenőrzés: cookie törlése
        $this->assertArrayNotHasKey('guest_cart', $_COOKIE);
    }

    public function testCartMergeWithExistingUserCart()
    {
        // Mock reCAPTCHA válasz
        $mock = Mockery::mock('overload:file_get_contents');
        $mock->shouldReceive('file_get_contents')->andReturn(json_encode(['success' => true]));

        // Felhasználói kosár inicializálása adatbázisban
        $this->conn->query("INSERT INTO tetelek (felhasznalo_id, etel_id, darab) VALUES (1, 1, 1)");

        // Vendég kosár beállítása session-ben
        $_SESSION['kosar'] = [1 => 2]; // 2 db Margherita

        // POST kérés szimulálása
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['g-recaptcha-response'] = 'valid-token';
        $_POST['username'] = 'main';
        $_POST['password'] = '1';

        // Kimenet rögzítése
        ob_start();
        include 'bejelentkezes.php';
        $output = ob_get_clean();

        // Ellenőrzés: sikeres bejelentkezés és kosár egyesítés
        $this->assertArrayHasKey('felhasznalo_id', $_SESSION);
        $this->assertEquals(1, $_SESSION['felhasznalo_id']);

        // Adatbázis ellenőrzése: tetelek tábla (1 + 2 = 3 db)
        $result = $this->conn->query("SELECT darab FROM tetelek WHERE felhasznalo_id = 1 AND etel_id = 1");
        $this->assertEquals(1, $result->num_rows);
        $row = $result->fetch_assoc();
        $this->assertEquals(3, $row['darab']);

        // Ellenőrzés: vendég kosár törlése
        $this->assertArrayNotHasKey('kosar', $_SESSION);
    }

    public function testCartMergeInvalidItem()
    {
        // Mock reCAPTCHA válasz
        $mock = Mockery::mock('overload:file_get_contents');
        $mock->shouldReceive('file_get_contents')->andReturn(json_encode(['success' => true]));

        // Vendég kosár beállítása érvénytelen étel ID-vel
        $_SESSION['kosar'] = [999 => 2]; // Nem létező étel

        // POST kérés szimulálása
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['g-recaptcha-response'] = 'valid-token';
        $_POST['username'] = 'main';
        $_POST['password'] = '1';

        // Kimenet rögzítése
        ob_start();
        include 'bejelentkezes.php';
        $output = ob_get_clean();

        // Ellenőrzés: sikeres bejelentkezés
        $this->assertArrayHasKey('felhasznalo_id', $_SESSION);
        $this->assertEquals(1, $_SESSION['felhasznalo_id']);

        // Adatbázis ellenőrzése: nincs beszúrás
        $result = $this->conn->query("SELECT * FROM tetelek WHERE felhasznalo_id = 1");
        $this->assertEquals(0, $result->num_rows);

        // Ellenőrzés: vendég kosár törlése
        $this->assertArrayNotHasKey('kosar', $_SESSION);
    }
}