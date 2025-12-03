<?php
require_once 'db.php';

// Fetch stats
try {
    // Total Users
    $stmt = $pdo->query("CALL sp_count_users()");
    $totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    $stmt->closeCursor();

    // Monthly Stats for Chart
    $stmt = $pdo->query("CALL sp_get_monthly_stats()");
    $monthlyStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    // Prepare data for Chart.js
    $months = [];
    $counts = [];
    foreach (array_reverse($monthlyStats) as $stat) {
        $months[] = date('M Y', strtotime($stat['month'] . '-01'));
        $counts[] = $stat['count'];
    }

} catch (PDOException $e) {
    $error = "Error fetching stats: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PHP CRUD Pro</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container">
        <header>
            <h1>Dashboard</h1>
            <p class="subtitle">Resumen general del sistema</p>
        </header>

        <div class="dashboard-grid">
            <!-- Total Users Card -->
            <div class="stat-card">
                <span class="stat-title">Total Usuarios</span>
                <span class="stat-value"><?= $totalUsers ?></span>
                <div class="stat-trend trend-up">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                    <span>Actualizado hoy</span>
                </div>
            </div>

            <!-- Active Users Card (Mocked for demo) -->
            <div class="stat-card">
                <span class="stat-title">Usuarios Activos</span>
                <span class="stat-value"><?= floor($totalUsers * 0.8) ?></span>
                <div class="stat-trend trend-up">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                    <span>+12% vs mes anterior</span>
                </div>
            </div>

            <!-- New Users Card (Mocked for demo) -->
            <div class="stat-card">
                <span class="stat-title">Nuevos (Mes)</span>
                <span class="stat-value"><?= end($counts) ?? 0 ?></span>
                <div class="stat-trend trend-up">
                    <span>Registro continuo</span>
                </div>
            </div>
        </div>

        <div class="chart-container">
            <canvas id="usersChart"></canvas>
        </div>

        <div class="card">
            <div class="header-actions">
                <h2>Accesos Rápidos</h2>
            </div>
            <div style="display: flex; gap: 1rem;">
                <a href="create.php" class="btn btn-primary">Crear Nuevo Usuario</a>
                <a href="users.php" class="btn" style="background: #f1f5f9;">Ver Todos los Usuarios</a>
            </div>
        </div>

        <footer>
            Programmed by PrettyVatt00
        </footer>
    </div>

    <script>
        const ctx = document.getElementById('usersChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($months) ?>,
                datasets: [{
                    label: 'Nuevos Usuarios',
                    data: <?= json_encode($counts) ?>,
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#6366f1',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Crecimiento de Usuarios (Últimos 6 meses)',
                        align: 'start',
                        font: {
                            size: 16,
                            family: "'Inter', sans-serif",
                            weight: 600
                        },
                        color: '#1e293b',
                        padding: {
                            bottom: 20
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            borderDash: [2, 4],
                            color: '#e2e8f0',
                            drawBorder: false
                        },
                        ticks: {
                            stepSize: 1
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
