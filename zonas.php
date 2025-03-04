<?php
// Mostrar errores para desarrollo (desactiva en producción)
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('conexion.php');

// Función para enviar respuesta JSON y salir
function sendResponse($response) {
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Verificar si la tabla Zonas existe
$result = $conn->query("SHOW TABLES LIKE 'Zonas'");
if ($result->num_rows == 0) {
    sendResponse(['success' => false, 'error' => 'La tabla Zonas no existe.']);
}

// Función para validar que la imagen sea JPG o PNG
function validarImagen($tmpName) {
    $fileType = mime_content_type($tmpName);
    $allowedTypes = ['image/jpeg', 'image/png'];
    if (!in_array($fileType, $allowedTypes)) {
        return false;
    }
    return true;
}

// Función para validar coordenadas (formato básico: "x,y")
function validarCoordenadas($coordenadas) {
    return preg_match('/^\d+,\d+$/', $coordenadas);
}

// Función para validar URL
function validarURL($url) {
    return filter_var($url, FILTER_VALIDATE_URL);
}

// Función para validar longitud máxima de un campo
function validarLongitud($campo, $maxLongitud) {
    return strlen($campo) <= $maxLongitud;
}

// Si la solicitud es GET, devolver la lista de zonas (incluyendo imagen)
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Se aliasa Descripción como Descripcion para facilitar el uso en JavaScript
    $sql = "SELECT ZonasID as Id_Zona, Nombre, Descripción as Descripcion, Coordenadas, Fotografia, Enlace, Tooltip FROM Zonas";
    $result = $conn->query($sql);
    if (!$result) {
        sendResponse(['success' => false, 'error' => 'Error en la consulta: ' . $conn->error]);
    }
    $zones = [];
    while ($row = $result->fetch_assoc()) {
        $zones[] = $row;
    }
    sendResponse(['success' => true, 'zones' => $zones]);
}
// Si la solicitud es POST, se espera un parámetro "action"
elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    if ($action == 'add') {
        // AGREGAR UNA NUEVA ZONA
        $nombre      = trim($_POST['nombre']);
        $coordenadas = trim($_POST['coordenadas']);
        $descripcion = trim($_POST['descripcion']);
        $tooltip     = trim($_POST['tooltip']);
        $enlace      = trim($_POST['enlace']);

        // Validaciones
        if (empty($nombre) || empty($coordenadas) || empty($descripcion)) {
            sendResponse(['success' => false, 'error' => 'Todos los campos obligatorios deben estar completos.']);
        }
        if (!validarCoordenadas($coordenadas)) {
            sendResponse(['success' => false, 'error' => 'Formato de coordenadas no válido. Debe ser "x,y".']);
        }
        if (!validarLongitud($nombre, 100)) {
            sendResponse(['success' => false, 'error' => 'El nombre no puede exceder los 100 caracteres.']);
        }
        if (!validarLongitud($descripcion, 500)) {
            sendResponse(['success' => false, 'error' => 'La descripción no puede exceder los 500 caracteres.']);
        }
        if (!empty($enlace) && !validarURL($enlace)) {
            sendResponse(['success' => false, 'error' => 'La URL proporcionada no es válida.']);
        }

        // Procesar imagen (se guarda solo la URL, no el contenido binario)
        if (isset($_FILES['fotografia']) && $_FILES['fotografia']['error'] == 0) {
            if (!validarImagen($_FILES['fotografia']['tmp_name'])) {
                sendResponse(['success' => false, 'error' => 'El archivo debe ser JPG o PNG.']);
            }
            // Directorio de subida
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            // Generar un nombre único para evitar colisiones
            $fileName = uniqid() . '_' . basename($_FILES['fotografia']['name']);
            $targetFile = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES['fotografia']['tmp_name'], $targetFile)) {
                // Guardamos la ruta relativa de la imagen
                $fotografia = $targetFile;
            } else {
                sendResponse(['success' => false, 'error' => 'Error al subir la imagen.']);
            }
        } else {
            $fotografia = null;
        }

        // Insertar usando el nombre exacto de la columna con tilde (Descripción)
        $sql = "INSERT INTO Zonas (Nombre, Descripción, Coordenadas, Fotografia, Enlace, Tooltip) 
                VALUES (?, ?, ?, ?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param('ssssss', $nombre, $descripcion, $coordenadas, $fotografia, $enlace, $tooltip);
            if ($stmt->execute()) {
                sendResponse(['success' => true, 'message' => 'Zona agregada con éxito!']);
            } else {
                sendResponse(['success' => false, 'error' => 'Error al insertar los datos: ' . $stmt->error]);
            }
            $stmt->close();
        } else {
            sendResponse(['success' => false, 'error' => 'Error al preparar la consulta: ' . $conn->error]);
        }
    }
    elseif ($action == 'update') {
        // ACTUALIZAR UNA ZONA EXISTENTE
        $id          = intval($_POST['id']);
        $nombre      = trim($_POST['nombre']);
        $coordenadas = trim($_POST['coordenadas']);
        $descripcion = trim($_POST['descripcion']);
        $tooltip     = trim($_POST['tooltip']);
        $enlace      = trim($_POST['enlace']);

        // Validaciones
        if (empty($id) || empty($nombre) || empty($coordenadas) || empty($descripcion)) {
            sendResponse(['success' => false, 'error' => 'Todos los campos obligatorios deben estar completos.']);
        }
        if (!validarCoordenadas($coordenadas)) {
            sendResponse(['success' => false, 'error' => 'Formato de coordenadas no válido. Debe ser "x,y".']);
        }
        if (!validarLongitud($nombre, 100)) {
            sendResponse(['success' => false, 'error' => 'El nombre no puede exceder los 100 caracteres.']);
        }
        if (!validarLongitud($descripcion, 500)) {
            sendResponse(['success' => false, 'error' => 'La descripción no puede exceder los 500 caracteres.']);
        }
        if (!empty($enlace) && !validarURL($enlace)) {
            sendResponse(['success' => false, 'error' => 'La URL proporcionada no es válida.']);
        }

        // Verificar si la zona existe antes de actualizar
        $checkSql = "SELECT ZonasID FROM Zonas WHERE ZonasID = ?";
        if ($checkStmt = $conn->prepare($checkSql)) {
            $checkStmt->bind_param('i', $id);
            $checkStmt->execute();
            $checkStmt->store_result();
            if ($checkStmt->num_rows == 0) {
                sendResponse(['success' => false, 'error' => 'La zona no existe.']);
            }
            $checkStmt->close();
        } else {
            sendResponse(['success' => false, 'error' => 'Error al verificar la zona: ' . $conn->error]);
        }

        // Si se envía una nueva imagen, actualizarla; de lo contrario, dejarla intacta.
        if (isset($_FILES['fotografia']) && $_FILES['fotografia']['error'] == 0) {
            if (!validarImagen($_FILES['fotografia']['tmp_name'])) {
                sendResponse(['success' => false, 'error' => 'El archivo debe ser JPG o PNG.']);
            }
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $fileName = uniqid() . '_' . basename($_FILES['fotografia']['name']);
            $targetFile = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES['fotografia']['tmp_name'], $targetFile)) {
                $fotografia = $targetFile;
            } else {
                sendResponse(['success' => false, 'error' => 'Error al subir la imagen.']);
            }
            $sql = "UPDATE Zonas SET Nombre = ?, Descripción = ?, Coordenadas = ?, Fotografia = ?, Enlace = ?, Tooltip = ? WHERE ZonasID = ?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param('ssssssi', $nombre, $descripcion, $coordenadas, $fotografia, $enlace, $tooltip, $id);
            } else {
                sendResponse(['success' => false, 'error' => 'Error al preparar la consulta: ' . $conn->error]);
            }
        } else {
            $sql = "UPDATE Zonas SET Nombre = ?, Descripción = ?, Coordenadas = ?, Enlace = ?, Tooltip = ? WHERE ZonasID = ?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param('sssssi', $nombre, $descripcion, $coordenadas, $enlace, $tooltip, $id);
            } else {
                sendResponse(['success' => false, 'error' => 'Error al preparar la consulta: ' . $conn->error]);
            }
        }

        if ($stmt->execute()) {
            sendResponse(['success' => true, 'message' => 'Zona actualizada con éxito!']);
        } else {
            sendResponse(['success' => false, 'error' => 'Error al actualizar los datos: ' . $stmt->error]);
        }
        $stmt->close();
    }
    elseif ($action == 'delete') {
        // ELIMINAR UNA ZONA
        $id = intval($_POST['id']);

        // Verificar si la zona existe antes de eliminar
        $checkSql = "SELECT ZonasID FROM Zonas WHERE ZonasID = ?";
        if ($checkStmt = $conn->prepare($checkSql)) {
            $checkStmt->bind_param('i', $id);
            $checkStmt->execute();
            $checkStmt->store_result();
            if ($checkStmt->num_rows == 0) {
                sendResponse(['success' => false, 'error' => 'La zona no existe.']);
            }
            $checkStmt->close();
        } else {
            sendResponse(['success' => false, 'error' => 'Error al verificar la zona: ' . $conn->error]);
        }

        $sql = "DELETE FROM Zonas WHERE ZonasID = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param('i', $id);
            if ($stmt->execute()) {
                sendResponse(['success' => true, 'message' => 'Zona eliminada con éxito!']);
            } else {
                sendResponse(['success' => false, 'error' => 'Error al eliminar la zona: ' . $stmt->error]);
            }
            $stmt->close();
        } else {
            sendResponse(['success' => false, 'error' => 'Error al preparar la consulta: ' . $conn->error]);
        }
    }
    else {
        sendResponse(['success' => false, 'error' => 'Acción no válida.']);
    }
}
$conn->close();
?>
