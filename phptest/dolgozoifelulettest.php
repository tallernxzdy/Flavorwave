<?php
namespace App;

use PHPUnit\Framework\TestCase;

class DolgozoiFeluletTest extends TestCase
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
        session_start();
        $_SESSION['felhasznalo_id'] = 1;
        $_SESSION['jog_szint'] = 1;
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
        $this->conn->query("INSERT INTO felhasznalo (id, felhasznalo_nev, jog_szint) VALUES (1, 'admin', 1)");
        $this->conn->query("INSERT INTO felhasznalo (id, felhasznalo_nev, jog_szint) VALUES (2, 'user', 0)");
        $this->conn->query("INSERT INTO etel (id, nev, egyseg_ar) VALUES (1, 'Margherita', 2000)");
        $this->conn->query("INSERT INTO megrendeles (id, felhasznalo_id, leadas_allapota) VALUES (1, 2, 0)");
        $this->conn->query("INSERT INTO megrendeles (id, felhasznalo_id, leadas_allapota) VALUES (2, 2, 1)");
        $this->conn->query("INSERT INTO megrendeles (id, felhasznalo_id, leadas_allapota) VALUES (3, 2, 2)");
        $this->conn->query("INSERT INTO rendeles_tetel (rendeles_id, termek_id, mennyiseg) VALUES (1, 1, 2)");
        $this->conn->query("INSERT INTO rendeles_tetel (rendeles_id, termek_id, mennyiseg) VALUES (2, 1, 3)");
        $this->conn->query("INSERT INTO rendeles_tetel (rendeles_id, termek_id, mennyiseg) VALUES (3, 1, 1)");
    }

    // Mock adatokLekerdezese függvény
    private function mockAdatokLekerdezese($sql, $params = [])
    {
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $types = $params[0];
            $values = array_slice($params, 1);
            $stmt->bind_param($types, ...$values);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    // Mock rendeleseLekerdezese függvény
    private function mockRendeleseLekerdezese($status)
    {
        $sql = "SELECT megrendeles.id, felhasznalo.felhasznalo_nev 
                FROM megrendeles 
                INNER JOIN felhasznalo ON megrendeles.felhasznalo_id = felhasznalo.id 
                WHERE leadas_allapota = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $status);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    public function testAccessDeniedForNonAuthorizedUser()
    {
        // Nem megfelelő jogosultság
        $_SESSION['jog_szint'] = 0;

        // Kimenet rögzítése
        ob_start();
        include 'dolgozoi_felulet.php';
        $output = ob_get_clean();

        // Ellenőrzés: átirányítás bejelentkezes.php-re
        $this->assertTrue(headers_sent());
        $this->assertEquals('Location: bejelentkezes.php', headers_list()[0]);
    }

    public function testGetOrderDetailsAjax()
    {
        // Mock függvények
        global $mockedAdatokLekerdezese;
        $mockedAdatokLekerdezese = function ($sql, $params = []) {
            return $this->mockAdatokLekerdezese($sql, $params);
        };

        // AJAX kérés szimulálása
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['ajax'] = 'get_order_details';
        $_POST['order_id'] = 1;

        // Kimenet rögzítése
        ob_start();
        include 'dolgozoi_felulet.php';
        $output = ob_get_clean();

        // JSON válasz ellenőrzése
        $response = json_decode($output, true);
        $this->assertArrayHasKey('html', $response);
        $this->assertStringContainsString('Margherita', $response['html']);
        $this->assertStringContainsString('user', $response['html']);
        $this->assertStringContainsString('4000 Ft', $response['html']);
        $this->assertEquals(1, $_SESSION['selected_order_id']);
    }

    public function testGetOrderDetailsAjaxInvalidOrder()
    {
        // AJAX kérés szimulálása érvénytelen rendelés ID-vel
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['ajax'] = 'get_order_details';
        $_POST['order_id'] = 999;

        // Kimenet rögzítése
        ob_start();
        include 'dolgozoi_felulet.php';
        $output = ob_get_clean();

        // JSON válasz ellenőrzése
        $response = json_decode($output, true);
        $this->assertArrayHasKey('html', $response);
        $this->assertStringContainsString('Nincs tétel ehhez a rendeléshez.', $response['html']);
        $this->assertArrayNotHasKey('selected_order_id', $_SESSION);
    }

    public function testRefreshDropdownsAjax()
    {
        // Mock függvény
        global $mockedRendeleseLekerdezese;
        $mockedRendeleseLekerdezese = function ($status) {
            return $this->mockRendeleseLekerdezese($status);
        };

        // AJAX kérés szimulálása
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['ajax'] = 'refresh_dropdowns';

        // Kimenet rögzítése
        ob_start();
        include 'dolgozoi_felulet.php';
        $output = ob_get_clean();

        // JSON válasz ellenőrzése
        $response = json_decode($output, true);
        $this->assertArrayHasKey('pending', $response);
        $this->assertArrayHasKey('in_progress', $response);
        $this->assertArrayHasKey('completed', $response);
        $this->assertCount(1, $response['pending']);
        $this->assertCount(1, $response['in_progress']);
        $this->assertCount(1, $response['completed']);
        $this->assertEquals(1, $response['pending'][0]['id']);
        $this->assertEquals('user', $response['pending'][0]['felhasznalo_nev']);
    }

    public function testUpdateOrderStatusToInProgress()
    {
        // Mock függvények
        global $mockedAdatokValtoztatasa, $mockedRendeleseLekerdezese;
        $mockedAdatokValtoztatasa = function ($sql, $params) {
            return 'Sikeres művelet!';
        };
        $mockedRendeleseLekerdezese = function ($status) {
            return $this->mockRendeleseLekerdezese($status);
        };

        // POST kérés szimulálása
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['update_status'] = true;
        $_POST['order_id'] = 1;
        $_POST['new_status'] = 1;

        // Kimenet rögzítése
        ob_start();
        include 'dolgozoi_felulet.php';
        $output = ob_get_clean();

        // Ellenőrzés: sikeres frissítés
        $this->assertStringContainsString('A rendelés állapota sikeresen frissítve!', $output);
        $this->assertEquals(1, $_SESSION['selected_order_id']);
    }

    public function testUpdateOrderStatusInvalidOrder()
    {
        // Mock függvény
        global $mockedRendeleseLekerdezese;
        $mockedRendeleseLekerdezese = function ($status) {
            return $this->mockRendeleseLekerdezese($status);
        };

        // POST kérés szimulálása üres rendelés ID-vel
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['update_status'] = true;
        $_POST['order_id'] = '';
        $_POST['new_status'] = 1;

        // Kimenet rögzítése
        ob_start();
        include 'dolgozoi_felulet.php';
        $output = ob_get_clean();

        // Ellenőrzés: hibaüzenet
        $this->assertStringContainsString('Nincs kiválasztva rendelés!', $output);
        $this->assertArrayNotHasKey('selected_order_id', $_SESSION);
    }

    public function testFinishOrder()
    {
        // Mock függvények
        global $mockedAdatokValtoztatasa, $mockedRendeleseLekerdezese;
        $mockedAdatokValtoztatasa = function ($sql, $params) {
            return 'Sikeres művelet!';
        };
        $mockedRendeleseLekerdezese = function ($status) {
            return $this->mockRendeleseLekerdezese($status);
        };

        // POST kérés szimulálása
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['finished_order'] = true;
        $_POST['order_id'] = 3;

        // Kimenet rögzítése
        ob_start();
        include 'dolgozoi_felulet.php';
        $output = ob_get_clean();

        // Ellenőrzés: sikeres elküldés
        $this->assertStringContainsString('A rendelés sikeresen elküldva!', $output);
        $this->assertArrayNotHasKey('selected_order_id', $_SESSION);
    }

    public function testRenderWithSelectedOrder()
    {
        // Mock függvények
        global $mockedAdatokLekerdezese, $mockedRendeleseLekerdezese;
        $mockedAdatokLekerdezese = function ($sql, $params = []) {
            return $this->mockAdatokLekerdezese($sql, $params);
        };
        $mockedRendeleseLekerdezese = function ($status) {
            return $this->mockRendeleseLekerdezese($status);
        };

        // Kiválasztott rendelés beállítása
        $_SESSION['selected_order_id'] = 1;

        // Kimenet rögzítése
        ob_start();
        include 'dolgozoi_felulet.php';
        $output = ob_get_clean();

        // Ellenőrzés: rendelés részletek megjelenítése
        $this->assertStringContainsString('Kiválasztott rendelés részletei', $output);
        $this->assertStringContainsString('Margherita', $output);
        $this->assertStringContainsString('4000 Ft', $output);
        $this->assertStringNotContainsString('Nincs tétel ehhez a rendeléshez.', $output);
    }
}