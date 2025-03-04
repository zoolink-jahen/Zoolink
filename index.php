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

$sql = "SELECT * FROM zoologico LIMIT 1";
$result = $conn->query($sql);
$zoo = $result->fetch_assoc();
$imagenes = glob("imagenes/*.jpeg");
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
        footer a{ text-decoration: none; color:white;} 
    </style>
</head>
<body>
    <nav class="navbar navbar-dark">
            <a class="navbar-brand" href="#">
                <img src="<?php echo !empty($zoo['logo']) ? 'data:image/jpeg;base64,'.base64_encode($zoo['logo']) : ''; ?>" alt="Logo">
                <?php echo $zoo['nombre'] ?? 'Zoológico'; ?>
            </a>
        <ul>
                <li><a href="index.php">Inicio</a></li>
                <li><a href="tabarez/historiausuario.php">Historia</a></li>
                <li><a href="torales/mapa_interactivo.php">Mapa Interactivo</a></li>
                <li><a href="continente/continentesu.php">Continentes</a></li>
                <li><a href="emma/visualizar_actividades.php">Actividades</a></li>
                <li><a href="tabarez/eventoscli.php">Noticias y Eventos</a></li>
            </ul>
    </nav>
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
        <p>&copy; <?php echo date("Y"); ?> zoolink. Todos los derechos reservados <a href="/zoologico_final/emma/login.php">admin</a></li></p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
