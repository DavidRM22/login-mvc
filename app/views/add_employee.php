<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar nuevo empleado</title>
    <link rel="stylesheet" href="<?= asset('estilos.css') ?>">
    <link rel="stylesheet" href="<?= asset('dashboard.css') ?>">
</head>
<body class="dashboard-body">
<div class="employee-shell">
    <section class="employee-modal">
        <div class="employee-modal__header">
            <h1>Agregar Nuevo Empleado</h1>
            <a class="employee-modal__close" href="<?= route('dashboard', 'index') ?>" aria-label="Cerrar">×</a>
        </div>

        <?php if (!empty($_SESSION['employee_error'])): ?>
            <div class="alert-error" style="margin-bottom: 16px; color: #dc2626; font-weight: 600;">
                <?= htmlspecialchars($_SESSION['employee_error']) ?>
            </div>
            <?php unset($_SESSION['employee_error']); ?>
        <?php endif; ?>

        <?php if (!empty($createdEmployee) && !empty($generatedPassword)): ?>
            <div class="alert-success" style="margin-bottom: 16px; padding: 12px; border-radius: 10px; background: #ecfdf3; border: 1px solid #bbf7d0; color: #065f46;">
                <strong>Empleado creado correctamente:</strong> <?= htmlspecialchars($createdEmployee['name']) ?><br>
                <strong>Contraseña temporal:</strong> <code><?= htmlspecialchars($generatedPassword) ?></code>
            </div>
        <?php endif; ?>

        <form class="employee-form" method="post" action="<?= route('dashboard', 'storeEmployee') ?>">
            <h2>Información del Usuario</h2>
            <div class="employee-grid employee-grid--two">
                <div class="field">
                    <label for="full_name">Nombre Completo *</label>
                    <input id="full_name" name="full_name" type="text" placeholder="Ej: Juan Pérez García" required>
                </div>

                <div class="field">
                    <label for="email">Correo Electrónico *</label>
                    <input id="email" name="email" type="email" placeholder="juan.perez@empresa.com" required>
                </div>

                <div class="field">
                    <label for="phone">Teléfono</label>
                    <input id="phone" name="phone" type="text" placeholder="+51 999 999 999">
                </div>
            </div>

            <h2>Foto de Perfil</h2>
            <div class="photo-row">
                <div class="photo-placeholder">📷</div>
                <div>
                    <button class="btn-secondary btn-inline" type="button">Capturar Foto con Reconocimiento Facial</button>
                    <p>Opcional: Captura la foto del empleado con verificación biométrica</p>
                </div>
            </div>

            <h2>Información Laboral</h2>
            <div class="employee-grid employee-grid--two">
                <div class="field">
                    <label for="type">Tipo de Empleado *</label>
                    <select id="type" name="type" required>
                        <option value="Administrador">Administrador</option>
                        <option value="Instructor" selected>Instructor</option>
                        <option value="Desarrollador">Desarrollador</option>
                        <option value="Asistente Administrativo">Asistente Administrativo</option>
                    </select>
                </div>

                <div class="field">
                    <label for="department">Departamento</label>
                    <select id="department" name="department">
                        <option value="">Seleccionar departamento</option>
                        <option value="Administracion">Administracion</option>
                        <option value="Recursos Humanos">Recursos Humanos</option>
                        <option value="Tecnologia de la Informacion">Tecnologia de la Informacion</option>
                        <option value="Desarrollo">Desarrollo</option>
                        <option value="Educacion">Educacion</option>
                        <option value="Marketing">Marketing</option>
                        <option value="Ventas">Ventas</option>
                        <option value="Soporte Tecnico">Soporte Tecnico</option>
                        <option value="Operaciones">Operaciones</option>
                        <option value="Finanzas">Finanzas</option>
                    </select>
                </div>

                <div class="field">
                    <label for="position">Puesto / Posición</label>
                    <select id="position" name="position">
                        <option value="">Seleccionar puesto</option>
                        <option value="Instructor Senior">Instructor Senior</option>
                        <option value="Instructor Junior">Instructor Junior</option>
                        <option value="Coordinador de Contenidos">Coordinador de Contenidos</option>
                        <option value="Especialista en Formacion">Especialista en Formacion</option>
                    
                    </select>
                </div>

                <div class="field">
                    <label for="hired_at">Fecha de Contratación</label>
                    <input id="hired_at" name="hired_at" type="date">
                </div>

                <div class="field">
                    <label for="status">Estado *</label>
                    <select id="status" name="status" required>
                        <option value="Activo" selected>Activo</option>
                        <option value="Inactivo">Inactivo</option>
                    </select>
                </div>
            </div>

            <h2>Información Profesional</h2>

            <div class="employee-actions">
                <a class="btn-secondary btn-inline" href="<?= route('dashboard', 'index') ?>">Cancelar</a>
                <button class="btn-primary btn-inline" type="submit">Agregar Empleado</button>
            </div>
        </form>
    </section>
</div>
</body>
</html>
