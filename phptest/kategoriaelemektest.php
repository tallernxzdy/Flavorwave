<?php
namespace App;

use PHPUnit\Framework\TestCase;

class PizzaTest extends TestCase
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
        $this->conn->query('TRUNCATE TABLE etel');
        $this->conn->query('TRUNCATE TABLE kategoria');
        $this->conn->close();
        session_unset();
        session_destroy();
    }

    private function initializeTestDatabase()
    {
        // Teszt adatok feltöltése
        $this->conn->query("INSERT INTO kategoria (id, kategoria_nev) VALUES (4, 'Pizza')");
        $this->conn->query("INSERT INTO etel (id, nev, egyseg_ar, leiras, kategoria_id, kep_url, kaloria, osszetevok, allergenek) 
                            VALUES (1, 'Margherita', 2000, 'Klasszikus pizza', 4, 'margherita.jpg', 800, 'Paradicsom, mozzarella', 'Glutén, laktóz')");
        $this->conn->query("INSERT INTO etel (id, nev, egyseg_ar, leiras, kategoria_id, kep_url, kaloria, osszetevok, allergenek) 
                            VALUES (2, 'Pepperoni', 2500, 'Pikáns pizza', 4, 'pepperoni.jpg', 900, 'Paradicsom, mozzarella, pepperoni', 'Glutén, laktóz')");
    }

    // Mock adatokLekerdezese függvény
    private function mockAdatokLekerdezese($sql, $params = [])
    {
        if (strpos($sql, 'SELECT * FROM etel WHERE kategoria_id = 4') !== false) {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function testPizzaPageRendersWithItems()
    {
        // Mock adatokLekerdezese függvény
        global $mockedAdatokLekerdezese;
        $mockedAdatokLekerdezese = function ($sql, $params = []) {
            return $this->mockAdatokLekerdezese($sql, $params);
        };

        // Kimenet rögzítése
        ob_start();
        include 'pizza.php';
        $output = ob_get_clean();

        // Ellenőrzés: HTML tartalom
        $this->assertStringContainsString('<h1 class="page-title" id="cim">Pizzák</h1>', $output);
        $this->assertStringContainsString('Margherita', $output);
        $this->assertStringContainsString('Pepperoni', $output);
        $this->assertStringContainsString('Ár: 2000 Ft', $output);
        $this->assertStringContainsString('Ár: 2500 Ft', $output);
        $this->assertStringContainsString('<img src="../kepek/4/margherita.jpg"', $output);
        $this->assertStringContainsString('<img src="../kepek/4/pepperoni.jpg"', $output);
        $this->assertStringContainsString('modal-1', $output);
        $this->assertStringContainsString('modal-2', $output);
        $this->assertStringNotContainsString('Ehhez a kategóriához nem tartozik étel!', $output);
    }

    public function testPizzaPageRendersEmptyCategory()
    {
        // Adatbázis tisztítása
        $this->conn->query('TRUNCATE TABLE etel');

        // Mock adatokLekerdezese függvény üres eredménnyel
        global $mockedAdatokLekerdezese;
        $mockedAdatokLekerdezese = function ($sql, $params = []) {
            return [];
        };

        // Kimenet rögzítése
        ob_start();
        include 'pizza.php';
        $output = ob_get_clean();

        // Ellenőrzés: üres kategória üzenet
        $this->assertStringContainsString('<h1 class="page-title" id="cim">Pizzák</h1>', $output);
        $this->assertStringContainsString('Ehhez a kategóriához nem tartozik étel!', $output);
        $this->assertStringNotContainsString('Margherita', $output);
        $this->assertStringNotContainsString('flex-grid', $output);
    }

    public function testPizzaPageModalContent()
    {
        // Mock adatokLekerdezese függvény
        global $mockedAdatokLekerdezese;
        $mockedAdatokLekerdezese = function ($sql, $params = []) {
            return $this->mockAdatokLekerdezese($sql, $params);
        };

        // Kimenet rögzítése
        ob_start();
        include 'pizza.php';
        $output = ob_get_clean();

        // Ellenőrzés: modal tartalom Margherita-hoz
        $this->assertStringContainsString('modal-1', $output);
        $this->assertStringContainsString('Margherita - Részletek', $output);
        $this->assertStringContainsString('Leírás: Klasszikus pizza', $output);
        $this->assertStringContainsString('Kalória: 800 kcal', $output);
        $this->assertStringContainsString('Összetevők: Paradicsom, mozzarella', $output);
        $this->assertStringContainsString('Allergének: Glutén, laktóz', $output);
        $this->assertStringContainsString('Ár: 2000 Ft', $output);
    }

    public function testPizzaPageInvalidCategoryData()
    {
        // Mock adatokLekerdezese függvény hibával
        global $mockedAdatokLekerdezese;
        $mockedAdatokLekerdezese = function ($sql, $params = []) {
            return false;
        };

        // Kimenet rögzítése
        ob_start();
        include 'pizza.php';
        $output = ob_get_clean();

        // Ellenőrzés: hibaüzenet adatbázis hiba esetén
        $this->assertStringContainsString('<h1 class="page-title" id="cim">Pizzák</h1>', $output);
        $this->assertStringContainsString('Ehhez a kategóriához nem tartozik étel!', $output);
        $this->assertStringNotContainsString('Margherita', $output);
    }
}