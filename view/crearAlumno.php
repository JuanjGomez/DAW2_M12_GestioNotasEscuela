<?php
session_start();
if(!isset($_SESSION['id_usu'])){
    header("Location: ../index.php");
    exit();
}
// Incluir el archivo de conexión
include '../database/conexion.php';

try{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = mysqli_real_escape_string($conexion, htmlspecialchars(trim($_POST['username'])));
        $dni = mysqli_real_escape_string($conexion, htmlspecialchars(trim($_POST['dni'])));
        $nombre = mysqli_real_escape_string($conexion,htmlspecialchars(trim($_POST['nombre'])));
        $apellido = mysqli_real_escape_string($conexion,htmlspecialchars(trim($_POST['apellido'])));
        $email = mysqli_real_escape_string($conexion,htmlspecialchars(trim($_POST['email'])));
        $telefono = mysqli_real_escape_string($conexion,htmlspecialchars(trim($_POST['telefono'])));
        $direccion = mysqli_real_escape_string($conexion,htmlspecialchars(trim($_POST['direccion'])));
        $fecha_nacimiento = mysqli_real_escape_string($conexion,htmlspecialchars(trim($_POST['fecha_nacimiento'])));

        // Verificacion de duplicados de alumnos en username
        $sqlNoDuplicados = "SELECT dni_alu FROM tbl_alumnos WHERE username_alu = ?";
        $stmtNoDuplicados = mysqli_stmt_init($conexion);
        mysqli_stmt_prepare($stmtNoDuplicados, $sqlNoDuplicados);
        mysqli_stmt_bind_param($stmtNoDuplicados, "s", $username);
        mysqli_stmt_execute($stmtNoDuplicados);
        $resultNoDuplicados = mysqli_stmt_get_result($stmtNoDuplicados);
        $rowNoDuplicados = mysqli_fetch_assoc($resultNoDuplicados);
        if($rowNoDuplicados){
            echo "<script>alert('Username ya registrado'); window.location.href='crearAlumno.php';</script>";
            exit();
        }

        // Verificacion de duplicados de alumnos en dni
        $sqlNoDuplicadosDni = "SELECT dni_alu FROM tbl_alumnos WHERE dni_alu =?";
        $stmtNoDuplicadosDni = mysqli_stmt_init($conexion);
        mysqli_stmt_prepare($stmtNoDuplicadosDni, $sqlNoDuplicadosDni);
        mysqli_stmt_bind_param($stmtNoDuplicadosDni, "s", $dni);
        mysqli_stmt_execute($stmtNoDuplicadosDni);
        $resultNoDuplicadosDni = mysqli_stmt_get_result($stmtNoDuplicadosDni);
        $rowNoDuplicadosDni = mysqli_fetch_assoc($resultNoDuplicadosDni);
        if($rowNoDuplicadosDni){
            echo "<script>alert('DNI ya registrado'); window.location.href='crearAlumno.php';</script>";
            exit();
        }

        // Preparar la consulta
        $stmt = mysqli_prepare($conexion, "INSERT INTO tbl_alumnos (username_alu, dni_alu, nombre_alu, apellido_alu, email_alu, telefono_alu, direccion_alu, fecha_nacimiento) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssssssss", $username, $dni, $nombre, $apellido, $email, $telefono, $direccion, $fecha_nacimiento);

        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Alumno creado exitosamente.'); window.location.href='gestionUsers.php';</script>";
        } else {
            echo "<script>alert('Error: " . mysqli_stmt_error($stmt) . "');</script>";
        }

        mysqli_stmt_close($stmt);
    }
} catch(Exception $e) {
    echo "Error: ". $e;
    exit();
}

mysqli_close($conexion);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.min.css" integrity="sha256-qWVM38RAVYHA4W8TAlDdszO1hRaAq0ME7y2e9aab354=" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/form.css">
    <title>Document</title>
</head>
<body>
<form method="post">
    <label>DNI: <input type="text" id="dni" name="dni"></label>
    <span id="errorDNI" class="error"></span>
    <label>Username: <input type="text" id="username" name="username"></label>
    <span id="errorUsername" class="error"></span>
    <label>Nombre: <input type="text" id="nombre" name="nombre"></label>
    <span id="errorNombre" class="error"></span>
    <label>Apellido: <input type="text" id="apellido" name="apellido" placeholder="Ex: Perez"></label>
    <span id="errorApellido" class="error"></span>
    <label>Email: <input type="email" id="email" name="email" placeholder="example@gmail.com"></label>
    <span id="errorEmail" class="error"></span>
    <label>Teléfono: <input type="text" id="telefono" name="telefono" placeholder="Ex: +34 629183402"></label>
    <span id="errorTelefono" class="error"></span>
    <label>Dirección: <input type="text" id="direccion" name="direccion" placeholder="Ex: Calle 123, Madrid"></label>
    <span id="errorDireccion" class="error"></span>
    <label>Fecha de Nacimiento: <input type="date" id="fecha" name="fecha_nacimiento"></label>
    <span id="errorDia" class="error"></span><br>
    <div class="button-group">
        <input type="submit" id="boton" value="Crear Alumno" disabled>
        <button type="button" class="btn btn-danger" onclick="window.location.href='gestionUsers.php'">VOLVER</button>
    </div>
</form> 
<script src="../js/verifAlu.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js" integrity="sha256-1m4qVbsdcSU19tulVTbeQReg0BjZiW6yGffnlr/NJu4=" crossorigin="anonymous"></script>
</body>
</html>