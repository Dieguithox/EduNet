<?php
include 'plantillaReporte.php'; // Asegúrate de tener esta plantilla de PDF
include "../Model/conexionBD.php"; // Asegúrate de tener la conexión a la BD

$conn = obtenerConexion(); // Conexión a la base de datos

$pdf = new PDF('P', 'mm', 'letter'); // Crear el objeto PDF
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFillColor(127, 179, 213); // Establecer color de fondo

// Configurar el título del reporte
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Historial de materiales y calificaciones del alumno', 0, 1, 'C');

// Establecer los encabezados de la tabla
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(9, 8, 'idM', 1, 0, 'C', true);
$pdf->Cell(18, 8, 'Categoria', 1, 0, 'C', true);
$pdf->Cell(80, 8, 'Titulo', 1, 0, 'C', true);
$pdf->Cell(35, 8, 'Fecha Subida', 1, 0, 'C', true);
$pdf->Cell(40, 8, 'Usuario', 1, 0, 'C', true);
$pdf->Cell(18, 8, 'Promedio', 1, 1, 'C', true);

// Consulta para obtener los datos del historial de materiales del alumno
$query = "SELECT 
        m.idM, 
        m.categoria, 
        m.titulo, 
        m.fechaSubida, 
        AVG(c.calificacion) AS promedio_calificacion, 
        u.usuario 
    FROM material m
    JOIN Calificacion c ON m.idM = c.materialId
    JOIN usuario u ON m.usuario_idU_M = u.idU
    WHERE u.idU = ?  -- Usamos un marcador de parámetro
    GROUP BY m.idM, u.usuario
    ORDER BY m.fechaSubida DESC
";

$usuarioId = 4; // Ejemplo de ID del alumno (ajustar según sea necesario)

$stmt = $conn->prepare($query);
if ($stmt === false) {
    die('Error al preparar la consulta: ' . $conn->error);
}

// Vinculamos el parámetro con el marcador de la consulta
$stmt->bind_param("i", $usuarioId); // El tipo "i" es para entero

$stmt->execute();
$result = $stmt->get_result();

// Configurar la fuente para los datos
$pdf->SetFont('Arial', '', 10);

// Iterar sobre los resultados y agregar los datos al PDF
while ($row = $result->fetch_assoc()) {
    $pdf->Cell(9, 8, $row['idM'], 1, 0, 'C');
    $pdf->Cell(18, 8, $row['categoria'], 1, 0, 'C');
    $pdf->Cell(80, 8, $row['titulo'], 1, 0, 'C');
    $pdf->Cell(35, 8, $row['fechaSubida'], 1, 0, 'C');
    $pdf->Cell(40, 8, $row['usuario'], 1, 0, 'C');
    $pdf->Cell(18, 8, number_format($row['promedio_calificacion'], 2), 1, 1, 'C'); // Promedio formateado con dos decimales
}

// Generar y mostrar el PDF
$pdf->Output('I', 'Historial_Materiales.pdf');
?>