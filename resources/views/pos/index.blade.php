<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Punto de venta — Shoppy</title></head>
<body>
    <h1>Punto de venta</h1>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Cerrar sesión</button>
    </form>
</body>
</html>
