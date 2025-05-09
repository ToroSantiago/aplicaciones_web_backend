<!DOCTYPE html>
<html>
<head>
    <title>Crear Perfume</title>
</head>
<body>
    <h1>Crear nuevo perfume</h1>

    <form method="POST" action="{{ route('perfumes.store') }}">
        @csrf

        <label>Nombre:</label>
        <input name="nombre" required><br>

        <label>Marca:</label>
        <input name="marca" required><br>

        <label>Descripción:</label>
        <textarea name="descripcion"></textarea><br>

        <label>Volumen (ml):</label>
        <input name="volumen" type="number" required><br>

        <label>Precio:</label>
        <input name="precio" type="number" required><br>

        <label>Género:</label>
        <select name="genero" required>
            <option value="M">Masculino</option>
            <option value="F">Femenino</option>
            <option value="U">Unisex</option>
        </select><br>

        <label>
            <input type="hidden" name="stock" value="0">
            <input type="checkbox" name="stock" value="1" checked>
            ¿Hay stock?
        </label><br>

        <button type="submit">Guardar</button>
    </form>
</body>
</html>
