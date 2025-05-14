<?php
namespace App;

use PHPUnit\Framework\TestCase;

class VisszajelzesekTest extends TestCase
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
        $_SESSION = ['felhasznalo_id' => 1];

        // Globális $conn beállítása
        global $conn;
        $conn = $this->conn;

        // Mock file_get_contents a reCAPTCHA ellenőrzéshez
        if (!function_exists('App\file_get_contents')) {
            function file_get_contents($url, $use_include_path = false, $context = null) {
                if (strpos($url, 'https://www.google.com/recaptcha/api/siteverify') !== false) {
                    return json_encode(['success' => true]);
                }
                return \file_get_contents($url, $use_include_path, $context);
            }
        }
    }

    protected function tearDown(): void
    {
        // Adatbázis tisztítása
        $this->conn->query('TRUNCATE TABLE felhasznalo');
        $this->conn->query('TRUNCATE TABLE velemenyek');
        $this->conn->close();
        session_unset();
        session_destroy();
    }

    private function initializeTestDatabase()
    {
        // Teszt adatok feltöltése
        $this->conn->query("INSERT INTO felhasznalo (id, felhasznalo_nev, email_cim, jog_szint) 
                            VALUES (1, 'testuser', 'test@example.com', 0)");
    }

    public function testRenderFeedbackFormAndNoFeedbacks()
    {
        // GET kérés szimulálása
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_POST = [];

        // Kimenet rögzítése
        ob_start();
        include 'visszajelzesek.php';
        $output = ob_get_clean();

        // Ellenőrzés: HTML tartalom
        $this->assertStringContainsString('<h1>Küldd el a véleményed!</h1>', $output);
        $this->assertStringContainsString('name="megelegedettseg"', $output);
        $this->assertStringContainsString('name="visszajelzes"', $output);
        $this->assertStringContainsString('g-recaptcha', $output);
        $this->assertStringContainsString('<h2>Mit mondanak rólunk?</h2>', $output);
        $this->assertStringContainsString('Légy az elsők között, aki visszajelzést küld!', $output);
    }

    public function testSuccessfulFeedbackSubmission()
    {
        // POST kérés szimulálása
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'megelegedettseg' => '4',
            'visszajelzes' => 'Nagyszerű szolgáltatás!',
            'g-recaptcha-response' => 'valid-token'
        ];

        // Kimenet rögzítése és átirányítás elkerülése
        ob_start();
        include 'visszajelzesek.php';
        $output = ob_get_clean();

        // Ellenőrzés: üzenet és adatbázis
        $this->assertTrue(isset($_SESSION['uzenet']));
        $this->assertEquals('Köszönjük a visszajelzést!', $_SESSION['uzenet']);

        $result = $this->conn->query("SELECT * FROM velemenyek WHERE felhasznalo_id = 1");
        $this->assertEquals(1, $result->num_rows);
        $feedback = $result->fetch_assoc();
        $this->assertEquals('Nagyszerű szolgáltatás!', $feedback['velemeny_szoveg']);
        $this->assertEquals(4, $feedback['ertekeles']);
        $this->assertEquals('test@example.com', $feedback['email_cim']);
    }

    public function testFeedbackUpdate()
    {
        // Először beszúrunk egy visszajelzést
        $this->conn->query("INSERT INTO velemenyek (felhasznalo_id, velemeny_szoveg, ertekeles, email_cim) 
                            VALUES (1, 'Első vélemény', 3, 'test@example.com')");

        // POST kérés szimulálása frissítéssel
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'megelegedettseg' => '5',
            'visszajelzes' => 'Még jobb szolgáltatás!',
            'g-recaptcha-response' => 'valid-token'
        ];

        // Kimenet rögzítése
        ob_start();
        include 'visszajelzesek.php';
        $output = ob_get_clean();

        // Ellenőrzés: üzenet és adatbázis
        $this->assertTrue(isset($_SESSION['uzenet']));
        $this->assertEquals('Visszajelzés frissítve!', $_SESSION['uzenet']);

        $result = $this->conn->query("SELECT * FROM velemenyek WHERE felhasznalo_id = 1");
        $this->assertEquals(1, $result->num_rows);
        $feedback = $result->fetch_assoc();
        $this->assertEquals('Még jobb szolgáltatás!', $feedback['velemeny_szoveg']);
        $this->assertEquals(5, $feedback['ertekeles']);
    }

    public function testFeedbackWithMissingFields()
    {
        // POST kérés szimulálása hiányzó mezőkkel
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'megelegedettseg' => '',
            'visszajelzes' => '',
            'g-recaptcha-response' => 'valid-token'
        ];

        // Kimenet rögzítése
        ob_start();
        include 'visszajelzesek.php';
        $output = ob_get_clean();

        // Ellenőrzés: hibaüzenet
        $this->assertTrue(isset($_SESSION['uzenet']));
        $this->assertEquals('Kérjük, minden mezőt töltsön ki!', $_SESSION['uzenet']);
        $result = $this->conn->query("SELECT * FROM velemenyek WHERE felhasznalo_id = 1");
        $this->assertEquals(0, $result->num_rows);
    }

    public function testFeedbackWithInvalidRecaptcha()
    {
        // Mock file_get_contents a sikertelen reCAPTCHA-hoz
        if (!function_exists('App\file_get_contents')) {
            function file_get_contents($url, $use_include_path = false, $context = null) {
                if (strpos($url, 'https://www.google.com/recaptcha/api/siteverify') !== false) {
                    return json_encode(['success' => false]);
                }
                return \file_get_contents($url, $use_include_path, $context);
            }
        }

        // POST kérés szimulálása
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'megelegedettseg' => '4',
            'visszajelzes' => 'Nagyszerű szolgáltatás!',
            'g-recaptcha-response' => 'invalid-token'
        ];

        // Kimenet rögzítése
        ob_start();
        include 'visszajelzesek.php';
        $output = ob_get_clean();

        // Ellenőrzés: hibaüzenet
        $this->assertTrue(isset($_SESSION['uzenet']));
        $this->assertEquals('reCAPTCHA ellenőrzés sikertelen. Próbálja újra!', $_SESSION['uzenet']);
        $result = $this->conn->query("SELECT * FROM velemenyek WHERE felhasznalo_id = 1");
        $this->assertEquals(0, $result->num_rows);
    }

    public function testGuestUserAccess()
    {
        // Vendég felhasználó beállítása
        unset($_SESSION['felhasznalo_id']);

        // Kimenet rögzítése
        ob_start();
        include 'visszajelzesek.php';
        $output = ob_get_clean();

        // Ellenőrzés: hozzáférés megtagadva
        $this->assertStringContainsString('Csak bejelentkezett felhasználók írhatnak véleményt.', $output);
    }

    public function testRenderExistingFeedbacks()
    {
        // Teszt visszajelzések beszúrása
        $this->conn->query("INSERT INTO velemenyek (felhasznalo_id, velemeny_szoveg, ertekeles, email_cim) 
                            VALUES (1, 'Nagyszerű szolgáltatás!', 4, 'test@example.com')");

        // GET kérés szimulálása
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_POST = [];

        // Kimenet rögzítése
        ob_start();
        include 'visszajelzesek.php';
        $output = ob_get_clean();

        // Ellenőrzés: visszajelzések megjelenítése
        $this->assertStringContainsString('Nagyszerű szolgáltatás!', $output);
        $this->assertStringContainsString('testuser', $output);
        $this->assertStringContainsString('⭐⭐⭐⭐', $output);
        $this->assertStringNotContainsString('Légy az elsők között, aki visszajelzést küld!', $output);
    }

    public function testHtmlEscaping()
    {
        // Veszélyes karakterek beszúrása
        $this->conn->query("INSERT INTO velemenyek (felhasznalo_id, velemeny_szoveg, ertekeles, email_cim) 
                            VALUES (1, 'Vélemény <script>alert(\"xss\")</script>', 4, 'test@example.com')");
        $this->conn->query("UPDATE felhasznalo SET felhasznalo_nev = 'user<script>alert(\"xss\")</script>' WHERE id = 1");

        // GET kérés szimulálása
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_POST = [];

        // Kimenet rögzítése
        ob_start();
        include 'visszajelzesek.php';
        $output = ob_get_clean();

        // Ellenőrzés: HTML escape működik
        $this->assertStringContainsString('Vélemény <script>alert("xss")</script>', $output);
        $this->assertStringContainsString('user<script>alert("xss")</script>', $output);
        $this->assertStringNotContainsString('<script>', $output);
    }
}