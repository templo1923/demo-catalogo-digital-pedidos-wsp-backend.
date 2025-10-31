<?php
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Cargar variables de entorno desde el archivo .env
require __DIR__.'/vendor/autoload.php';
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Obtener los valores de las variables de entorno
$servidor = $_ENV['DB_HOST'] . ':' . $_ENV['DB_PORT'];
$usuario = $_ENV['DB_USER'];
$contrasena = $_ENV['DB_PASS'];
$dbname = $_ENV['DB_NAME'];
$rutaweb = $_ENV['RUTA_WEB'];
$mensaje = "";

try {
    $dsn = "mysql:host=$servidor;dbname=$dbname";
    $conexion = new PDO($dsn, $usuario, $contrasena);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $descripcion = $_POST['descripcion'];
        $titulo = $_POST['titulo'];
        $precio = $_POST['precio'];
        $idCategoria = $_POST['idCategoria'];
        $idSubCategoria = $_POST['idSubCategoria'];
        $masVendido = $_POST['masVendido'];
        $item1 = $_POST['item1'];
        $item2 = $_POST['item2'];
        $item3 = $_POST['item3'];
        $item4 = $_POST['item4'];
        $item5 = $_POST['item5'];
        $item6 = $_POST['item6'];
        $item7 = $_POST['item7'];
        $item8 = $_POST['item8'];
        $item9 = $_POST['item9'];
        $item10 = $_POST['item10'];
        $precioAnterior = $_POST['precioAnterior'];
        $stock = $_POST['stock'];
        $verItems = $_POST['verItems'];

        // Validar que el título no contenga caracteres prohibidos
        if (strpos($titulo, '/') !== false || strpos($titulo, '\\') !== false) {
            echo json_encode(["error" => "El título no debe contener caracteres como / o \\"]);
            exit;
        }

        if (!empty($titulo) && !empty($precio) && !empty($idCategoria) && !empty($masVendido)) {

            // Verificar si se enviaron imágenes
            $imagenesPresentes = isset($_FILES['imagen1']) || isset($_FILES['imagen2']) || isset($_FILES['imagen3']) || isset($_FILES['imagen4']);

            if ($imagenesPresentes) {

                // Crear carpeta para imágenes si no existe
                $carpetaImagenes = './imagenes_productos';
                if (!file_exists($carpetaImagenes)) {
                    mkdir($carpetaImagenes, 0777, true);
                }

                // Inicializar rutas de imágenes
                $rutaImagenCompleta = '';
                $rutaImagen2Completa = '';
                $rutaImagen3Completa = '';
                $rutaImagen4Completa = '';

                // Subir imágenes si están presentes
                if (isset($_FILES['imagen1']) && $_FILES['imagen1']['error'] === UPLOAD_ERR_OK) {
                    $nombreImagen = $_FILES['imagen1']['name'];
                    $rutaImagen = $carpetaImagenes . '/' . $nombreImagen;
                    move_uploaded_file($_FILES['imagen1']['tmp_name'], $rutaImagen);
                    $rutaImagenCompleta = $rutaweb . $rutaImagen;
                }

                if (isset($_FILES['imagen2']) && $_FILES['imagen2']['error'] === UPLOAD_ERR_OK) {
                    $nombreImagen2 = $_FILES['imagen2']['name'];
                    $rutaImagen2 = $carpetaImagenes . '/' . $nombreImagen2;
                    move_uploaded_file($_FILES['imagen2']['tmp_name'], $rutaImagen2);
                    $rutaImagen2Completa = $rutaweb . $rutaImagen2;
                }

                if (isset($_FILES['imagen3']) && $_FILES['imagen3']['error'] === UPLOAD_ERR_OK) {
                    $nombreImagen3 = $_FILES['imagen3']['name'];
                    $rutaImagen3 = $carpetaImagenes . '/' . $nombreImagen3;
                    move_uploaded_file($_FILES['imagen3']['tmp_name'], $rutaImagen3);
                    $rutaImagen3Completa = $rutaweb . $rutaImagen3;
                }

                if (isset($_FILES['imagen4']) && $_FILES['imagen4']['error'] === UPLOAD_ERR_OK) {
                    $nombreImagen4 = $_FILES['imagen4']['name'];
                    $rutaImagen4 = $carpetaImagenes . '/' . $nombreImagen4;
                    move_uploaded_file($_FILES['imagen4']['tmp_name'], $rutaImagen4);
                    $rutaImagen4Completa = $rutaweb . $rutaImagen4;
                }

                // Verificar que al menos una imagen esté presente
                if ($rutaImagenCompleta === '' && $rutaImagen2Completa === '' && $rutaImagen3Completa === '' && $rutaImagen4Completa === '') {
                    echo json_encode(["error" => "Debe seleccionar al menos una imagen"]);
                    exit;
                }

                // Almacenar enlaces completos en la base de datos
                $sqlInsert = "INSERT INTO `productos` (descripcion, titulo, precio, idCategoria, idSubCategoria, masVendido, imagen1, imagen2 , imagen3, imagen4,
                 item1, item2, item3, item4, item5, item6, item7, item8, item9, item10,precioAnterior,stock, verItems) 
                 VALUES (:descripcion, :titulo, :precio, :idCategoria, :idSubCategoria, :masVendido, :imagen1, :imagen2, :imagen3 , :imagen4,
                 :item1, :item2, :item3, :item4, :item5, :item6, :item7, :item8, :item9, :item10, :precioAnterior, :stock, :verItems)";
                $stmt = $conexion->prepare($sqlInsert);
                $stmt->bindParam(':descripcion', $descripcion);
                $stmt->bindParam(':titulo', $titulo);
                $stmt->bindParam(':precio', $precio);
                $stmt->bindParam(':idCategoria', $idCategoria);
                $stmt->bindParam(':idSubCategoria', $idSubCategoria);
                $stmt->bindParam(':masVendido', $masVendido);
                $stmt->bindParam(':imagen1', $rutaImagenCompleta);
                $stmt->bindParam(':imagen2', $rutaImagen2Completa);
                $stmt->bindParam(':imagen3', $rutaImagen3Completa);
                $stmt->bindParam(':imagen4', $rutaImagen4Completa);
                $stmt->bindParam(':item1', $item1);
                $stmt->bindParam(':item2', $item2);
                $stmt->bindParam(':item3', $item3);
                $stmt->bindParam(':item4', $item4);
                $stmt->bindParam(':item5', $item5);
                $stmt->bindParam(':item6', $item6);
                $stmt->bindParam(':item7', $item7);
                $stmt->bindParam(':item8', $item8);
                $stmt->bindParam(':item9', $item9);
                $stmt->bindParam(':item10', $item10);
                $stmt->bindParam(':precioAnterior', $precioAnterior);
                $stmt->bindParam(':stock', $stock);
                $stmt->bindParam(':verItems', $verItems);
                $stmt->execute();

                // Obtener el ID de la última inserción
                $lastId = $conexion->lastInsertId();

                // Obtener la fecha de creación actualizada
                $sqlSelect = "SELECT createdAt FROM `productos` WHERE idProducto = :lastId";
                $stmtSelect = $conexion->prepare($sqlSelect);
                $stmtSelect->bindParam(':lastId', $lastId);
                $stmtSelect->execute();
                $createdAt = $stmtSelect->fetchColumn();

                // Respuesta JSON con enlaces de las imágenes y fecha de creación
                echo json_encode([
                    "mensaje" => "producto creado exitosamente",
                    "imagen1" => $rutaImagenCompleta,
                    "imagen2" => $rutaImagen2Completa,
                    "imagen3" => $rutaImagen3Completa,
                    "imagen4" => $rutaImagen4Completa,
                    "createdAt" => $createdAt
                ]);
            } else {
                echo json_encode(["error" => "Debe seleccionar al menos una imagen"]);
            }
        } else {
            echo json_encode(["error" => "Por favor, complete todos los campos correctamente"]);
        }
    } else {
        echo json_encode(["error" => "Método no permitido"]);
    }
} catch (PDOException $error) {
    echo json_encode(["error" => "Error de conexión: " . $error->getMessage()]);
}
?>
