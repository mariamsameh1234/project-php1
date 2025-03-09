<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php';
include 'database.php';
include 'business_logic.php';

// إنشاء كائن Database مع تمرير القيم الصحيحة
$database = new Database("localhost", "root", "123456", "Cafeteria");
$pdo = $database->getConnection();
$productModel = new Product($pdo);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product = trim($_POST["product"]);
    $price = $_POST["price"];
    $category = $_POST["category"];
    $image = isset($_FILES["img"]) && $_FILES["img"]["error"] == 0 ? $_FILES["img"]["name"] : null;

    $errors = [];

    // التحقق من المدخلات
    if (empty($product)) {
        $errors[] = "Product name is required.";
    }

    if (!is_numeric($price) || $price <= 0) {
        $errors[] = "Enter a valid price.";
    }

    if (empty($category)) {
        $errors[] = "Select a category.";
    }

    // الحصول على c_id بدلاً من اسم الفئة
    $stmt = $pdo->prepare("SELECT c_id FROM Category WHERE name = ?");
    $stmt->execute([$category]);
    $category_id = $stmt->fetchColumn();

    if (!$category_id) {
        $errors[] = "Invalid category selected.";
    }

    // رفع الصورة والتحقق منها
    if ($image && is_uploaded_file($_FILES["img"]["tmp_name"])) {
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
        
        if (!in_array($ext, $allowed_ext)) {
            $errors[] = "Invalid image format. Only JPG, PNG, and GIF are allowed.";
        } else {
            // التأكد من أن مجلد uploads موجود
            if (!is_dir('uploads')) {
                mkdir('uploads', 0777, true);
            }

            $upload_dir = "uploads/";
            $image_path = $upload_dir . basename($image);

            if (!move_uploaded_file($_FILES["img"]["tmp_name"], $image_path)) {
                $errors[] = "Failed to upload image.";
            }
        }
    } else {
        $image_path = null;
    }

    // إدخال البيانات في قاعدة البيانات
    if (empty($errors)) {
        $inserted = $productModel->insert($product, $price, $category_id, $image_path);

        if ($inserted) {
            $_SESSION['success'] = "Product added successfully!";
            header("Location: add_product.php");
            exit();
        } else {
            $_SESSION['errors'] = ["Failed to add product. Please try again."];
            header("Location: add_product.php");
            exit();
        }
    } else {
        $_SESSION['errors'] = $errors;
        header("Location: AddProduct.php");

        exit();
    }
}
?>
