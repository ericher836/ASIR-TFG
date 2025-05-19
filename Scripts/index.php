<?php
// Configuración de la base de datos
$servername = "localhost";
$username = "emrice";
$password = "emrice";
$database = "apache_mysql";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
	die("Conexión fallida: " . $conn->connect_error);
}

// Eliminar registro
if (isset($_GET['eliminar'])) {
	$id = intval($_GET['eliminar']);
	$conn->query("DELETE FROM personas WHERE id=$id");
	header("Location: index.php");
	exit();
}

// Obtener datos para editar
$editando = false;
$personaEditar = [
	'id' => '',
	'nombre' => '',
	'apellidos' => '',
	'correo' => '',
	'telefono' => '',
	'fecha_nacimiento' => ''
];
if (isset($_GET['editar'])) {
	$id = intval($_GET['editar']);
	$res = $conn->query("SELECT * FROM personas WHERE id=$id");
	if ($res->num_rows > 0) {
		$personaEditar = $res->fetch_assoc();
		$editando = true;
	}
}

// Insertar o actualizar registro
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$nombre = $conn->real_escape_string($_POST['nombre']);
	$apellidos = $conn->real_escape_string($_POST['apellidos']);
	$correo = $conn->real_escape_string($_POST['correo']);
	$telefono = $conn->real_escape_string($_POST['telefono']);
	$fecha_nacimiento = $conn->real_escape_string($_POST['fecha_nacimiento']);

	if (isset($_POST['id']) && $_POST['id'] != '') {
		// Actualizar
		$id = intval($_POST['id']);
		$sql = "UPDATE personas SET nombre='$nombre', apellidos='$apellidos', correo='$correo', telefono='$telefono', fecha_nacimiento='$fecha_nacimiento' WHERE id=$id";
		$conn->query($sql);
	} else {
		// Insertar
		$sql = "INSERT INTO personas (nombre, apellidos, correo, telefono, fecha_nacimiento) VALUES ('$nombre', '$apellidos', '$correo', '$telefono', '$fecha_nacimiento')";
		$conn->query($sql);
	}
	header("Location: index.php");
	exit();
}

// Mostrar formulario
echo "<h2>" . ($editando ? "Editar persona" : "Agregar nueva persona") . "</h2>";
echo "<form method='POST'> <input type='hidden' name='id' value='" . htmlspecialchars($personaEditar['id']) . "'> Nombre: <input type='text' name='nombre' value='" . htmlspecialchars($personaEditar['nombre']) . "' required><br> Apellidos: <input type='text' name='apellidos' value='" . htmlspecialchars($personaEditar['apellidos']) . "' required><br> Correo: <input type='email' name='correo' value='" . htmlspecialchars($personaEditar['correo']) . "' required><br> Telefono: <input type='text' name='telefono' value='" . htmlspecialchars($personaEditar['telefono']) . "'><br> Fecha de nacimiento: <input type='date' name='fecha_nacimiento' value='" . htmlspecialchars($personaEditar['fecha_nacimiento']) . "'><br> <input type='submit' value='" . ($editando ? "Actualizar" : "Agregar") . "'> " . ($editando ? "<a href='index.php'>Cancelar</a>" : "") . " </form>";

// Mostrar tabla de personas
$sql = "SELECT * FROM personas";
$result = $conn->query($sql);

echo "<h2>Lista de personas:</h2>";

if ($result->num_rows > 0) {
	echo "<table border='1'><tr><th>ID</th><th>Nombre</th><th>Apellidos</th><th>Correo</th><th>Telefono</th><th>Fecha de nacimiento</th><th>Acciones</th></tr>";
	while($row = $result->fetch_assoc()) {
		echo "<tr><td>".htmlspecialchars($row['id'])."</td><td>".htmlspecialchars($row['nombre'])."</td><td>".htmlspecialchars($row['apellidos'])."</td><td>".htmlspecialchars($row['correo'])."</td><td>".htmlspecialchars($row['telefono'])."</td><td>".htmlspecialchars($row['fecha_nacimiento'])."</td><td><a href='?editar=".$row['id']."'>Editar</a> | <a href='?eliminar=".$row['id']."' onclick=\"return confirm('¿Seguro que quieres eliminar este registro?');\">Eliminar</a></td></tr>";
	}
	echo "</table>";
} else {
	echo "<p>¡La base de datos esta vacia!</p>";
}

$conn->close();
?>
