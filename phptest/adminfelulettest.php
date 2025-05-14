<?php
namespace App;

use PHPUnit\Framework\TestCase;

class AdminFeluletTest extends TestCase
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

        // Session inicializálása admin jogosultsággal
        $_SESSION['felhasznalo_id'] = 1;
        $_SESSION['jog_szint'] = 1;
    }

    protected function tearDown(): void
    {
        // Adatbázis tisztítása
        $this->conn->query('TRUNCATE TABLE etel');
        $this->conn->query('TRUNCATE TABLE kategoria');
        $this->conn->query('TRUNCATE TABLE felhasznalo');
        $this->conn->close();
        session_unset();
    }

    private function initializeTestDatabase()
    {
        // Teszt adatok feltöltése
        $this->conn->query("INSERT INTO kategoria (id, kategoria_nev) VALUES (1, 'Pizza')");
        $this->conn->query("INSERT INTO felhasznalo (id, felhasznalo_nev, jog_szint) VALUES (1, 'admin', 1)");
        $this->conn->query("INSERT INTO felhasznalo (id, felhasznalo_nev, jog_szint) VALUES (2, 'dolgozo', 2)");
    }

    public function testGetEtelApiValidId()
    {
        // GET kérés szimulálása
        $_GET['action'] = 'get_etel';
        $_GET['id'] = 1;
        $_SERVER['REQUEST_METHOD'] = 'GET';

        // Étel hozzáadása teszteléshez
        $this->conn->query("INSERT INTO etel (id, nev, egyseg_ar, kategoria_id) VALUES (1, 'Margherita', 2000, 1)");

        // Kimenet rögzítése
        ob_start();
        include 'admin_felulet.php';
        $output = ob_get_clean();

        // JSON válasz ellenőrzése
        $response = json_decode($output, true);
        $this->assertArrayHasKey('nev', $response);
        $this->assertEquals('Margherita', $response['nev']);
        $this->assertEquals(200, http_response_code());
    }

    public function testGetEtelApiInvalidId()
    {
        // GET kérés szimulálása érvénytelen ID-vel
        $_GET['action'] = 'get_etel';
        $_GET['id'] = 'invalid';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        // Kimenet rögzítése
        ob_start();
        include 'admin_felulet.php';
        $output = ob_get_clean();

        // JSON válasz ellenőrzése
        $response = json_decode($output, true);
        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('Érvénytelen ID!', $response['error']);
        $this->assertEquals(400, http_response_code());
    }

    public function testAddEtelSuccess()
    {
        // POST kérés szimulálása
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['operation'] = 'add';
        $_POST['nev'] = 'Margherita';
        $_POST['egyseg_ar'] = 2000;
        $_POST['leiras'] = 'Klasszikus pizza';
        $_POST['kategoria_id'] = 1;
        $_POST['kaloria'] = 800;
        $_POST['osszetevok'] = 'Paradicsom, mozzarella';
        $_POST['allergenek'] = 'Glutén, laktóz';
        $_FILES['kepek_url'] = [
            'name' => '',
            'type' => '',
            'tmp_name' => '',
            'error' => UPLOAD_ERR_NO_FILE,
            'size' => 0
        ];

        // Kimenet rögzítése
        ob_start();
        include 'admin_felulet.php';
        $output = ob_get_clean();

        // Ellenőrzés: sikeres hozzáadás
        $this->assertStringContainsString('Étel sikeresen hozzáadva!', $output);

        // Adatbázis ellenőrzése
        $result = $this->conn->query("SELECT * FROM etel WHERE nev = 'Margherita'");
        $this->assertEquals(1, $result->num_rows);
        $etel = $result->fetch_assoc();
        $this->assertEquals(2000, $etel['egyseg_ar']);
        $this->assertEquals(1, $etel['kategoria_id']);
    }

    public function testAddEtelInvalidPrice()
    {
        // POST kérés szimulálása negatív árral
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['operation'] = 'add';
        $_POST['nev'] = 'Margherita';
        $_POST['egyseg_ar'] = -100;
        $_POST['leiras'] = 'Klasszikus pizza';
        $_POST['kategoria_id'] = 1;
        $_POST['kaloria'] = 800;
        $_POST['osszetevok'] = 'Paradicsom, mozzarella';
        $_POST['allergenek'] = 'Glutén, laktóz';
        $_FILES['kepek_url'] = [
            'name' => '',
            'type' => '',
            'tmp_name' => '',
            'error' => UPLOAD_ERR_NO_FILE,
            'size' => 0
        ];

        // Kimenet rögzítése
        ob_start();
        include 'admin_felulet.php';
        $output = ob_get_clean();

        // Ellenőrzés: hibaüzenet
        $this->assertStringContainsString('Az egységár nem lehet negatív vagy érvénytelen szám!', $output);

        // Adatbázis ellenőrzése: nem történt beszúrás
        $result = $this->conn->query("SELECT * FROM etel WHERE nev = 'Margherita'");
        $this->assertEquals(0, $result->num_rows);
    }

    public function testEditEtelSuccess()
    {
        // Étel hozzáadása teszteléshez
        $this->conn->query("INSERT INTO etel (id, nev, egyseg_ar, kategoria_id) VALUES (1, 'Margherita', 2000, 1)");

        // POST kérés szimulálása szerkesztéshez
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['operation'] = 'edit';
        $_POST['edit_etel'] = 1;
        $_POST['edit_nev'] = 'Margherita Updated';
        $_POST['edit_egyseg_ar'] = 2500;
        $_POST['edit_leiras'] = 'Frissített pizza';
        $_POST['edit_kategoria_id'] = 1;
        $_POST['edit_kaloria'] = 900;
        $_POST['edit_osszetevok'] = 'Paradicsom, mozzarella, bazsalikom';
        $_POST['edit_allergenek'] = 'Glutén, laktóz';
        $_FILES['edit_kepek_url'] = [
            'name' => '',
            'type' => '',
            'tmp_name' => '',
            'error' => UPLOAD_ERR_NO_FILE,
            'size' => 0
        ];

        // Kimenet rögzítése
        ob_start();
        include 'admin_felulet.php';
        $output = ob_get_clean();

        // Ellenőrzés: sikeres szerkesztés
        $this->assertStringContainsString('Sikeres szerkesztés!', $output);

        // Adatbázis ellenőrzése
        $result = $this->conn->query("SELECT * FROM etel WHERE id = 1");
        $etel = $result->fetch_assoc();
        $this->assertEquals('Margherita Updated', $etel['nev']);
        $this->assertEquals(2500, $etel['egyseg_ar']);
    }

    public function testDeleteEtelSuccess()
    {
        // Étel hozzáadása teszteléshez
        $this->conn->query("INSERT INTO etel (id, nev, egyseg_ar, kategoria_id) VALUES (1, 'Margherita', 2000, 1)");

        // POST kérés szimulálása törléshez
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['operation'] = 'delete';
        $_POST['delete_etel'] = 1;

        // Kimenet rögzítése
        ob_start();
        include 'admin_felulet.php';
        $output = ob_get_clean();

        // Ellenőrzés: sikeres törlés
        $this->assertStringContainsString('Étel sikeresen törölve!', $output);

        // Adatbázis ellenőrzése
        $result = $this->conn->query("SELECT * FROM etel WHERE id = 1");
        $this->assertEquals(0, $result->num_rows);
    }

    public function testEditUserSuccess()
    {
        // POST kérés szimulálása felhasználó szerkesztéséhez
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['operation'] = 'edit_user';
        $_POST['editUserId'] = 2;
        $_POST['jog_szint'] = 0;

        // Kimenet rögzítése
        ob_start();
        include 'admin_felulet.php';
        $output = ob_get_clean();

        // Ellenőrzés: sikeres jogosultság módosítás
        $this->assertStringContainsString('A Választott profil jogosultsága sikeresen frissítve!', $output);

        // Adatbázis ellenőrzése
        $result = $this->conn->query("SELECT jog_szint FROM felhasznalo WHERE id = 2");
        $felhasznalo = $result->fetch_assoc();
        $this->assertEquals(0, $felhasznalo['jog_szint']);
    }

    public function testEditUserSameJogSzint()
    {
        // POST kérés szimulálása azonos jogosultsággal
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['operation'] = 'edit_user';
        $_POST['editUserId'] = 2;
        $_POST['jog_szint'] = 2;

        // Kimenet rögzítése
        ob_start();
        include 'admin_felulet.php';
        $output = ob_get_clean();

        // Ellenőrzés: figyelmeztetés azonos jogosultságra
        $this->assertStringContainsString('A felhasználó már rendelkezik ezzel a jogosultsági szinttel!', $output);

        // Adatbázis ellenőrzése: változatlan jogosultság
        $result = $this->conn->query("SELECT jog_szint FROM felhasznalo WHERE id = 2");
        $felhasznalo = $result->fetch_assoc();
        $this->assertEquals(2, $felhasznalo['jog_szint']);
    }
}