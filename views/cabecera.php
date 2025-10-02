<!DOCTYPE html>
<html>
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='https://fonts.googleapis.com/css?family=Actor' rel='stylesheet'>
    <?php $cssVersion = @filemtime($_SERVER['DOCUMENT_ROOT'] . '/academia/academia.css') ?: time(); ?>
    <link rel="stylesheet" type="text/css" href="/academia/academia.css?v=<?php echo $cssVersion; ?>">
    <title>Academia Alma Mater</title>
    <script src="https://use.fontawesome.com/bc32f7bfed.js"></script>
</head>
<body>
    <!--Divisor de la cabezera -->
    <?php 
        // Mostrar estado de sesión
        require_once(__DIR__ . '/../models/session.php'); 
    ?>
    <div id="cabecera">
		<div id="logo">
            <?php $logoVersion = @filemtime($_SERVER['DOCUMENT_ROOT'] . '/academia/img/logo.png') ?: time(); ?>
			<a href="/academia/index.php"><img src="/academia/img/logo.png?v=<?php echo $logoVersion; ?>" alt="Academia Alma Mater"></a>
        </div>
        <div id="menu">
            <a href="/academia/index.php">Inicio</a>
            <a href="/academia/dashboard.php">Información</a>
            <a href="/academia/calendario.php">Calendario</a>
            <a href="/academia/asistencia.php">Asistencia</a>
            <a href="/academia/trabajos.php">Trabajos</a>
            <a href="/academia/comunicados.php">Comunicados</a>
            <a href="/academia/historial.php">Historial</a>
            <a href="/academia/gestionAlumnos.php">Gestión Alumnos</a>
            <a href="/academia/gestionAsignaturas.php">Gestión Asignaturas</a>
            <button id="themeToggle" style="margin-left:10px;">🌓</button>
            <div class="notification-bell" style="margin-left:10px; position:relative; display:inline-block;">
                <button id="notificationBtn" style="background:none; border:none; cursor:pointer; font-size:18px;">🔔</button>
                <span id="notificationCount" style="position:absolute; top:-8px; right:-8px; background:#ef4444; color:white; border-radius:50%; width:18px; height:18px; font-size:12px; display:none; align-items:center; justify-content:center;">0</span>
                <div id="notificationDropdown" style="position:absolute; top:100%; right:0; background:var(--card); border:1px solid var(--border); border-radius:8px; box-shadow:var(--shadow); min-width:300px; max-height:400px; overflow-y:auto; z-index:1000; display:none;">
                    <div style="padding:12px; border-bottom:1px solid var(--border); font-weight:600;">Notificaciones</div>
                    <div id="notificationList"></div>
                    <div style="padding:8px; text-align:center; border-top:1px solid var(--border);">
                        <button id="markAllRead" style="background:none; border:none; color:var(--brand); cursor:pointer; font-size:12px;">Marcar todas como leídas</button>
                    </div>
                </div>
            </div>
            <?php if (isAuthenticated()): ?>
                <span style="margin-left:10px; white-space: nowrap;">Hola, <?php echo htmlspecialchars(currentUserName()); ?> (<?php echo htmlspecialchars(currentUserRole()); ?>)</span>
                <a href="/academia/logout.php" style="margin-left:10px;">Salir</a>
            <?php else: ?>
                <a href="/academia/login.php" style="margin-left:10px;">Entrar</a>
            <?php endif; ?>
        </div>
    </div>
    <script>
    (function(){
        try {
            var root = document.documentElement;
            var saved = localStorage.getItem('theme');
            if (saved === 'dark') {
                root.setAttribute('data-theme','dark');
            }
            var btn = document.getElementById('themeToggle');
            if (btn) {
                btn.addEventListener('click', function(){
                    var isDark = root.getAttribute('data-theme') === 'dark';
                    if (isDark) {
                        root.removeAttribute('data-theme');
                        localStorage.setItem('theme','light');
                    } else {
                        root.setAttribute('data-theme','dark');
                        localStorage.setItem('theme','dark');
                    }
                });
            }

            // Sistema de notificaciones
            var notificationBtn = document.getElementById('notificationBtn');
            var notificationDropdown = document.getElementById('notificationDropdown');
            var notificationCount = document.getElementById('notificationCount');
            var notificationList = document.getElementById('notificationList');
            var markAllRead = document.getElementById('markAllRead');

            function loadNotifications() {
                fetch('/academia/api/notifications.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            updateNotificationCount(data.unreadCount);
                            renderNotifications(data.notifications);
                        }
                    })
                    .catch(error => console.error('Error loading notifications:', error));
            }

            function updateNotificationCount(count) {
                if (count > 0) {
                    notificationCount.textContent = count;
                    notificationCount.style.display = 'flex';
                } else {
                    notificationCount.style.display = 'none';
                }
            }

            function renderNotifications(notifications) {
                notificationList.innerHTML = '';
                if (notifications.length === 0) {
                    notificationList.innerHTML = '<div style="padding:12px; text-align:center; color:var(--muted);">No hay notificaciones</div>';
                    return;
                }
                notifications.forEach(function(notif) {
                    var item = document.createElement('div');
                    item.style.cssText = 'padding:12px; border-bottom:1px solid var(--border); cursor:pointer;' + (notif.leida ? '' : 'background:var(--accent);');
                    item.innerHTML = '<div style="font-weight:600; margin-bottom:4px;">' + notif.titulo + '</div><div style="font-size:14px; color:var(--muted);">' + notif.mensaje + '</div><div style="font-size:12px; color:var(--muted); margin-top:4px;">' + new Date(notif.fecha_creacion).toLocaleString() + '</div>';
                    item.addEventListener('click', function() {
                        if (!notif.leida) {
                            markAsRead(notif.id);
                            item.style.background = '';
                        }
                    });
                    notificationList.appendChild(item);
                });
            }

            function markAsRead(id) {
                fetch('/academia/api/notifications.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({action: 'mark_read', id: id})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadNotifications();
                    }
                })
                .catch(error => console.error('Error marking as read:', error));
            }

            if (notificationBtn) {
                notificationBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    notificationDropdown.style.display = notificationDropdown.style.display === 'none' ? 'block' : 'none';
                    if (notificationDropdown.style.display === 'block') {
                        loadNotifications();
                    }
                });
            }

            if (markAllRead) {
                markAllRead.addEventListener('click', function() {
                    fetch('/academia/api/notifications.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({action: 'mark_all_read'})
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            loadNotifications();
                        }
                    })
                    .catch(error => console.error('Error marking all as read:', error));
                });
            }

            // Cerrar dropdown al hacer click fuera
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.notification-bell')) {
                    notificationDropdown.style.display = 'none';
                }
            });

            // Cargar notificaciones al inicio
            loadNotifications();
            setInterval(loadNotifications, 30000); // Actualizar cada 30 segundos

        } catch(e) {}
    })();
    </script>
</body>
</html>