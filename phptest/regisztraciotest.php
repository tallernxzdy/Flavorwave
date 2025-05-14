<?php
namespace App;

use PHPUnit\Framework\TestCase;
use PHPMailer\PHPMailer\PHPMailer;

class RegisztracioTest extends TestCase
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
        $this->conn->query('TRUNCATE TABLE felhasznalo');
        $this->conn->close();
        session_unset();
        session_destroy();
    }

    private function initializeTestDatabase()
    {
        // Teszt adatok feltöltése
        $this->conn->query("INSERT INTO felhasznalo (felhasznalo_nev, email_cim, jelszo, tel_szam) 
                            VALUES ('existinguser', 'existing@example.com', 'hashedpass', '+36201234567')");
    }

    public function testSuccessfulRegistration()
    {
        // Mock PHPMailer
        $mailerMock = $this->createMock(PHPMailer::class);
        $mailerMock->method('send')->willReturn(true);

        // POST kérés szimulálása
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'username' => 'newuser',
            'email' => 'newuser@example.com',
            'password' => 'Test1234',
            'phone' => '+36209876543'
        ];

        // Kimenet rögzítése
        ob_start();
        include 'regisztracio.php';
        $output = ob_get_clean();

        // Ellenőrzés: sikeres üzenet
        $this->assertStringContainsString('Sikeres regisztráció!', $output);
        $this->assertStringContainsString('Bejelentkezés', $output);

        // Adatbázis ellenőrzése
        $result = $this->conn->query("SELECT * FROM felhasznalo WHERE felhasznalo_nev = 'newuser'");
        $this->assertEquals(1, $result->num_rows);
        $user = $result->fetch_assoc();
        $this->assertEquals('newuser@example.com', $user['email_cim']);
        $this->assertEquals('+36209876543', $user['tel_szam']);
        $this->assertTrue(password_verify('Test1234', $user['jelszo']));
    }

    public function testRegistrationWithExistingUsername()
    {
        // POST kérés szimulálása létező felhasználónévvel
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'username' => 'existinguser',
            'email' => 'newuser@example.com',
            'password' => 'Test1234',
            'phone' => '+36209876543'
        ];

        // Kimenet rögzítése
        ob_start();
        include 'regisztracio.php';
        $output = ob_get_clean();

        // Ellenőrzés: hibaüzenet
        $this->assertStringContainsString('Ez a felhasználónév már foglalt!', $output);
        $this->assertStringNotContainsString('Sikeres regisztráció!', $output);

        // Adatbázis ellenőrzése
        $result = $this->conn->query("SELECT * FROM felhasznalo WHERE email_cim = 'newuser@example.com'");
        $this->assertEquals(0, $result->num_rows);
    }

    public function testRegistrationWithInvalidEmail()
    {
        // POST kérés szimulálása érvénytelen emaillel
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'username' => 'newuser',
            'email' => 'invalid-email',
            'password' => 'Test1234',
            'phone' => '+36209876543'
        ];

        // Kimenet rögzítése
        ob_start();
        include 'regisztracio.php';
        $output = ob_get_clean();

        // Ellenőrzés: hibaüzenet
        $this->assertStringContainsString('Érvénytelen email cím formátum!', $output);
        $this->assertStringNotContainsString('Sikeres regisztráció!', $output);

        // Adatbázis ellenőrzése
        $result = $this->conn->query("SELECT * FROM felhasznalo WHERE felhasznalo_nev = 'newuser'");
        $this->assertEquals(0, $result->num_rows);
    }

    public function testRegistrationWithWeakPassword()
    {
        // POST kérés szimulálása gyenge jelszóval
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'username' => 'newuser',
            'email' => 'newuser@example.com',
            'password' => 'weak',
            'phone' => '+36209876543'
        ];

        // Kimenet rögzítése
        ob_start();
        include 'regisztracio.php';
        $output = ob_get_clean();

        // Ellenőrzés: hibaüzenet
        $this->assertStringContainsString('A jelszónak legalább 8 karakter hosszúnak kell lennie!', $output);
        $this->assertStringNotContainsString('Sikeres regisztráció!', $output);

        // Adatbázis ellenőrzése
        $result = $this->conn->query("SELECT * FROM felhasznalo WHERE felhasznalo_nev = 'newuser'");
        $this->assertEquals(0, $result->num_rows);
    }

    public function testRegistrationWithInvalidPhone()
    {
        // POST kérés szimulálása érvénytelen telefonszámmal
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'username' => 'newuser',
            'email' => 'newuser@example.com',
            'password' => 'Test1234',
            'phone' => '12345'
        ];

        // Kimenet rögzítése
        ob_start();
        include 'regisztracio.php';
        $output = ob_get_clean();

        // Ellenőrzés: hibaüzenet
        $this->assertStringContainsString('Érvénytelen telefonszám!', $output);
        $this->assertStringNotContainsString('Sikeres regisztráció!', $output);

        // Adatbázis ellenőrzése
        $result = $this->conn->query("SELECT * FROM felhasznalo WHERE felhasznalo_nev = 'newuser'");
        $this->assertEquals(0, $result->num_rows);
    }

    public function testRenderRegistrationForm()
    {
        // GET kérés szimulálása
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_POST = [];

        // Kimenet rögzítése
        ob_start();
        include 'regisztracio.php';
        $output = ob_get_clean();

        // Ellenőrzés: űrlap megjelenítése
        $this->assertStringContainsString('<h2>Regisztráció</h2>', $output);
        $this->assertStringContainsString('name="username"', $output);
        $this->assertStringContainsString('name="email"', $output);
        $this->assertStringContainsString('name="password"', $output);
        $this->assertStringContainsString('name="phone"', $output);
        $this->assertStringContainsString('Bejelentkezés', $output);
    }

    public function testFormRetainsDataOnError()
    {
        // POST kérés szimulálása hibával
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'username' => 'newuser',
            'email' => 'invalid-email',
            'password' => 'Test1234',
            'phone' => '+36209876543'
        ];

        // Kimenet rögzítése
        ob_start();
        include 'regisztracio.php';
        $output = ob_get_clean();

        // Ellenőrzés: űrlap visszakapja az adatokat
        $this->assertStringContainsString('value="newuser"', $output);
        $this->assertStringContainsString('value="invalid-email"', $output);
        $this->assertStringContainsString('value="+36209876543"', $output);
        $this->assertStringNotContainsString('value="Test1234"', $output); // Jelszó nem marad meg
    }
}