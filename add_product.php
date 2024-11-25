<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Thư viện thông báo đẹp -->
</head>
<body>
    <h1>Thêm Sản Phẩm</h1>
    <form action="" method="POST" enctype="multipart/form-data">
        <table>
            <tr>
                <td>Mã sản phẩm:</td>
                <td><input type="text" name="product_id" required></td>
            </tr>
            <tr>
                <td>Tên sản phẩm:</td>
                <td><input type="text" name="product_name" required></td>
            </tr>
            <tr>
                <td>Giá sản phẩm:</td>
                <td><input type="number" name="product_price" required></td>
            </tr>
            <tr>
                <td>Số lượng sản phẩm:</td>
                <td><input type="number" name="quantity" required></td>
            </tr>
            <tr>
                <td>Ảnh sản phẩm:</td>
                <td><input type="file" name="product_img" accept="image/*" required></td>
            </tr>
            <tr>
                <td>Mô tả sản phẩm:</td>
                <td><textarea name="product_description" rows="4" required></textarea></td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type="submit" name="add_product" value="Thêm mới">
                </td>
            </tr>
        </table>
    </form>

    <?php
    // Connect to the database
    $connect = mysqli_connect('localhost', 'root', '', 'se06303_web');
    if (!$connect) {
        die("Kết nối cơ sở dữ liệu thất bại: " . mysqli_connect_error());
    }

    // Insert data when form is submitted
    if (isset($_POST['add_product'])) {
        // Retrieve data from form
        $product_id = mysqli_real_escape_string($connect, $_POST['product_id']);
        $product_name = mysqli_real_escape_string($connect, $_POST['product_name']);
        $product_price = floatval($_POST['product_price']);
        $quantity = intval($_POST['quantity']);
        $product_description = mysqli_real_escape_string($connect, $_POST['product_description']);
        $product_img = $_FILES['product_img']['name'];
        $product_img_tmp = $_FILES['product_img']['tmp_name'];

        // Directory to store uploaded images
        $upload_dir = "uploads/products/";

        // Check if directory exists, create if not
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Generate unique filename to prevent overwriting
        $unique_filename = uniqid() . "_" . basename($product_img);

        // Full path to save the image
        $target_file = $upload_dir . $unique_filename;

        // Check file type and size (e.g., max 5MB)
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (!in_array($file_type, $allowed_types)) {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi!',
                    text: 'Chỉ hỗ trợ các định dạng ảnh JPG, JPEG, PNG, GIF.'
                });
            </script>";
        } elseif ($_FILES['product_img']['size'] > 5 * 1024 * 1024) {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi!',
                    text: 'Kích thước ảnh tối đa là 5MB.'
                });
            </script>";
        } else {
            // Attempt to move uploaded file
            if (move_uploaded_file($product_img_tmp, $target_file)) {
                // SQL query to insert product
                $sql = "INSERT INTO products (product_id, product_name, product_price, quantity, product_img, product_description) 
                        VALUES ('$product_id', '$product_name', '$product_price', '$quantity', '$target_file', '$product_description')";

                // Execute query
                if (mysqli_query($connect, $sql)) {
                    echo "<script>
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành Công!',
                            text: 'Sản phẩm đã được thêm thành công.'
                        });
                    </script>";
                } else {
                    echo "<script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: 'Lỗi khi thêm sản phẩm: " . mysqli_error($connect) . "'
                        });
                    </script>";
                }
            } else {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi!',
                        text: 'Lỗi khi tải lên ảnh.'
                    });
                </script>";
            }
        }
    }

    mysqli_close($connect);
    ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>
</html>

