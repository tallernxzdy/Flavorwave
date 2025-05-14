<?php
namespace App;

use PHPUnit\Framework\TestCase;

class ProfilFrissitTest extends TestCase
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
        $this->conn->query("CREATE TABLE IF NOT EXISTS felhasznalo (
            id INT PRIMARY KEY AUTO_INCREMENT,
            Teljes_nev VARCHAR(255),
            felhasznalo_nev VARCHAR(255),
            email_cim VARCHAR(255),
            tel_szam VARCHAR(20),
            lakcim VARCHAR(255)
        )");
        $this->conn->query("INSERT INTO felhasznalo (id, Teljes_nev, felhasznalo_nev, email_cim, tel_szam, lakcim) 
                           VALUES (1, 'Teszt Elek', 'tesztelek', 'teszt@example.com', '+36301234567', 'Budapest, Teszt utca 1')");

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
        $this->conn->query("DROP TABLE IF EXISTS felhasznalo");
        $this->conn->close();
        session_unset();
        session_destroy();
    }

    public function testSuccessfulProfileUpdate()
    {
        $_SESSION['felhasznalo_id'] = 1;
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'teljes_nev' => 'Új Elek',
            'felhasznalo_nev' => 'ujelek',
            'email_cim' => 'uj@example.com',
            'tel_szam' => '+36309876543',
            'lakcim' => 'Debrecen, Új utca 2'
        ];

        ob_start();
        include 'profil_frissit.php';
        $output = ob_get_clean();

        $this->assertEmpty($output); // Nincs kimenet átirányítás miatt

        $user = $this->conn->query("SELECT * FROM felhasznalo WHERE id = 1")->fetch_assoc();
        $this->assertEquals('Új Elek', $user['Teljes_nev']);
        $this->assertEquals('ujelek', $user['felhasznalo_nev']);
        $this->assertEquals('uj@example.com', $user['email_cim']);
        $this->assertEquals('+36309876543', $user['tel_szam']);
        $this->assertEquals('Debrecen, Új utca 2', $user['lakcim']);

        $headers = headers_list();
        $locationHeader = array_filter($headers, fn($header) => strpos($header, 'Location: profil_megtekintes.php') !== false);
        $this->assertNotEmpty($locationHeader);
    }

    public function testFailedProfileUpdate()
    {
        $_SESSION['felhasznalo_id'] = 1;
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = []; // Üres POST adatok

        ob_start();
        include 'profil_frissit.php';
        $output = ob_get_clean();

        $this->assertEmpty($output); // Nincs kimenet átirányítás miatt

        $user = $this->conn->query("SELECT * FROM felhasznalo WHERE id = 1")->fetch_assoc();
        $this->assertEquals('Teszt Elek', $user['Teljes_nev']); // Nem változott

        $headers = headers_list();
        $locationHeader = array_filter($headers, fn($header) => strpos($header, 'Location: profil_megtekintes.php') !== false);
        $this->assertNotEmpty($locationHeader);
    }

    public function testRedirectForGuestUser()
    {
        unset($_SESSION['felhasznalo_id']);
        $_SERVER['REQUEST_METHOD'] = 'POST';

        ob_start();
        include 'profil_frissit.php';
        $output = ob_get_clean();

        $this->assertEmpty($output); // Nincs kimenet átirányítás miatt
        $headers = headers_list();
        $locationHeader = array_filter($headers, fn($header) => strpos($header, 'Location: bejelentkezes.php') !== false);
        $this->assertNotEmpty($locationHeader);
    }

    public function testNonPostRequest()
    {
        $_SESSION['felhasznalo_id'] = 1;
        $_SERVER['REQUEST_METHOD'] = 'GET';

        ob_start();
        include 'profil_frissit.php';
        $output = ob_get_clean();

        $this->assertEmpty($output); // Nincs kimenet, mert nem POST
        $user = $this->conn->query("SELECT * FROM felhasznalo WHERE id = 1")->fetch_assoc();
        $this->assertEquals('Teszt Elek', $user['Teljes_nev']); // Nem változott
    }
}