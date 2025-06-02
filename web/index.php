<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Monitor de Temperaturas</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #eef1f5;
      margin: 0;
      padding: 40px 20px;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    h1 {
      margin-bottom: 30px;
      color: #333;
    }

    .alert {
      background: #ff4d4d;
      color: white;
      padding: 15px 25px;
      border-radius: 10px;
      margin: 20px;
      font-weight: bold;
      display: none;
    }

    .charts-container {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 50px;
    }

    .gauges {
      display: flex;
      justify-content: center;
      gap: 50px;
      flex-wrap: wrap;
    }

    canvas {
      max-width: 600px;
      width: 90vw;
    }

    .gauge-title {
      text-align: center;
      margin-top: -40px;
      margin-bottom: 10px;
      font-weight: bold;
      color: #333;
    }
  </style>
</head>
<body>

<h1>Monitor de Temperaturas</h1>

<div class="alert" id="alerta">
  ⚠ ¡Alerta! Una temperatura supera el límite permitido.
</div>

<div class="charts-container">
  <canvas id="lineChart"></canvas>

  <div class="gauges">
    <div>
      <canvas id="gauge1" width="250" height="250"></canvas>
      <div class="gauge-title" id="label1"></div>
    </div>
    <div>
      <canvas id="gauge2" width="250" height="250"></canvas>
      <div class="gauge-title" id="label2"></div>
    </div>
  </div>
</div>

<!-- El <body> sigue igual hasta aquí... -->
<script>
let lineChart, gauge1Chart, gauge2Chart;
const limite = 28;

// Crear gráfico de líneas con 20 puntos de tiempo
function crearGraficos(sensor1, sensor2) {
  const ctxLine = document.getElementById('lineChart').getContext('2d');

  const labels = sensor1.map(p => new Date(p.sensor1_date).toLocaleTimeString());
  const datos1 = sensor1.map(p => parseFloat(p.sensor1_temp));
  const datos2 = sensor2.map(p => parseFloat(p.sensor2_temp));

  lineChart = new Chart(ctxLine, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [
        {
          label: 'Sensor 1',
          data: datos1,
          borderColor: '#36A2EB',
          backgroundColor: 'rgba(54,162,235,0.2)',
          tension: 0.3,
          pointRadius: 3
        },
        {
          label: 'Sensor 2',
          data: datos2,
          borderColor: '#FF6384',
          backgroundColor: 'rgba(255,99,132,0.2)',
          tension: 0.3,
          pointRadius: 3
        }
      ]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          max: 50,
          title: { display: true, text: 'Temperatura (°C)' }
        },
        x: {
          title: { display: true, text: 'Hora' },
          ticks: {
            maxRotation: 45,
            minRotation: 45
          }
        }
      }
    }
  });

  const t1 = datos1[datos1.length - 1];
  const t2 = datos2[datos2.length - 1];

  const gauge1 = document.getElementById('gauge1').getContext('2d');
  const gauge2 = document.getElementById('gauge2').getContext('2d');

  gauge1Chart = crearGauge(gauge1, t1, '#36A2EB');
  gauge2Chart = crearGauge(gauge2, t2, '#FF6384');

  actualizarTexto(t1, t2);
  verificarAlerta(t1, t2);
}

// Crea un gauge semicircular
function crearGauge(ctx, valor, color) {
  return new Chart(ctx, {
    type: 'doughnut',
    data: {
      datasets: [{
        data: [valor, 50 - valor],
        backgroundColor: [color, '#e0e0e0'],
        borderWidth: 0,
        cutout: '70%',
        circumference: 180,
        rotation: -90
      }]
    },
    options: {
      responsive: false,
      plugins: {
        tooltip: { enabled: false },
        legend: { display: false }
      }
    }
  });
}

function actualizarTexto(t1, t2) {
  document.getElementById('label1').textContent = `Sensor 1: ${t1.toFixed(1)}°C`;
  document.getElementById('label2').textContent = `Sensor 2: ${t2.toFixed(1)}°C`;
}

function verificarAlerta(t1, t2) {
  const alerta = document.getElementById('alerta');
  alerta.style.display = (t1 >= limite || t2 >= limite) ? 'block' : 'none';
}

// Actualiza los valores cada 5 segundos
function actualizarDatos() {
  fetch('datos.php')
    .then(res => res.json())
    .then(data => {
      const sensor1 = data.sensor1;
      const sensor2 = data.sensor2;

      const labels = sensor1.map(p => new Date(p.sensor1_date).toLocaleTimeString());
      const datos1 = sensor1.map(p => parseFloat(p.sensor1_temp));
      const datos2 = sensor2.map(p => parseFloat(p.sensor2_temp));

      lineChart.data.labels = labels;
      lineChart.data.datasets[0].data = datos1;
      lineChart.data.datasets[1].data = datos2;
      lineChart.update();

      const t1 = datos1[datos1.length - 1];
      const t2 = datos2[datos2.length - 1];

      gauge1Chart.data.datasets[0].data = [t1, 50 - t1];
      gauge2Chart.data.datasets[0].data = [t2, 50 - t2];
      gauge1Chart.update();
      gauge2Chart.update();

      actualizarTexto(t1, t2);
      verificarAlerta(t1, t2);
    });
}

// Iniciar todo
fetch('datos.php')
  .then(res => res.json())
  .then(data => {
    crearGraficos(data.sensor1, data.sensor2);
    setInterval(actualizarDatos, 5000);
  });
</script>

</body>
</html>
