<?php

use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{
    private $conn;
    private $mockUser;
    
    protected function setUp(): void
    {
        // Mock database connection
        $this->conn = $this->createMock(PDO::class);
        
        // Sample mock user data
        $this->mockUser = [
            'id' => 1,
            'username' => 'testuser',
            'password' => password_hash('testpassword', PASSWORD_DEFAULT),
            'role' => 'user'
        ];
        
        // Start session
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    protected function tearDown(): void
    {
        // Clean up session after each test
        $_SESSION = array();
        session_destroy();
        
        // Reset POST data
        $_POST = array();
    }
    
    public function testSuccessfulUserLogin()
    {
        // Arrange
        $_POST['username'] = 'testuser';
        $_POST['password'] = 'testpassword';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        
        // Mock get_user function to return our test user
        $this->mockGetUser('testuser', $this->mockUser);
        
        // Act
        ob_start();
        include 'quiz_app.php';
        $output = ob_get_clean();
        
        // Assert
        $this->assertEquals(1, $_SESSION['user_id']);
        $this->assertEquals('testuser', $_SESSION['username']);
        $this->assertEquals('user', $_SESSION['role']);
    }
    
    public function testSuccessfulAdminLogin()
    {
        // Arrange
        $_POST['username'] = 'admin';
        $_POST['password'] = 'adminpass';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        
        $adminUser = array_merge($this->mockUser, ['role' => 'admin']);
        $this->mockGetUser('admin', $adminUser);
        
        // Act
        ob_start();
        include 'quiz_app.php';
        $output = ob_get_clean();
        
        // Assert
        $this->assertEquals('admin', $_SESSION['role']);
    }
    
    public function testFailedLoginInvalidPassword()
    {
        // Arrange
        $_POST['username'] = 'testuser';
        $_POST['password'] = 'wrongpassword';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        
        $this->mockGetUser('testuser', $this->mockUser);
        
        // Act
        ob_start();
        include 'quiz_app.php';
        $output = ob_get_clean();
        
        // Assert
        $this->assertArrayNotHasKey('user_id', $_SESSION);
        $this->assertStringContainsString('Invalid username or password', $output);
    }
    
    public function testFailedLoginNonexistentUser()
    {
        // Arrange
        $_POST['username'] = 'nonexistent';
        $_POST['password'] = 'testpassword';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        
        $this->mockGetUser('nonexistent', false);
        
        // Act
        ob_start();
        include 'quiz_app.php';
        $output = ob_get_clean();
        
        // Assert
        $this->assertArrayNotHasKey('user_id', $_SESSION);
        $this->assertStringContainsString('Invalid username or password', $output);
    }
    
    public function testLoginFormDisplay()
    {
        // Arrange
        $_SERVER['REQUEST_METHOD'] = 'GET';
        
        // Act
        ob_start();
        include 'quiz_app.php';
        $output = ob_get_clean();
        
        // Assert
        $this->assertStringContainsString('<form method="POST">', $output);
        $this->assertStringContainsString('<input type="text" id="username"', $output);
        $this->assertStringContainsString('<input type="password" id="password"', $output);
    }
    
    private function mockGetUser($username, $returnValue)
    {
        // Define the mock function in the global namespace
        global $conn;
        $conn = $this->conn;
        
        // Create a mock function that returns our test data
        function get_user($conn, $username) {
            return $GLOBALS['mockUserData'];
        }
        
        // Set the global mock data
        $GLOBALS['mockUserData'] = $returnValue;
    }
}