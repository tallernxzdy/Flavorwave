<?php
namespace App;

use PHPUnit\Framework\TestCase;

class RendelesMegtekintesTest extends TestCase
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

        // Mock adatokLekerdezese függvény
        if (!function_exists('App\adatokLekerdezese')) {
            function adatokLekerdezese($query, $params) {
                global $conn;
                $stmt = $conn->prepare($query);
                if ($params[0] && $params[1]) {
                    $stmt->bind_param($params[0], $params[1]);
                }
                $stmt->execute();
                $result = $stmt->get_result();
                $data = [];
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
                $stmt->close();
                return $data;
            }
        }
    }

    protected function tearDown(): void
    {
        // Adatbázis tisztítása
        $this->conn->query('TRUNCATE TABLE megrendeles');
        $this->conn->query('TRUNCATE TABLE rendeles_tetel');
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
        $this->conn->query("INSERT INTO etel (id, nev, egyseg_ar) VALUES (1, 'Margherita', 2000)");
        $this->conn->query("INSERT INTO megrendeles (id, felhasznalo_id, leadasdatuma, leadas_allapota, kezbesites, leadas_megjegyzes, Fizetesi_mod) 
                            VALUES (1, 1, '2025-05-01 10:00:00', 0, 'Házhozszállítás', 'Kérem gyorsan', 1)");
        $this->conn->query("INSERT INTO rendeles_tetel (rendeles_id, termek_id, mennyiseg) VALUES (1, 1, 2)");
    }

    public function testRenderOrdersLoggedInUserWithOrders()
    {
        // Bejelentkezett felhasználó beállítása
        $_SESSION['felhasznalo_id'] = 1;

        // Kimenet rögzítése
        ob_start();
        include 'rendeles_megtekintes.php';
        $output = ob_get_clean();

        // Ellenőrzés: HTML tartalom
        $this->assertStringContainsString('<h1 id="cim">Rendeléseim</h1>', $output);
        $this->assertStringContainsString('Rendelési azonosító: 1', $output);
        $this->assertStringContainsString('Leadás dátuma: 2025-05-01 10:00:00', $output);
        $this->assertStringContainsString('Állapot: Függőben', $output);
        $this->assertStringContainsString('Kézbesítés módja: Házhozszállítás', $output);
        $this->assertStringContainsString('Fizetési mód: Bankkártya', $output);
        $this->assertStringContainsString('Megjegyzés: Kérem gyorsan', $output);
        $this->assertStringContainsString('Margherita (2 db, 2 000 Ft)', $output);
        $this->assertStringNotContainsString('Nem vagy bejelentkezve!', $output);
        $this->assertStringNotContainsString('Úgy tűnik, még nem adtál fel rendelést', $output);
    }

    public function testRenderOrdersLoggedInUserNoOrders()
    {
        // Bejelentkezett felhasználó beállítása
        $_SESSION['felhasznalo_id'] = 1;

        // Üres rendelések (töröljük a meglévő rendelést)
        $this->conn->query("DELETE FROM megrendeles WHERE id = 1");

        // Kimenet rögzítése
        ob_start();
        include 'rendeles_megtekintes.php';
        $output = ob_get_clean();

        // Ellenőrzés: üres rendelések üzenet
        $this->assertStringContainsString('<h1 id="cim">Rendeléseim</h1>', $output);
        $this->assertStringContainsString('Úgy tűnik, még nem adtál fel rendelést', $output);
        $this->assertStringNotContainsString('Nem vagy bejelentkezve!', $output);
        $this->assertStringNotContainsString('Rendelési azonosító', $output);
    }

    public function testRenderOrdersGuestUser()
    {
        // Vendég felhasználó beállítása
        unset($_SESSION['felhasznalo_id']);

        // Kimenet rögzítése
        ob_start();
        include 'rendeles_megtekintes.php';
        $output = ob_get_clean();

        // Ellenőrzés: bejelentkezési üzenet
        $this->assertStringContainsString('<h1 id="cim">Rendeléseim</h1>', $output);
        $this->assertStringContainsString('Nem vagy bejelentkezve!', $output);
        $this->assertStringContainsString('Bejelentkezés', $output);
        $this->assertStringNotContainsString('Úgy tűnik, még nem adtál fel rendelést', $output);
        $this->assertStringNotContainsString('Rendelési azonosító', $output);
    }

    public function testOrderWithNoItems()
    {
        // Bejelentkezett felhasználó beállítása
        $_SESSION['felhasznalo_id'] = 1;

        // Tételek törlése a rendelésből
        $this->conn->query("DELETE FROM rendeles_tetel WHERE rendeles_id = 1");

        // Kimenet rögzítése
        ob_start();
        include 'rendeles_megtekintes.php';
        $output = ob_get_clean();

        // Ellenőrzés: üres tételek üzenet
        $this->assertStringContainsString('<h1 id="cim">Rendeléseim</h1>', $output);
        $this->assertStringContainsString('Rendelési azonosító: 1', $output);
        $this->assertStringContainsString('Még nem találhatóak tételek ehhez a rendeléshez.', $output);
        $this->assertStringNotContainsString('Margherita', $output);
    }

    public function testInvalidStatusMapping()
    {
        // Bejelentkezett felhasználó beállítása
        $_SESSION['felhasznalo_id'] = 1;

        // Érvénytelen státusz beállítása
        $this->conn->query("UPDATE megrendeles SET leadas_allapota = 999 WHERE id = 1");

        // Kimenet rögzítése
        ob_start();
        include 'rendeles_megtekintes.php';
        $output = ob_get_clean();

        // Ellenőrzés: ismeretlen státusz
        $this->assertStringContainsString('Állapot: Ismeretlen', $output);
        $this->assertStringNotContainsString('Állapot: Függőben', $output);
    }

    public function testInvalidPaymentMethodMapping()
    {
        // Bejelentkezett felhasználó beállítása
        $_SESSION['felhasznalo_id'] = 1;

        // Érvénytelen fizetési mód beállítása
        $this->conn->query("UPDATE megrendeles SET Fizetesi_mod = 999 WHERE id = 1");

        // Kimenet rögzítése
        ob_start();
        include 'rendeles_megtekintes.php';
        $output = ob_get_clean();

        // Ellenőrzés: ismeretlen fizetési mód
        $this->assertStringContainsString('Fizetési mód: Ismeretlen', $output);
        $this->assertStringNotContainsString('Fizetési mód: Bankkártya', $output);
    }

    public function testHtmlEscaping()
    {
        // Bejelentkezett felhasználó beállítása
        $_SESSION['felhasznalo_id'] = 1;

        // Veszélyes karakterek beszúrása
        $this->conn->query("UPDATE megrendeles SET leadas_megjegyzes = '<script>alert(\"xss\")</script>' WHERE id = 1");
        $this->conn->query("UPDATE etel SET nev = 'Pizza <script>alert(\"xss\")</script>' WHERE id = 1");

        // Kimenet rögzítése
        ob_start();
        include 'rendeles_megtekintes.php';
        $output = ob_get_clean();

        // Ellenőrzés: HTML escape működik
        $this->assertStringContainsString('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;', $output);
        $this->assertStringNotContainsString('<script>', $output);
    }
}