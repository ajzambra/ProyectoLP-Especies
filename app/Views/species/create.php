<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Nueva Especie</title>
</head>
<body>

    <h1>Registro de Nueva Especie</h1>
    <p>Complete los siguientes campos para añadir una nueva especie.</p>

    <hr>

    <form action="/ProyectoLP-Especies/public/species/store" method="POST" enctype="multipart/form-data">
        
        <div>
            <label for="nombre_comun">Nombre Común:</label><br>
            <input type="text" id="nombre_comun" name="nombre_comun" required>
        </div>
        <br>

        <div>
            <label for="tipo">Tipo de Especie:</label><br>
            <select id="tipo" name="tipo">
                <option value="Fauna">Fauna</option>
                <option value="Flora">Flora</option>
            </select>
        </div>
        <br>

        <div>
            <label for="id_ecosistema">Ecosistema:</label><br>
            <select id="id_ecosistema" name="id_ecosistema" required>
                <option value="">-- Seleccione un ecosistema --</option>
                <?php
                if (isset($ecosystems) && !empty($ecosystems)) {
                    foreach ($ecosystems as $ecosystem) {
                        echo '<option value="' . htmlspecialchars($ecosystem['id']) . '">' . htmlspecialchars($ecosystem['nombre']) . '</option>';
                    }
                }
                ?>
            </select>
        </div>
        <br>

        <div>
            <label for="descripcion">Descripción:</label><br>
            <textarea id="descripcion" name="descripcion" rows="5" cols="40"></textarea>
        </div>
        <br>

        <div>
            <label for="imagen">Evidencia Fotográfica:</label><br>
            <input type="file" id="imagen" name="imagen" accept="image/*">
        </div>
        <br>

        <div>
            <button type="submit">Registrar Especie</button>
        </div>

    </form>

</body>
</html>