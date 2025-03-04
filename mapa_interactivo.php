<?php
include 'conexion.php';
$sql = "SELECT * FROM zoologico LIMIT 1";
$result = $conn->query($sql);
$zoo = $result->fetch_assoc();
$imagenes = glob("imagenes/*.jpeg");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mapa Interactivo</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@700&family=Pacifico&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <style>
    /* Estilos generales */
    body {
      margin: 0;
      padding: 0;
      font-family: 'Roboto', sans-serif;
      display: flex;
      flex-direction: column;
      align-items: center;
      background-color: #f1f1f1;
      overflow-x: hidden; /* Evitar el scroll horizontal */
    }
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
    .navbar-brand img { height: 50px; margin-right: 10px; }
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
    /* Estilos para las instrucciones */
    .instructions-container {
      max-width: 1300px;
      margin: 15px auto;
      background-color: #f8f9fa;
      border-left: 4px solid #065f46;
      padding: 15px 20px;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      width: calc(100% - 20px);
      animation: fadeIn 0.8s ease-out;
      position: relative;
      overflow: hidden;
    }
    .instructions-container::before {
      content: "GUÍA";
      position: absolute;
      right: 0;
      top: 0;
      background-color: #065f46;
      color: white;
      font-size: 0.7em;
      padding: 3px 12px;
      border-bottom-left-radius: 8px;
      font-weight: bold;
    }
    .instructions-container h3 {
      color: #065f46;
      margin-top: 0;
      font-size: 1.2em;
      display: flex;
      align-items: center;
    }
    .instructions-container h3::before {
      content: "🔍";
      margin-right: 8px;
      font-size: 1.1em;
    }
    .instructions-container ul {
      margin: 0;
      padding-left: 25px;
      color: #333;
    }
    .instructions-container li {
      margin-bottom: 8px;
      position: relative;
    }
    .instructions-container li:last-child {
      margin-bottom: 0;
    }
    /* Contenedor del mapa y listado */
    .map-container-wrapper {
      display: flex;
      justify-content: center;
      gap: 30px;
      width: 100%;
      max-width: 1300px;
      margin-top: 20px;
      padding: 0 10px;
    }
    /* Mapa interactivo */
    .map-container {
      position: relative;
      width: 80%;
      height: 600px;
      background: url('imgg/Imagen\ de\ WhatsApp\ 2025-02-25\ a\ las\ 13.08.18_1676902c.jpg') no-repeat center center;
      background-size: cover;
      border: 2px solid #065f46;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      transition: all 0.3s ease;
    }
    /* Listado de zonas */
    .zones-list {
      width: 250px;
      background-color: #fff;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      overflow-y: auto;
      max-height: 600px;
      scrollbar-width: thin;
      scrollbar-color: #065f46 transparent;
      opacity: 0; /* Oculto inicialmente */
      transform: translateY(20px); /* Desplazado hacia abajo */
      animation: fadeIn 0.5s ease forwards; /* Animación de entrada */
    }
    .zones-list::-webkit-scrollbar {
      width: 6px;
    }
    .zones-list::-webkit-scrollbar-thumb {
      background-color: #065f46;
      border-radius: 3px;
    }
    /* Campo de búsqueda */
    #zoneSearchInput {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 6px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.05);
      transition: all 0.3s ease;
    }
    #zoneSearchInput:focus {
      outline: none;
      border-color: #065f46;
      box-shadow: 0 0 0 3px rgba(6, 95, 70, 0.2);
    }
    #zoneSearchInput::placeholder {
      color: #888;
    }
    .zone-item {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 15px;
      padding: 12px;
      border-radius: 8px;
      transition: all 0.3s ease;
      cursor: pointer;
      border: 1px solid transparent;
      position: relative; /* Para el parpadeo */
    }
    .zone-item:hover {
      background-color: rgba(6, 95, 70, 0.1);
      transform: translateX(5px);
      border-color: rgba(6, 95, 70, 0.2);
    }
    .zone-item.active {
      background-color: rgba(6, 95, 70, 0.2);
      transform: translateX(5px);
      border-left: 3px solid #065f46;
      animation: blink 1.5s infinite; /* Parpadeo */
    }
    @keyframes blink {
      0%, 100% {
        background-color: rgba(6, 95, 70, 0.2);
      }
      50% {
        background-color: rgba(255, 255, 0, 0.5); /* Color amarillo con opacidad */
      }
    }
    .zone-item img {
      width: 50px;
      height: 50px;
      object-fit: cover;
      border-radius: 6px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .zone-item span {
      font-weight: bold;
      color: #065f46;
      font-size: 0.9em;
    }
    /* Estilos para los marcadores */
    .marker {
      position: absolute;
      width: 50px;
      height: 50px;
      background-size: cover;
      background-position: center;
      border-radius: 50%;
      border: 3px solid #fff;
      box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.3);
      cursor: pointer;
      transform: translate(-50%, -50%);
      transition: all 0.3s ease;
      animation: pulse 1.5s infinite;
      --pulse-color: rgba(32, 4, 240, 0.5); /* Color por defecto */
    }
    @keyframes pulse {
      0% {
        box-shadow: 0 0 0 0 var(--pulse-color);
      }
      70% {
        box-shadow: 0 0 0 20px var(--pulse-color);
      }
      100% {
        box-shadow: 0 0 0 20px rgba(0, 0, 0, 0);
      }
    }
    .marker.highlighted {
      border: 3px solid rgb(212, 255, 0);
      box-shadow: 0 0 15px rgba(255, 255, 0, 0); /* Amarillo con opacidad */
      transform: translate(-50%, -50%) scale(1.5);
      z-index: 10;
    }
    .marker:hover {
      transform: translate(-50%, -50%) scale(1.2);
    }
    /* Tooltip para el nombre de la zona */
    .marker:hover::after {
      content: attr(data-name);
      position: absolute;
      top: -25px;
      left: 50%;
      transform: translateX(-50%);
      background: rgba(0,0,0,0.7);
      color: #fff;
      padding: 2px 6px;
      border-radius: 3px;
      font-size: 0.8em;
      white-space: nowrap;
      z-index: 10;
    }
    /* Animación de aparición gradual */
    @keyframes fadeIn {
      from { opacity: 0; transform: scale(0.8); }
      to { opacity: 1; transform: scale(1); }
    }
    /* Estilos para la info-card */
    .info-card {
      position: absolute;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 6px 15px rgba(0,0,0,0.3);
      width: 300px;
      z-index: 1000;
      overflow: hidden;
      animation: fadeIn 0.3s ease;
      font-family: Arial, sans-serif;
      opacity: 1; /* Inicialmente visible */
      transition: opacity 0.3s ease; /* Transición para el desvanecimiento */
    }
    .info-card img {
      width: 100%;
      height: 200px;
      object-fit: cover;
      display: block;
    }
    .info-card .content {
      padding: 15px;
      max-height: 300px;
      overflow-y: auto;
    }
    .info-card .close-btn {
      position: absolute;
      top: 5px;
      right: 10px;
      cursor: pointer;
      font-weight: bold;
      color: #ffffff;
      font-size: 1.2em;
    }
    .info-card .content h3,
    .info-card .content p,
    .info-card .content a {
      opacity: 0;
    }
    .info-card .content h3 {
      margin: 0 0 10px;
      font-size: 1.2em;
      color: #065f46;
      animation: fadeIn 1s ease forwards;
      animation-delay: 0.3s;
    }
    .info-card .content p {
      font-size: 0.9em;
      color: #555;
      margin: 0 0 15px;
      line-height: 1.4em;
    }
    .info-card .content p:nth-of-type(1) { animation: fadeIn 1s ease forwards; animation-delay: 0.5s; }
    .info-card .content p:nth-of-type(2) { animation: fadeIn 1s ease forwards; animation-delay: 0.7s; }
    .info-card .content p:nth-of-type(3) { animation: fadeIn 1s ease forwards; animation-delay: 0.9s; }
    .info-card .content p:nth-of-type(4) { animation: fadeIn 1s ease forwards; animation-delay: 1.1s; }
    .info-card .content a {
      display: inline-block;
      background: #065f46;
      color: #fff;
      text-decoration: none;
      padding: 8px 12px;
      border-radius: 5px;
      font-size: 0.9em;
      transition:background 0.3s ease;
      animation: fadeIn 1s ease forwards;
      animation-delay: 1.3s;
    }
    .info-card .content a:hover {
      background: #047857;
    }
    footer { background: #004d40; color: white; text-align: center; width: 100%;
      margin-top: 30px; } 
  </style>
</head>
<body>
<!-- Navigation Bar -->
<nav class="navbar navbar-dark">
    <a class="navbar-brand" href="#">
        <img src="<?php echo !empty($zoo['logo']) ? 'data:image/jpeg;base64,'.base64_encode($zoo['logo']) : ''; ?>" alt="Logo">
        <?php echo $zoo['nombre'] ?? 'Zoológico'; ?>
    </a>
    <ul>
        <li><a href="/zoologico_final/index.php">Inicio</a></li>
        <li><a href="/zoologico_final/tabarez/historiausuario.php">Historia</a></li>
        <li><a href="/zoologico_final/torales/mapa_interactivo.php">Mapa Interactivo</a></li>
        <li><a href="/zoologico_final/continente/continentesu.php">Continentes</a></li>
        <li><a href="/zoologico_final/emma/visualizar_actividades.php">Actividades</a></li>
        <li><a href="/zoologico_final/tabarez/eventoscli.php">Noticias y Eventos</a></li>
    </ul>
</nav>

  <h2>Mapa Interactivo</h2>
  
  <!-- Instrucciones de uso -->
  <div class="instructions-container">
    <h3>¿Cómo usar este mapa?</h3>
    <ul>
      <li><strong>Ver zonas:</strong> Haz clic en los marcadores para obtener información detallada.</li>
      <li><strong>Buscar áreas:</strong> Utiliza el campo de búsqueda para encontrar zonas específicas.</li>
      <li><strong>Navegar:</strong> Selecciona una zona de la lista para resaltarla en el mapa.</li>
    </ul>
  </div>
  
  <div class="map-container-wrapper">
    <div class="map-container" id="mapContainer" oncontextmenu="getCoordinates(event); return false;"></div>
    <div class="zones-list" id="zonesList">
      <!-- Campo de búsqueda -->
      <input type="text" id="zoneSearchInput" placeholder="Buscar zona...">
      <!-- Las zonas se cargarán dinámicamente -->
    </div>
  </div>
  
  <script>
    let currentInfoCard = null; // Variable para almacenar la info-card actual

    // Función para evitar colisiones entre marcadores
    function resolveCollision(x, y) {
      const markers = document.getElementsByClassName('marker');
      let newX = x;
      let newY = y;
      let collisionFound = true;
      let iteration = 0;
      const threshold = 60;
      while (collisionFound && iteration < 10) {
        collisionFound = false;
        for (let marker of markers) {
          const markerX = parseInt(marker.style.left);
          const markerY = parseInt(marker.style.top);
          const dx = newX - markerX;
          const dy = newY - markerY;
          const distance = Math.sqrt(dx * dx + dy * dy);
          if (distance < threshold) {
            collisionFound = true;
            newX += 10;
            newY += 10;
            break;
          }
        }
        iteration++;
      }
      return { x: newX, y: newY };
    }

    // Cargar zonas desde zonas.php
    function loadZones() {
      fetch('zonas.php')
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            console.log("Zonas cargadas:", data.zones);
            data.zones.forEach(zone => {
              addMarker(zone);
              addZoneToList(zone);
            });
            // Hacer que la lista de zonas aparezca de manera fluida
            document.getElementById('zonesList').style.opacity = 1;
            document.getElementById('zonesList').style.transform = 'translateY(0)';
          } else {
            console.error("Error al cargar zonas:", data.error);
          }
        })
        .catch(error => {
          console.error("Error al cargar zonas:", error);
        });
    }

    // Agregar marcador al mapa
    function addMarker(zone) {
      const coords = zone.Coordenadas.split(',');
      const x = parseInt(coords[0]);
      const y = parseInt(coords[1]);

      const marker = document.createElement('div');
      marker.className = 'marker';
      const pos = resolveCollision(x, y);
      marker.style.left = pos.x + 'px';
      marker.style.top = pos.y + 'px';
      marker.style.backgroundImage = `url(${zone.Fotografia})`;
      marker.setAttribute('data-name', zone.Nombre);
      marker.setAttribute('data-id', zone.Id_Zona);

      marker.addEventListener('click', function(e) {
        if (currentInfoCard) {
          // Desvanecer la info-card actual
          currentInfoCard.style.opacity = 0;
          setTimeout(() => {
            currentInfoCard.style.display = 'none'; // Ocultar después de desvanecerse
          }, 300); // Tiempo de desvanecimiento
        }
        showInfoCard(e, zone, pos.x, pos.y);
      });
      document.getElementById('mapContainer').appendChild(marker);
    }

    // Agregar zona a la lista y configurar clic para resaltar el marcador correspondiente
    function addZoneToList(zone) {
      const zoneList = document.getElementById('zonesList');
      const zoneItem = document.createElement('div');
      zoneItem.className = 'zone-item';
      zoneItem.innerHTML = `
        <img src="${zone.Fotografia}" alt="${zone.Nombre}">
        <span>${zone.Nombre}</span>
      `;
      zoneItem.addEventListener('click', function() {
        document.querySelectorAll('.zone-item').forEach(item => {
          item.classList.remove('active');
        });
        zoneItem.classList.add('active');
        highlightMarker(zone.Id_Zona);
      });
      zoneList.appendChild(zoneItem);
    }

    // Función para filtrar la lista de zonas según lo que se escribe en el input
    document.getElementById('zoneSearchInput').addEventListener('keyup', function() {
      const filter = this.value.toLowerCase();
      const zoneItems = document.querySelectorAll('.zone-item');
      zoneItems.forEach(item => {
        const zoneName = item.querySelector('span').textContent.toLowerCase();
        if (zoneName.indexOf(filter) > -1) {
          item.style.display = '';
        } else {
          item.style.display = 'none';
        }
      });
    });

    // Resaltar el marcador durante 5 segundos
    function highlightMarker(zoneId) {
      // Quitar el resaltado de todos los marcadores
      document.querySelectorAll('.marker').forEach(marker => {
        marker.classList.remove('highlighted');
        marker.style.setProperty('--pulse-color', 'rgba(32, 4, 240, 0.5)'); // Color azul por defecto
      });
      
      // Quitar el parpadeo de todas las zonas
      document.querySelectorAll('.zone-item').forEach(item => {
        item.classList.remove('active');
      });

      console.log("Buscando marcador con data-id:", zoneId);
      const targetMarker = document.querySelector(`.marker[data-id="${zoneId}"]`);
      if (targetMarker) {
        targetMarker.classList.add('highlighted');
        targetMarker.style.setProperty('--pulse-color', 'rgba(255, 255, 0, 0.5)'); // Cambiar a amarillo con opacidad
        // Remover el resaltado después de 5 segundos
        setTimeout(() => {
          targetMarker.classList.remove('highlighted');
          targetMarker.style.setProperty('--pulse-color', 'rgba(32, 4, 240, 0.3)'); // Volver a azul
        }, 5000);

        // Resaltar la zona en la lista
        const targetZoneItem = document.querySelector(`.zone-item img[alt="${targetMarker.getAttribute('data-name')}"]`).parentElement;
        targetZoneItem.classList.add('active');

        // Quitar el parpadeo después de 5 segundos
        setTimeout(() => {
          targetZoneItem.classList.remove('active');
        }, 5000);

        // Si el marcador no es visible, hacer scroll hasta él
        const mapContainer = document.getElementById('mapContainer');
        const markerRect = targetMarker.getBoundingClientRect();
        const containerRect = mapContainer.getBoundingClientRect();
        if (markerRect.top < containerRect.top || markerRect.bottom > containerRect.bottom) {
          targetMarker.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
          });
        }
      } else {
        console.warn("No se encontró marcador con data-id:", zoneId);
      }
    }

    // Mostrar info-card en el mapa
    function showInfoCard(event, zone, x, y) {
      let infoCard = document.getElementById('infoCard');
      if (!infoCard) {
        infoCard = document.createElement('div');
        infoCard.className = 'info-card';
        infoCard.id = 'infoCard';
        document.getElementById('mapContainer').appendChild(infoCard);
      }
      
      infoCard.innerHTML = `
        <span class="close-btn" onclick="closeInfoCard();">&times;</span>
        ${ zone.Fotografia ? `<img src="${zone.Fotografia}" alt="Imagen de ${zone.Nombre}">` : '' }
        <div class="content">
          <h3 style="animation-delay: 0.3s;">${zone.Nombre}</h3>
          <p style="animation-delay: 0.5s;"><strong>Coordenadas:</strong> ${zone.Coordenadas}</p>
          <p style="animation-delay: 0.7s;"><strong>Descripción:</strong> ${zone.Descripcion}</p>
          <p style="animation-delay: 0.9s;"><strong>Tooltip:</strong> ${zone.Tooltip}</p>
          <p style="animation-delay: 1.1s;"><strong>Enlace:</strong> <a href="${zone.Enlace}" target="_blank">${zone.Enlace}</a></p>
        </div>
      `;
      
      infoCard.style.display = 'block';
      infoCard.style.opacity = 1; // Asegurarse de que sea visible
      
      const mapContainer = document.getElementById('mapContainer');
      const mapWidth = mapContainer.clientWidth;
      const mapHeight = mapContainer.clientHeight;
      const cardWidth = infoCard.offsetWidth;
      const cardHeight = infoCard.offsetHeight;
      
      let leftPos = x;
      if (leftPos + cardWidth > mapWidth) {
        leftPos = mapWidth - cardWidth - 10;
      }
      if (leftPos < 0) {
        leftPos = 10;
      }
      
      let topPos = y;
      if (topPos + cardHeight > mapHeight) {
        topPos = mapHeight - cardHeight - 10;
      }
      if (topPos < 0) {
        topPos = 10;
      }
      
      infoCard.style.left = leftPos + 'px';
      infoCard.style.top = topPos + 'px';
    }

    // Cerrar la info-card
    function closeInfoCard() {
      const infoCard = document.getElementById('infoCard');
      if (infoCard) {
        infoCard.style.opacity = 0; // Desvanecer
        setTimeout(() => {
          infoCard.style.display = 'none'; // Ocultar después de desvanecerse
        }, 300); // Tiempo de desvanecimiento
      }
    }

    // Obtener coordenadas al hacer clic derecho en el mapa
    function getCoordinates(event) {
      const rect = document.getElementById('mapContainer').getBoundingClientRect();
      const x = event.clientX - rect.left;
      const y = event.clientY - rect.top;
      alert(`Coordenadas: ${Math.round(x)}, ${Math.round(y)}`);
    }

    // Cargar zonas al iniciar la página
    window.onload = loadZones;
  </script>
  
  <footer>
        <p>&copy; <?php echo date("Y"); ?> zoolink. Todos los derechos reservados</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>