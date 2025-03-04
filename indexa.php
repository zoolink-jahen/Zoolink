<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$database = "zoolink";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Recuperar los datos del zoológico
$sql = "SELECT * FROM zoologico LIMIT 1";
$result = $conn->query($sql);
$zoo = $result->fetch_assoc();
$imagenes = glob("imagenes/*.jpeg");

// Procesar formulario de actualización
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $horarios = $_POST['horarios'];
    $ubicacion = $_POST['ubicacion'];
    $contacto = $_POST['contacto'];
    $reglas = $_POST['reglas'];
    $logo = $_FILES['logo']['tmp_name'] ? file_get_contents($_FILES['logo']['tmp_name']) : $zoo['logo'];

    // Actualizar en la base de datos
    $sql_update = "UPDATE zoologico SET 
        nombre = ?, descripcion = ?, horarios = ?, ubicacion = ?, contacto = ?, reglas = ?, logo = ?
        WHERE id = 1";
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param("sssssss", $nombre, $descripcion, $horarios, $ubicacion, $contacto, $reglas, $logo);
    if ($stmt->execute()) {
        echo "Perfil actualizado exitosamente.";
        // Recargar los datos después de actualizar
        $result = $conn->query($sql);
        $zoo = $result->fetch_assoc();
    } else {
        echo "Error al actualizar el perfil: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - <?php echo $zoo['nombre'] ?? 'Zoológico'; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@700&family=Pacifico&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { background-color: #f4f4f4; font-family: 'Roboto', sans-serif; }
        nav {
      background-color: #065f46;
      padding: 1rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      color: white;
      width: 100%;
      position: fixed;
      top: 0;
      z-index: 100;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    nav h1 {
      display: flex;
      align-items: center;
      margin: 0;
    }
    nav ul {
      list-style: none;
      display: flex;
      gap: 20px;
      margin: 0;
      padding: 0;
    }
    nav a {
      color: white;
      text-decoration: none;
      padding: 0.5rem 1rem;
      border-radius: 4px;
      transition: background-color 0.3s ease;
      font-weight: bold;
    }
    nav a:hover {
      background-color: #047857;
    }
    h2 {
      text-align: center;
      margin-top: 80px;
      color: #065f46;
      animation: fadeIn 0.5s ease;
    }
        .navbar-brand img { height: 50px; margin-right: 10px; }
        .container { max-width: 1200px; }
        h1 { color: #00796b; font-size: 3rem; text-align: center; }
        .descripcion { font-size: 1.3rem; color: #555; text-align: center; }
        .carousel img { height: 400px; object-fit: cover; }
        .card { border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
        .card-header { background: #00796b; color: white; font-weight: bold; }
        .card-body { background: white; }
      
        footer { background: #004d40; color: white; text-align: center; padding: 20px; margin-top: 20px; } 
    </style>
</head>
<body>
    <nav class="navbar navbar-dark">
        <a class="navbar-brand" href="#">
            <img src="<?php echo !empty($zoo['logo']) ? 'data:image/jpeg;base64,'.base64_encode($zoo['logo']) : ''; ?>" alt="Logo">
            <?php echo $zoo['nombre'] ?? 'Zoológico'; ?>
        </a>
        <ul>
            <li><a href="indexa.php">PerfilZoo</a></li>
            <li><a href="/zoologico_final/tabarez/historia.php">Historia</a></li>
            <li><a href="/zoologico_final/torales/zonas1.php">Zonas</a></li>
            <li><a href="/zoologico_final/continente/continentes.php">Continentes</a></li>
            <li><a href="/zoologico_final/emma/gestionar_actividades.php">Actividades</a></li>
            <li><a href="/zoologico_final/tabarez/eventos2.php">Noticias y Eventos</a></li>
            <li><a href="/zoologico_final/emma/gestionar_administrador.php">Ajustes</a></li>
            <li><a href="index.php" class="btn btn-danger">Cerrar sesión</a></li>
        </ul>
    </nav>

    <div class="container mt-4">
        <!-- Mostrar el formulario de edición -->
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#editModal">Editar Perfil</button>

        <!-- Modal de edición -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Editar Perfil del Zoológico</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre del Zoológico</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $zoo['nombre']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" required><?php echo $zoo['descripcion']; ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="horarios" class="form-label">Horarios</label>
                                <textarea class="form-control" id="horarios" name="horarios" required><?php echo $zoo['horarios']; ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="ubicacion" class="form-label">Ubicación</label>
                                <textarea class="form-control" id="ubicacion" name="ubicacion" required><?php echo $zoo['ubicacion']; ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="contacto" class="form-label">Contacto</label>
                                <textarea class="form-control" id="contacto" name="contacto" required><?php echo $zoo['contacto']; ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="reglas" class="form-label">Reglas</label>
                                <textarea class="form-control" id="reglas" name="reglas" required><?php echo $zoo['reglas']; ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="logo" class="form-label">Logo</label>
                                <input type="file" class="form-control" id="logo" name="logo">
                            </div>
                            <div class="modal-footer">
                        <!-- Botón para cancelar la acción -->
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <!-- Botón para guardar los cambios -->
                        <button type="submit" class="btn btn-success">Guardar Cambios</button>
                    </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="container mt-4">
        <h1><?php echo $zoo['nombre'] ?? 'Zoológico'; ?></h1>
        <p class="descripcion"> <?php echo $zoo['descripcion'] ?? 'Descripción no disponible.'; ?></p>
        <div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php if (count($imagenes) > 0): ?>
                    <?php foreach($imagenes as $index => $imagen): ?>
                        <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                            <img src="<?php echo $imagen; ?>" class="d-block w-100" alt="Imagen <?php echo $index + 1; ?>">
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="carousel-item active">
                        <img src="ruta/a/una/imagen/default.jpg" class="d-block w-100" alt="Imagen por defecto">
                    </div>
                <?php endif; ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Horarios</div>
                    <div class="card-body">
                        <p><?php echo $zoo['horarios'] ?? 'Sin horarios'; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Reglas</div>
                    <div class="card-body">
                        <ul>
                            <?php 
                            $reglas = explode("\n", $zoo['reglas'] ?? 'Sin reglas');
                            foreach ($reglas as $regla) {
                                echo '<li>' . htmlspecialchars($regla) . '</li>';
                            }
                            ?>
                        </ul>
                    </div>
                </div>
                </div>
                <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Ubicación</div>
                    <div class="card-body">
                        <ul>
                            <?php 
                            $ubi = explode("\n", $zoo['ubicacion'] ?? 'Sin ubicacion');
                            foreach ($ubi as $ubicacion) {
                                echo '<li>' . htmlspecialchars($ubicacion) . '</li>';
                            }
                            ?>
                        </ul>
                    </div>
                </div>
                <br>
                <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Contacto</div>
                    <div class="card-body">
                        <ul>
                            <?php 
                            $contactos = explode("\n", $zoo['contacto'] ?? 'Sin contacto');
                            foreach ($contactos as $contacto) {
                                echo '<li>' . htmlspecialchars($contacto) . '</li>';
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <footer>
        <p>&copy; <?php echo date("Y"); ?> zoolink. Todos los derechos reservados</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
