<?php
include("conexion.php");
include("obtener_tablas.php");

?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Prototipo Eulen</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="style.css">
</head>


<body>
  <header>
    <img src="imagenes/GRUPO-EULEN.jpg" alt="">
    <h1>Panel de Gestión - Eulen Colombia</h1>
    <p>Visualización dinámica de datos, gráficos y gestión por módulos</p>
    <a href="index.html">Inicio</a>
    <a href="aplicaciones.html">Aplicativos</a>
    <a href="proyectos.php">Proyectos</a>

  </header>
  <div class="container module">
    <h2>Módulo Proyectos</h2>
    <form action="importar_proyecto.php" method="POST" enctype="multipart/form-data" class="my-3">
      <label for="delegacion">Delegación:</label>
      <select name="delegacion" required class="form-select mb-2">
        <option value="delegacion_antioquia">Antioquia</option>
        <option value="delegacion_centro">Centro</option>
        <option value="delegacion_norte">Norte</option>
        <option value="delegacion_occidente">Occidente</option>
        <option value="mantenimiento_nacional">Mantenimiento Nacional</option>
      </select>
      <label for="archivo">Importar CSV:</label>
      <input type="file" name="archivo" accept=".csv" required>
      <button type="submit" class="btn btn-primary">Importar</button>
    </form>


    <form method="POST" action="#">
      <label for="filtroDelegacion">Filtrar por delegacion</label>
      <select name="tabla" id="filtroDelegacion" required class="form-select">
        <option value="">-- Elige una tabla --</option>
        <?php foreach ($tablas as $tabla): ?>
          <option value=" <?= $tabla ?>"><?= $tabla ?></option>
        <?php endforeach; ?>
      </select>
    </form>




    <label for="filtroResponsable" class="form-label">Filtrar por responsable</label>
    <select id="filtroResponsable" class="form-select"></select>
    <label for="filtroMes" class="form-label">Filtrar por mes</label>
    <select id="filtroMes" class="form-select"></select>
  </div>

  <div class="row">
    <div class="col-md-6">
      <canvas id="graficoBarras"></canvas>
    </div>
    <div class="col-md-6">
      <canvas id="graficoTorta"></canvas>
    </div>
  </div>




  <div class="mt-4">
    <h4>Tabla de responsables (Cantidad de tareas)</h4>
    <table class="table table-bordered" id="tablaResponsables">
      <thead>
        <tr>
          <th>Responsable</th>
          <th>Total Tareas</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
  </div>
  </div>

  <script>
    const delegacionSelect = document.getElementById('filtroDelegacion');
    const responsableSelect = document.getElementById('filtroResponsable');
    const mesSelect = document.getElementById('filtroMes');

    let datosOriginales = [];
    let barChart, pieChart;

    delegacionSelect.addEventListener('change', () => {
      const tabla = delegacionSelect.value;
      if (!tabla) return;

      fetch(`datos_proyectos.php?tabla=${encodeURIComponent(tabla)}`)
        .then(res => res.json())
        .then(data => {
          datosOriginales = data;

          const responsablesUnicos = [...new Set(data.map(d => d.responsable))];
          responsableSelect.innerHTML = '<option value="">Todos</option>' + responsablesUnicos.map(r => `<option value="${r}">${r}</option>`).join('');

          const mesesUnicos = [...new Set(data.map(d => d.mes))];
          mesSelect.innerHTML = '<option value="">Todos</option>' + mesesUnicos.map(m => `<option value="${m}">${m}</option>`).join('');

          actualizarVisuales(data);
        })
        .catch(error => console.error("Error al obtener datos:", error));
    });

    responsableSelect.addEventListener('change', aplicarFiltros);
    mesSelect.addEventListener('change', aplicarFiltros);

    function aplicarFiltros() {
      let filtrados = [...datosOriginales];
      const responsable = responsableSelect.value;
      const mes = mesSelect.value;

      if (responsable) {
        filtrados = filtrados.filter(d => d.responsable === responsable);
      }
      if (mes) {
        filtrados = filtrados.filter(d => d.mes === mes);
      }

      actualizarVisuales(filtrados);
    }

    function actualizarVisuales(data) {
      const labels = data.map(d => d.responsable);
      const completadas = data.map(d => d.completadas);
      const pendientes = data.map(d => d.pendientes);
      const vencidas = data.map(d => d.vencidas);

      const tbody = document.querySelector('#tablaResponsables tbody');
      tbody.innerHTML = labels.map((r, i) => `<tr><td>${r}</td><td>${data[i].total}</td></tr>`).join('');

      if (barChart) barChart.destroy();
      if (pieChart) pieChart.destroy();

      barChart = new Chart(document.getElementById('graficoBarras'), {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
              label: 'Completadas',
              data: completadas,
              backgroundColor: '#28a745'
            },
            {
              label: 'Pendientes',
              data: pendientes,
              backgroundColor: '#ffc107'
            },
            {
              label: 'Vencidas',
              data: vencidas,
              backgroundColor: '#dc3545'
            }
          ]
        },
        options: {
          responsive: true,
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      });






      const avance = data.map(d => d.total > 0 ? Math.round((d.completadas / d.total) * 100) : 0);
      pieChart = new Chart(document.getElementById('graficoTorta'), {
        type: 'pie',
        data: {
          labels: labels,
          datasets: [{
            label: 'Porcentaje de avance',
            data: avance,
            backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545']
          }]
        }
      });
    }
  </script>

</body>

</html>