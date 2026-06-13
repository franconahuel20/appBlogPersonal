<?php
require_once __DIR__ . '/db_config.php';

$mensaje = '';
$tipoMensaje = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'crear') {
        $titulo = trim($_POST['titulo'] ?? '');
        $contenido = trim($_POST['contenido'] ?? '');
        if ($titulo !== '' && $contenido !== '') {
            $stmt = $conn->prepare('INSERT INTO posts (titulo, contenido) VALUES (?, ?)');
            $stmt->bind_param('ss', $titulo, $contenido);
            $stmt->execute();
            $mensaje = '¡Post agregado exitosamente!';
        } else {
            $mensaje = 'Título y contenido son obligatorios.';
            $tipoMensaje = 'danger';
        }
    }

    if ($accion === 'editar') {
        $id = (int)($_POST['id'] ?? 0);
        $titulo = trim($_POST['titulo'] ?? '');
        $contenido = trim($_POST['contenido'] ?? '');
        if ($id > 0 && $titulo !== '' && $contenido !== '') {
            $stmt = $conn->prepare('UPDATE posts SET titulo = ?, contenido = ? WHERE id = ?');
            $stmt->bind_param('ssi', $titulo, $contenido, $id);
            $stmt->execute();
            $mensaje = '¡Post actualizado exitosamente!';
        }
    }

    if ($accion === 'eliminar') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $conn->prepare('DELETE FROM posts WHERE id = ?');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $mensaje = '¡Post eliminado exitosamente!';
        }
    }
}

$posts = $conn->query('SELECT id, titulo, contenido, fecha_publicacion FROM posts ORDER BY fecha_publicacion DESC');
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mi Blog Personal - Franco Nahuel Herrera</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #d8e1e7; font-family: Arial, sans-serif; }
        .profile-img { width: 150px; height: 150px; object-fit: cover; border: 5px solid #0d6efd; }
        .hero { text-align: center; padding-top: 35px; padding-bottom: 25px; }
        .card { border: 0; box-shadow: 0 2px 10px rgba(0,0,0,.08); }
        .post-content { white-space: pre-wrap; }
    </style>
</head>
<body>
<div class="container py-4">
    <header class="hero">
        <h1 class="display-5">Mi Blog Personal</h1>
        <nav class="mb-3">
            <a href="index.php" class="me-3 text-decoration-none">Inicio del Blog</a>
            <a href="personal.php" class="text-decoration-none">Mis Datos Personales</a>
        </nav>
        <img src="perfil.jpg" alt="Foto de perfil de Franco Nahuel Herrera" class="profile-img rounded-circle img-fluid">
    </header>

    <?php if ($mensaje): ?>
        <div class="alert alert-<?= htmlspecialchars($tipoMensaje) ?>"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <section class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h2 class="h3 m-0">Últimas Entradas</h2>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#crearModal">+ Agregar nuevo post</button>
        </div>

        <?php if ($posts && $posts->num_rows > 0): ?>
            <?php while ($post = $posts->fetch_assoc()): ?>
                <article class="card mb-3">
                    <div class="card-body">
                        <h3 class="h4"><?= htmlspecialchars($post['titulo']) ?></h3>
                        <p class="text-muted small mb-2">Publicado el: <?= htmlspecialchars($post['fecha_publicacion']) ?></p>
                        <p class="post-content"><?= htmlspecialchars($post['contenido']) ?></p>
                        <div class="d-flex gap-2 flex-wrap">
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#verModal<?= $post['id'] ?>">👁 Ver</button>
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editarModal<?= $post['id'] ?>">✏ Editar</button>
                            <form method="post" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este post? Esta acción es irreversible.');">
                                <input type="hidden" name="accion" value="eliminar">
                                <input type="hidden" name="id" value="<?= (int)$post['id'] ?>">
                                <button class="btn btn-danger btn-sm" type="submit">✖ Eliminar</button>
                            </form>
                        </div>
                    </div>
                </article>

                <div class="modal fade" id="verModal<?= $post['id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content">
                        <div class="modal-header"><h5 class="modal-title"><?= htmlspecialchars($post['titulo']) ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                        <div class="modal-body"><p class="post-content"><?= htmlspecialchars($post['contenido']) ?></p></div>
                    </div></div>
                </div>

                <div class="modal fade" id="editarModal<?= $post['id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content">
                        <form method="post">
                            <div class="modal-header"><h5 class="modal-title">Editar post</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                            <div class="modal-body">
                                <input type="hidden" name="accion" value="editar">
                                <input type="hidden" name="id" value="<?= (int)$post['id'] ?>">
                                <label class="form-label">Título</label>
                                <input class="form-control mb-3" name="titulo" value="<?= htmlspecialchars($post['titulo']) ?>" required>
                                <label class="form-label">Contenido</label>
                                <textarea class="form-control" name="contenido" rows="7" required><?= htmlspecialchars($post['contenido']) ?></textarea>
                            </div>
                            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Salir</button><button class="btn btn-primary" type="submit">Guardar</button></div>
                        </form>
                    </div></div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="alert alert-info">Todavía no hay posts cargados.</div>
        <?php endif; ?>
    </section>
</div>

<div class="modal fade" id="crearModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content">
        <form method="post">
            <div class="modal-header"><h5 class="modal-title">Agregar nuevo post</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <input type="hidden" name="accion" value="crear">
                <label class="form-label">Título</label>
                <input class="form-control mb-3" name="titulo" required>
                <label class="form-label">Contenido</label>
                <textarea class="form-control" name="contenido" rows="7" required></textarea>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Salir</button><button class="btn btn-success" type="submit">Guardar</button></div>
        </form>
    </div></div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
