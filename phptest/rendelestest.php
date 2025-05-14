<?php
namespace App;

use PHPUnit\Framework\TestCase;
use PHPMailer\PHPMailer\PHPMailer;

class RendelesTest extends TestCase
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
    }

    protected function tearDown(): void
    {
        // Adatbázis tisztítása
        $this->conn->query('TRUNCATE TABLE felhasznalo');
        $this->conn->query('TRUNCATE TABLE tetelek');
        $this->conn->query('TRUNCATE TABLE etel');
        $this->conn->query('TRUNCATE TABLE megrendeles');
        $this->conn->query('TRUNCATE TABLE rendeles_tetel');
        $this->conn->close();
        session_unset();
        session_destroy();
    }

    private function initializeTestDatabase()
    {
        // Teszt adatok feltöltése
        $this->conn->query("INSERT INTO felhasznalo (id, felhasznalo_nev, email_cim, tel_szam, lakcim, Teljes_nev, jog_szint) 
                            VALUES (1, 'testuser', 'test@example.com', '+36201234567', 'Budapest, Teszt utca 1', 'Teszt Elek', 0)");
        $this->conn->query("INSERT INTO etel (id, nev, egyseg_ar) VALUES (1, 'Margherita', 2000)");
        $this->conn->query("INSERT INTO tetelek (felhasznalo_id, etel_id, darab) VALUES (1, 1, 2)");
    }

    public function testRenderOrderFormLoggedInUser()
    {
        // GET kérés szimulálása
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_POST = [];

        // Kimenet rögzítése
        ob_start();
        include 'rendeles.php';
        $output = ob_get_clean();

        // Ellenőrzés: HTML tartalom
        $this->assertStringContainsString('<h1 class="mb-4">Rendelés véglegesítése</h1>', $output);
        $this->assertStringContainsString('value="Teszt Elek"', $output);
        $this->assertStringContainsString('value="Budapest, Teszt utca 1"', $output);
        $this->assertStringContainsString('value="+36201234567"', $output);
        $this->assertStringContainsString('Margherita (2 db)', $output);
        $this->assertStringContainsString('4000 Ft', $output);
        $this->assertStringContainsString('Összesen: 4000 Ft', $output);
        $this->assertStringContainsString('Készpénz', $output);
        $this->assertStringContainsString('Bankkártya', $output);
    }

    public function testSuccessfulOrderProcessing()
    {
        // Mock PHPMailer
        $mailerMock = $this->createMock(PHPMailer::class);
        $mailerMock->method('send')->willReturn(true);

        // POST kérés szimulálása
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'submit' => true,
            'name' => 'Teszt Elek',
            'address' => 'Budapest, Teszt utca 2',
            'phone' => '+36209876543',
            'payment_method' => '1',
            'notes' => 'Kérem gyorsan'
        ];

        // Kimenet rögzítése és átirányítás elkerülése
        ob_start();
        include 'rendeles.php';
        $output = ob_get_clean();

        // Ellenőrzés: átirányítás sikeres rendeléshez
        $this->assertTrue(isset($_SESSION['order_success']));

        // Adatbázis ellenőrzése
        $result = $this->conn->query("SELECT * FROM megrendeles WHERE felhasznalo_id = 1");
        $this->assertEquals(1, $result->num_rows);
        $order = $result->fetch_assoc();
        $this->assertEquals('Kérem gyorsan', $order['leadas_megjegyzes']);
        $this->assertEquals('házhozszállítás', $order['kezbesites']);
        $this->assertEquals(1, $order['Fizetesi_mod']);

        $result = $this->conn->query("SELECT * FROM rendeles_tetel WHERE rendeles_id = {$order['id']}");
        $this->assertEquals(1, $result->num_rows);
        $item = $result->fetch_assoc();
        $this->assertEquals(1, $item['termek_id']);
        $this->assertEquals(2, $item['mennyiseg']);

        $result = $this->conn->query("SELECT * FROM tetelek WHERE felhasznalo_id = 1");
        $this->assertEquals(0, $result->num_rows);

        $result = $this->conn->query("SELECT tel_szam, lakcim, Teljes_nev FROM felhasznalo WHERE id = 1");
        $user = $result->fetch_assoc();
        $this->assertEquals('+36209876543', $user['tel_szam']);
        $this->assertEquals('Budapest, Teszt utca 2', $user['lakcim']);
        $this->assertEquals('Teszt Elek', $user['Teljes_nev']);
    }

    public function testOrderWithEmptyCart()
    {
        // Kosár ürítése
        $this->conn->query("DELETE FROM tetelek WHERE felhasznalo_id = 1");

        // POST kérés szimulálása
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'submit' => true,
            'name' => 'Teszt Elek',
            'address' => 'Budapest, Teszt utca 2',
            'phone' => '+36209876543',
            'payment_method' => '1'
        ];

        // Kimenet rögzítése
        ob_start();
        include 'rendeles.php';
        $output = ob_get_clean();

        // Ellenőrzés: hibaüzenet
        $this->assertStringContainsString('A kosár üres, nem lehet leadni a rendelést.', $output);
        $this->assertFalse(isset($_SESSION['order_success']));
    }

    public function testOrderWithInvalidPhone()
    {
        // POST kérés szimulálása érvénytelen telefonszámmal
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'submit' => true,
            'name' => 'Teszt Elek',
            'address' => 'Budapest, Teszt utca 2',
            'phone' => '12345',
            'payment_method' => '1'
        ];

        // Kimenet rögzítése
        ob_start();
        include 'rendeles.php';
        $output = ob_get_clean();

        // Ellenőrzés: hibaüzenet
        $this->assertStringContainsString('Érvénytelen telefonszám formátum', $output);
        $this->assertFalse(isset($_SESSION['order_success']));
    }

    public function testOrderWithMissingFields()
    {
        // POST kérés szimulálása hiányzó mezőkkel
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'submit' => true,
            'name' => '',
            'address' => '',
            'phone' => '',
            'payment_method' => ''
        ];

        // Kimenet rögzítése
        ob_start();
        include 'rendeles.php';
        $output = ob_get_clean();

        // Ellenőrzés: hibaüzenetek
        $this->assertStringContainsString('Név megadása kötelező', $output);
        $this->assertStringContainsString('Cím megadása kötelező', $output);
        $this->assertStringContainsString('Telefonszám megadása kötelező', $output);
        $this->assertStringContainsString('Fizetési mód kiválasztása kötelező', $output);
        $this->assertFalse(isset($_SESSION['order_success']));
    }

    public function testGuestUserRedirect()
    {
        // Vendég felhasználó beállítása
        unset($_SESSION['felhasznalo_id']);

        // Kimenet rögzítése
        ob_start();
        include 'rendeles.php';
        $output = ob_get_clean();

        // Ellenőrzés: átirányítás
        $this->assertStringContainsString('Location: bejelentkezes.php', $output);
    }

    public function testHtmlEscaping()
    {
        // Veszélyes karakterek beszúrása
        $this->conn->query("UPDATE felhasznalo SET Teljes_nev = 'Teszt <script>alert(\"xss\")</script>', lakcim = 'Budapest <script>', tel_szam = '+36201234567' WHERE id = 1");
        $this->conn->query("UPDATE etel SET nev = 'Pizza <script>alert(\"xss\")</script>' WHERE id = 1");

        // GET kérés szimulálása
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_POST = [];

        // Kimenet rögzítése
        ob_start();
        include 'rendeles.php';
        $output = ob_get_clean();

        // Ellenőrzés: HTML escape működik
        $this->assertStringContainsString('Teszt &lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;', $output);
        $this->assertStringContainsString('Budapest &lt;script&gt;', $output);
        $this->assertStringContainsString('Pizza &lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;', $output);
        $this->assertStringNotContainsString('<script>', $output);
    }
}