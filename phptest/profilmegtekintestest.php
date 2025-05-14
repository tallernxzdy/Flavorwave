<?php
namespace App;

use PHPUnit\Framework\TestCase;

class ProfilMegtekintesTest extends TestCase
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
        $this->conn->query("INSERT INTO felhasznalo (id, Teljes_nev, felhasznalo_nev, email_cim, tel_szam, lakcim, jog_szint) 
                            VALUES (1, 'Teszt Elek', 'tesztuser', 'teszt@example.com', '+36201234567', 'Budapest, Teszt utca 1', 0)");
    }

    public function testRenderProfileLoggedInUser()
    {
        // Bejelentkezett felhasználó beállítása
        $_SESSION['felhasznalo_id'] = 1;

        // Kimenet rögzítése
        ob_start();
        include 'profil_megtekintes.php';
        $output = ob_get_clean();

        // Ellenőrzés: HTML tartalom
        $this->assertStringContainsString('<h1><i class="fas fa-user-circle"></i> Profil adataim</h1>', $output);
        $this->assertStringContainsString('Teszt Elek', $output);
        $this->assertStringContainsString('tesztuser', $output);
        $this->assertStringContainsString('teszt@example.com', $output);
        $this->assertStringContainsString('+36201234567', $output);
        $this->assertStringContainsString('Budapest, Teszt utca 1', $output);
        $this->assertStringContainsString('Mentés', $output);
        $this->assertStringNotContainsString('A profil oldal megtekintéséhez be kell jelentkeznie!', $output);
    }

    public function testRenderProfileGuestUser()
    {
        // Vendég felhasználó beállítása
        unset($_SESSION['felhasznalo_id']);

        // Kimenet rögzítése
        ob_start();
        include 'profil_megtekintes.php';
        $output = ob_get_clean();

        // Ellenőrzés: HTML tartalom
        $this->assertStringContainsString('<h1><i class="fas fa-user-circle"></i> Profil adataim</h1>', $output);
        $this->assertStringContainsString('A profil oldal megtekintéséhez be kell jelentkeznie!', $output);
        $this->assertStringContainsString('Bejelentkezés', $output);
        $this->assertStringNotContainsString('Mentés', $output);
        $this->assertStringNotContainsString('Teszt Elek', $output);
    }

    public function testRenderProfileWithSuccessAlert()
    {
        // Bejelentkezett felhasználó és success paraméter beállítása
        $_SESSION['felhasznalo_id'] = 1;
        $_GET['success'] = '1';

        // Kimenet rögzítése
        ob_start();
        include 'profil_megtekintes.php';
        $output = ob_get_clean();

        // Ellenőrzés: siker üzenet
        $this->assertStringContainsString('alert-success', $output);
        $this->assertStringContainsString('Sikeresen módosítva!', $output);
        $this->assertStringNotContainsString('alert-danger', $output);
    }

    public function testRenderProfileWithErrorAlert()
    {
        // Bejelentkezett felhasználó és error paraméter beállítása
        $_SESSION['felhasznalo_id'] = 1;
        $_GET['error'] = '1';

        // Kimenet rögzítése
        ob_start();
        include 'profil_megtekintes.php';
        $output = ob_get_clean();

        // Ellenőrzés: hiba üzenet
        $this->assertStringContainsString('alert-danger', $output);
        $this->assertStringContainsString('Hiba történt a módosítás során!', $output);
        $this->assertStringNotContainsString('alert-success', $output);
    }

    public function testRenderProfileInvalidUserId()
    {
        // Érvénytelen felhasználó ID beállítása
        $_SESSION['felhasznalo_id'] = 999;

        // Kimenet rögzítése
        ob_start();
        include 'profil_megtekintes.php';
        $output = ob_get_clean();

        // Ellenőrzés: vendég felhasználóként kezelve
        $this->assertStringContainsString('<h1><i class="fas fa-user-circle"></i> Profil adataim</h1>', $output);
        $this->assertStringContainsString('A profil oldal megtekintéséhez be kell jelentkeznie!', $output);
        $this->assertStringContainsString('Bejelentkezés', $output);
        $this->assertStringNotContainsString('Mentés', $output);
    }

    public function testUserDataEscaping()
    {
        // Felhasználó veszélyes karakterekkel
        $this->conn->query("INSERT INTO felhasznalo (id, Teljes_nev, felhasznalo_nev, email_cim, tel_szam, lakcim, jog_szint) 
                            VALUES (2, 'Teszt <script>alert(\"xss\")</script>', 'test<script>user', 'test@example.com', '+36201234567', 'Budapest', 0)");
        $_SESSION['felhasznalo_id'] = 2;

        // Kimenet rögzítése
        ob_start();
        include 'profil_megtekintes.php';
        $output = ob_get_clean();

        // Ellenőrzés: HTML escape működik
        $this->assertStringContainsString('Teszt &lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;', $output);
        $this->assertStringContainsString('test&lt;script&gt;user', $output);
        $this->assertStringNotContainsString('<script>', $output);
    }
}