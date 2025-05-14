<?php
namespace App;

use PHPUnit\Framework\TestCase;

class AdatbazisraCsatlakozasTest extends TestCase
{
    private $conn;
    private $testDbName = 'flavorwave';
    private $baseDir = '../kepek/';

    protected function setUp(): void
    {
        // Adatbázis kapcsolat inicializálása
        $this->conn = new \mysqli('localhost', 'root', '', $this->testDbName);
        if ($this->conn->connect_error) {
            $this->fail('Adatbázis kapcsolat sikertelen: ' . $this->conn->connect_error);
        }
        $this->conn->set_charset("utf8mb4");

        // Teszt adatbázis inicializálása
        $this->initializeTestDatabase();

        // Globális $conn beállítása
        global $conn;
        $conn = $this->conn;

        // Mock fájlrendszer függvények
        if (!function_exists('App\move_uploaded_file')) {
            function move_uploaded_file($from, $to) {
                return rename($from, $to); // Egyszerűsített mock
            }
        }
        if (!function_exists('App\rename')) {
            function rename($old, $new) {
                return true; // Sikeres átnevezés szimulálása
            }
        }
        if (!function_exists('App\copy')) {
            function copy($source, $dest) {
                return true; // Sikeres másolás szimulálása
            }
        }
        if (!function_exists('App\unlink')) {
            function unlink($file) {
                return true; // Sikeres törlés szimulálása
            }
        }
        if (!function_exists('App\file_exists')) {
            function file_exists($file) {
                return false; // Alapértelmezés: fájl nem létezik
            }
        }
        if (!function_exists('App\mkdir')) {
            function mkdir($path, $mode = 0755, $recursive = false) {
                return true; // Sikeres mappa létrehozás szimulálása
            }
        }
    }

    protected function tearDown(): void
    {
        // Adatbázis tisztítása
        $this->conn->query('TRUNCATE TABLE felhasznalo');
        $this->conn->query('TRUNCATE TABLE megrendeles');
        $this->conn->query('TRUNCATE TABLE etel');
        $this->conn->close();
    }

    private function initializeTestDatabase()
    {
        // Teszt adatok feltöltése
        $this->conn->query("CREATE TABLE IF NOT EXISTS felhasznalo (
            id INT PRIMARY KEY AUTO_INCREMENT,
            felhasznalo_nev VARCHAR(255) NOT NULL,
            jog_szint INT DEFAULT 0
        )");
        $this->conn->query("CREATE TABLE IF NOT EXISTS megrendeles (
            id INT PRIMARY KEY AUTO_INCREMENT,
            felhasznalo_id INT,
            leadas_allapota INT DEFAULT 0,
            FOREIGN KEY (felhasznalo_id) REFERENCES felhasznalo(id)
        )");
        $this->conn->query("CREATE TABLE IF NOT EXISTS etel (
            id INT PRIMARY KEY AUTO_INCREMENT,
            nev VARCHAR(255) NOT NULL,
            egyseg_ar INT NOT NULL,
            kategoria_id INT NOT NULL
        )");
        $this->conn->query("INSERT INTO felhasznalo (id, felhasznalo_nev, jog_szint) VALUES (1, 'testuser', 0)");
        $this->conn->query("INSERT INTO megrendeles (id, felhasznalo_id, leadas_allapota) VALUES (1, 1, 0)");
        $this->conn->query("INSERT INTO etel (id, nev, egyseg_ar, kategoria_id) VALUES (1, 'Margherita', 2000, 1)");
    }

    public function testDatabaseConnection()
    {
        global $conn;
        include 'adatbazisra_csatlakozas.php';
        $this->assertInstanceOf(\mysqli::class, $conn);
        $this->assertEquals(0, $conn->connect_errno);
        $this->assertEquals('utf8mb4', $conn->character_set_name());
    }

    public function testSafeFileName()
    {
        include 'adatbazisra_csatlakozas.php';
        $this->assertEquals('teszt-kep', safeFileName('Teszt Kép!'));
        $this->assertEquals('magyar-kep', safeFileName('Magyar Kép@#'));
        $this->assertEquals('teszt', safeFileName('Teszt---'));
        $this->assertEquals('unicode-kep', safeFileName('Ünícódé Kép'));
    }

    public function testHandleImageUploadSuccess()
    {
        include 'adatbazisra_csatlakozas.php';
        $categoryId = '1';
        $uploadedFile = [
            'name' => 'test.jpg',
            'tmp_name' => '/tmp/test.jpg'
        ];
        $kepNev = 'Teszt Kép';

        $result = handleImageUpload($categoryId, $uploadedFile, $kepNev);
        $this->assertEquals('teszt-kep.jpg', $result);
    }

    public function testHandleImageUploadFileExists()
    {
        include 'adatbazisra_csatlakozas.php';

        // Mock file_exists to return true
        if (!function_exists('App\file_exists')) {
            function file_exists($file) {
                return true; // Fájl létezik szimulálása
            }
        }

        $categoryId = '1';
        $uploadedFile = [
            'name' => 'test.jpg',
            'tmp_name' => '/tmp/test.jpg'
        ];
        $kepNev = 'Teszt Kép';

        $result = handleImageUpload($categoryId, $uploadedFile, $kepNev);
        $this->assertFalse($result);
    }

    public function testRenameImageFileSuccess()
    {
        include 'adatbazisra_csatlakozas.php';
        $oldPath = '1/old-image.jpg';
        $newName = 'Új Kép';

        $result = renameImageFile($oldPath, $newName);
        $this->assertEquals('uj-kep.jpg', $result);
    }

    public function testRenameImageFileTargetExists()
    {
        include 'adatbazisra_csatlakozas.php';

        // Mock file_exists to return true for new path
        if (!function_exists('App\file_exists')) {
            function file_exists($file) {
                return strpos($file, 'uj-kep.jpg') !== false;
            }
        }

        $oldPath = '1/old-image.jpg';
        $newName = 'Új Kép';

        $result = renameImageFile($oldPath, $newName);
        $this->assertFalse($result);
    }

    public function testMoveImageToCategorySuccess()
    {
        include 'adatbazisra_csatlakozas.php';
        $oldCategoryId = '1';
        $newCategoryId = '2';
        $fileName = 'test.jpg';

        // Mock file_exists to return true for old path
        if (!function_exists('App\file_exists')) {
            function file_exists($file) {
                return strpos($file, '1/test.jpg') !== false;
            }
        }

        $result = moveImageToCategory($oldCategoryId, $newCategoryId, $fileName);
        $this->assertEquals('test.jpg', $result);
    }

    public function testMoveImageToCategoryFileNotExists()
    {
        include 'adatbazisra_csatlakozas.php';
        $oldCategoryId = '1';
        $newCategoryId = '2';
        $fileName = 'test.jpg';

        $result = moveImageToCategory($oldCategoryId, $newCategoryId, $fileName);
        $this->assertFalse($result);
    }

    public function testDeleteImageFiles()
    {
        include 'adatbazisra_csatlakozas.php';
        $imagePath = 'test.jpg';
        $categoryId = '1';

        // Mock file_exists to return true
        if (!function_exists('App\file_exists')) {
            function file_exists($file) {
                return true;
            }
        }

        // Nincs visszatérési érték, csak a függvény meghívása
        deleteImageFiles($imagePath, $categoryId);
        $this->assertTrue(true); // Sikeres végrehajtás
    }

    public function testAdatokLekerdezeseSuccess()
    {
        include 'adatbazisra_csatlakozas.php';
        $query = "SELECT * FROM etel WHERE id = ?";
        $params = ["i", 1];

        $result = adatokLekerdezese($query, $params);
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('Margherita', $result[0]['nev']);
    }

    public function testAdatokLekerdezeseNoResults()
    {
        include 'adatbazisra_csatlakozas.php';
        $query = "SELECT * FROM etel WHERE id = ?";
        $params = ["i", 999];

        $result = adatokLekerdezese($query, $params);
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testAdatokValtoztatasaSuccess()
    {
        include 'adatbazisra_csatlakozas.php';
        $query = "UPDATE etel SET nev = ? WHERE id = ?";
        $params = ["si", "Új Margherita", 1];

        $result = adatokValtoztatasa($query, $params);
        $this->assertEquals('Sikeres művelet!', $result);

        $check = $this->conn->query("SELECT nev FROM etel WHERE id = 1")->fetch_assoc();
        $this->assertEquals('Új Margherita', $check['nev']);
    }

    public function testAdatokValtoztatasaNoChanges()
    {
        include 'adatbazisra_csatlakozas.php';
        $query = "UPDATE etel SET nev = ? WHERE id = ?";
        $params = ["si", "Margherita", 999];

        $result = adatokValtoztatasa($query, $params);
        $this->assertEquals('Nem történt változtatás.', $result);
    }

    public function testAdatokTorleseSuccess()
    {
        include 'adatbazisra_csatlakozas.php';
        $id = 1;

        $result = adatokTorlese($id);
        $this->assertEquals('Sikeres törlés!', $result);

        $check = $this->conn->query("SELECT * FROM etel WHERE id = 1");
        $this->assertEquals(0, $check->num_rows);
    }

    public function testAdatokTorleseNoChanges()
    {
        include 'adatbazisra_csatlakozas.php';
        $id = 999;

        $result = adatokTorlese($id);
        $this->assertEquals('Nem történt törlés.', $result);
    }

    public function testRendeleseLekerdezeseSuccess()
    {
        include 'adatbazisra_csatlakozas.php';
        $allapot = 0;

        $result = rendeleseLekerdezese($allapot);
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('testuser', $result[0]['felhasznalo_nev']);
    }

    public function testRendeleseLekerdezeseNoResults()
    {
        include 'adatbazisra_csatlakozas.php';
        $allapot = 999;

        $result = rendeleseLekerdezese($allapot);
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }
}