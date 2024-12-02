<?php 
include 'plantillaReporte.php';
include "../Model/conexionBD.php";

$conn = obtenerConexion(); // Establece la conexión

$pdf = new PDF('P', 'mm', 'letter');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFillColor(127, 179, 213);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Top 10 materiales mejor calificados', 0, 1, 'C');
$pdf->Cell(8, 8, 'ID', 1, 0, 'C', true);
$pdf->Cell(20, 8, 'Categoria', 1, 0, 'C', true);
$pdf->Cell(80, 8, 'Titulo', 1, 0, 'C', true);
$pdf->Cell(35, 8, 'Fecha de subida', 1, 0, 'C', true);
$pdf->Cell(38, 8, 'Usuario', 1, 0, 'C', true);
$pdf->Cell(20, 8, 'Promedio', 1, 1, 'C', true);

// Consulta para el Top 10 materiales mejor calificados
$query = " SELECT m.idM, m.categoria, m.titulo, m.fechaSubida, AVG(c.calificacion) AS promedio_calificacion, u.usuario AS usuario FROM material m
    JOIN Calificacion c ON m.idM = c.materialId JOIN usuario u ON m.usuario_idU_M = u.idU GROUP BY m.idM, u.usuario 
    ORDER BY promedio_calificacion DESC LIMIT 10;";

$result = mysqli_query($conn, $query);

// Configuración del cuerpo del PDF
$pdf->SetFont('Arial', '', 12);

while ($row = mysqli_fetch_assoc($result)) {
    $pdf->Cell(8, 9, $row['idM'], 1, 0, 'C');
    $pdf->Cell(20, 9, $row['categoria'], 1, 0, 'C');
    $pdf->Cell(80, 9, $row['titulo'], 1, 0, 'C');
    $pdf->Cell(35, 9, $row['fechaSubida'], 1, 0, 'C');
    $pdf->Cell(38, 9, $row['usuario'], 1, 0, 'C');
    $pdf->Cell(20, 9, number_format($row['promedio_calificacion'], 2), 1, 1, 'C'); // Promedio formateado
}

$pdf->Output();
?>