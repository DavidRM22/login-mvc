<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard RRHH</title>
    <link rel="stylesheet" href="<?= asset('estilos.css') ?>">
    <link rel="stylesheet" href="<?= asset('dashboard.css') ?>">
</head>
<body class="dashboard-body">
<?php
$view = $_GET['view'] ?? 'table';
$activeView = in_array($view, ['table', 'gallery'], true) ? $view : 'table';

$totalEmployees = count($employees);
$instructors = count(array_filter($employees, fn($employee) => strtolower($employee['type'] ?? '') === 'instructor'));
$developers = count(array_filter($employees, fn($employee) => strtolower($employee['type'] ?? '') === 'desarrollador'));
$admins = count(array_filter($employees, fn($employee) => strtolower($employee['type'] ?? '') === 'administrador'));
$assistants = count(array_filter($employees, fn($employee) => strtolower($employee['type'] ?? '') === 'asistente administrativo'));
?>
<div class="dashboard-layout">
    <aside class="dashboard-sidebar">
        <h3>Dashboard</h3>
        <p>Panel de Administración</p>

        <span class="sidebar-section">PANEL</span>
        <a class="sidebar-link active" href="<?= route('dashboard', 'index') ?>">Inicio</a>
        <a class="sidebar-link" href="<?= route('dashboard', 'audit') ?>">Auditoría</a>

        <span class="sidebar-section">GENERAL</span>
        <a class="sidebar-link" href="<?= route('dashboard', 'addEmployee') ?>">Agregar empleado</a>
        <a class="sidebar-link" href="<?= route('dashboard', 'logout') ?>">Cerrar sesión</a>
    </aside>

    <main class="dashboard-main">
        <section class="panel panel--wide hr-panel hr-panel--fullscreen">
            <h1>Formulario Tabla Detallada</h1>

            <div class="module-headline">
                <h2>Gestión Interna (RRHH & Ops)</h2>
                <p>Recursos humanos, soporte al cliente y comunidad</p>
            </div>

            <nav class="tab-row">
                <a class="tab-item active" href="<?= route('dashboard', 'index') ?>">Recursos Humanos</a>
                <a class="tab-item" href="<?= route('dashboard', 'audit') ?>">Soporte</a>
                <a class="tab-item" href="<?= route('dashboard', 'audit') ?>">Comunidad</a>
            </nav>

            <div class="subtab-row">
                <a class="subtab-item active" href="<?= route('dashboard', 'index') ?>">Personal</a>
                <a class="subtab-item" href="<?= route('dashboard', 'index') ?>">Desempeño</a>
                <a class="subtab-item" href="<?= route('dashboard', 'index') ?>">Objetivos</a>
                <a class="subtab-item" href="<?= route('dashboard', 'audit') ?>">Auditoría</a>
            </div>

            <div class="module-header">
                <div>
                    <h3>Recursos Humanos</h3>
                    <p class="subtitle">Gestión de personal y empleados</p>
                </div>

                <div class="module-actions">
                    <a class="btn-secondary btn-inline" href="<?= route('dashboard', 'audit') ?>">Exportar</a>
                    <a class="btn-primary btn-inline" href="<?= route('dashboard', 'addEmployee') ?>">Agregar Empleado</a>
                </div>
            </div>

            <div class="stats-grid">
                <article class="stat-card">
                    <h4>Total Personal</h4>
                    <p class="stat-value"><?= $totalEmployees ?></p>
                    <small>Empleados registrados</small>
                </article>
                <article class="stat-card">
                    <h4>Instructores</h4>
                    <p class="stat-value"><?= $instructors ?></p>
                    <small>Equipo docente</small>
                </article>
                <article class="stat-card">
                    <h4>Desarrolladores</h4>
                    <p class="stat-value"><?= $developers ?></p>
                    <small>Equipo técnico</small>
                </article>
                <article class="stat-card">
                    <h4>Administradores</h4>
                    <p class="stat-value"><?= $admins ?></p>
                    <small>Personal administrativo</small>
                </article>
                <article class="stat-card">
                    <h4>Asist. Administrativos</h4>
                    <p class="stat-value"><?= $assistants ?></p>
                    <small>Personal de soporte</small>
                </article>
            </div>

            <div class="subtab-row media-tabs">
                <a class="subtab-item <?= $activeView === 'gallery' ? 'active' : '' ?>" href="<?= route('dashboard', 'index') ?>&view=gallery">Galería de Fotos</a>
                <a class="subtab-item <?= $activeView === 'table' ? 'active' : '' ?>" href="<?= route('dashboard', 'index') ?>&view=table">Tabla Detallada</a>
            </div>

            <div class="filter-row">
                <input type="search" placeholder="Buscar por nombre, email o puesto..." aria-label="Buscar personal">
                <select aria-label="Filtrar por tipo">
                    <option>Todos los tipos</option>
                    <option>Instructor</option>
                    <option>Admin</option>
                </select>
                <select aria-label="Filtrar por estado">
                    <option>Todos los estados</option>
                    <option>Activo</option>
                </select>
            </div>

            <?php if ($activeView === 'gallery'): ?>
                <div class="gallery-grid">
                    <?php foreach ($employees as $employee): ?>
                        <article class="profile-card">
                            <div class="avatar"><?= strtoupper(substr($employee['name'] ?? '-', 0, 1)) ?></div>
                            <h4><?= htmlspecialchars($employee['name'] ?? 'Sin nombre') ?></h4>
                            <span class="role-chip"><?= htmlspecialchars($employee['type'] ?? 'Sin tipo') ?></span>
                            <p><?= htmlspecialchars($employee['email'] ?? '-') ?></p>
                            <small>Registrado: <?= htmlspecialchars($employee['created_at'] ?? '-') ?></small>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="table-wrap table-wrap--dashboard">
                    <table class="audit-table dashboard-table">
                        <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Tipo</th>
                            <th>Puesto</th>
                            <th>Departamento</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($employees as $employee): ?>
                            <?php
                            $employeeType = $employee['type'] ?? 'Instructor';
                            $isAdmin = strtolower($employeeType) === 'administrador';
                            $employeeStatus = $employee['status'] ?? 'active';
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($employee['name'] ?? 'Sin nombre') ?></td>
                                <td><?= htmlspecialchars($employee['email'] ?? '-') ?></td>
                                <td><span class="role-chip <?= $isAdmin ? 'role-chip--admin' : '' ?>"><?= htmlspecialchars($employeeType) ?></span></td>
                                <td><?= htmlspecialchars($employee['position'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($employee['department'] ?? 'N/A') ?></td>
                                <td><span class="status-chip"><?= htmlspecialchars($employeeStatus) ?></span></td>
                                <td>
                                    <a class="table-link" href="<?= route('dashboard', 'audit') ?>">Ver</a>
                                    <a class="table-link" href="<?= route('dashboard', 'addEmployee') ?>">Editar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </main>
</div>
</body>
</html>
