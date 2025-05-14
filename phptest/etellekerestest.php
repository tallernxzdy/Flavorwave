<?php
namespace App;

use PHPUnit\Framework\TestCase;

class GetEtelTest extends TestCase
{
    private $pdo;
    private $testDbName = 'flavorwave';

    protected function setUp(): void
    {
        $this->pdo = new \PDO("mysql:host=localhost;dbname=$this->testDbName", 'root', '');
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $this->pdo->exec("CREATE TABLE IF NOT EXISTS etel (
            id INT PRIMARY KEY AUTO_INCREMENT,
            nev VARCHAR(255) NOT NULL,
            egyseg_ar INT NOT NULL
        )");
        $this->pdo->exec("INSERT INTO etel (id, nev, egyseg_ar) VALUES (1, 'Margherita', 2000)");
    }

    protected function tearDown(): void
    {
        $this->pdo->exec("DROP TABLE etel");
        $this->pdo = null;
    }

    public function testGetEtelSuccess()
    {
        $_GET['id'] = 1;
        ob_start();
        include 'get_etel.php';
        $output = ob_get_clean();
        $result = json_decode($output, true);

        $this->assertEquals('Margherita', $result['nev']);
        $this->assertEquals(2000, $result['egyseg_ar']);
    }

    public function testGetEtelNotFound()
    {
        $_GET['id'] = 999;
        ob_start();
        include 'get_etel.php';
        $output = ob_get_clean();
        $result = json_decode($output, true);

        $this->assertFalse($result);
    }

    public function testGetEtelInvalidDatabase()
    {
        // Mock PDOException
        rename('get_etel.php', 'get_etel_temp.php');
        file_put_contents('get_etel.php', '<?php
        header("Content-Type: application/json");
        throw new PDOException("Adatbázis hiba");
        ?>');

        ob_start();
        include 'get_etel.php';
        $output = ob_get_clean();
        $result = json_decode($output, true);

        $this->assertArrayHasKey('error', $result);
        $this->assertEquals('Adatbázis hiba', $result['error']);

        rename('get_etel_temp.php', 'get_etel.php');
    }
}