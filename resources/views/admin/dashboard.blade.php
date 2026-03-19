<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Panel de administración — Shoppy</title></head>
<body>
    <h1>Panel de administración</h1>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Cerrar sesión</button>
    </form>
</body>
</html>
