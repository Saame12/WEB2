<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_POST['login'])) {
    // Create a connection
    $conn = mysqli_connect('localhost', 'root', '', 'web_db');
    
    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    
    // Prepare the SQL query with prepared statement
    $query = "SELECT IDKH, NAME, STATUS FROM kh WHERE NAME = ? AND MK = ?";
    $stmt = $conn->prepare($query);
    
    // Get user input
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Bind parameters
    $stmt->bind_param("ss", $username, $password);
    
    // Execute the query
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Check if user exists
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Check if account is locked
        if ($row['STATUS'] === 'Locked') {
            echo "<script type='text/javascript'>alert('Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.'); window.location.href='dangnhap.html';</script>";
            $stmt->close();
            $conn->close();
            exit();
        }
        
        // Account is not locked, proceed with login
        setcookie("user_id", $row['IDKH'], time() + (86400 * 30), "/"); // Store user ID in cookie
        $_SESSION['IDKH'] = $row['IDKH'];
        $_SESSION['username'] = $row['NAME'];
        
        echo "<script type='text/javascript'>alert('Đăng nhập thành công.'); window.location.href='../index.php';</script>";
    } else {
        echo "<script type='text/javascript'>alert('Tài khoản hoặc mật khẩu chưa chính xác.'); window.location.href='dangnhap.html';</script>";
    }
    
    // Clean up
    $stmt->close();
    $conn->close();
    exit();
}
?>
