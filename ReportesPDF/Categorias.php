<?php 
include 'plantillaReporte.php';
include "../Model/conexionBD.php";

$conn = obtenerConexion(); // Establece la conexión

$pdf = new PDF('P', 'mm', 'letter');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFillColor(127, 179, 213);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Materiales por categoria', 0, 1, 'C');

// Encabezados de tabla
$pdf->Cell(60, 8, 'Categoria', 1, 0, 'C', true);
$pdf->Cell(60, 8, 'Total de materiales', 1, 0, 'C', true);
$pdf->Cell(60, 8, 'Promedio de calificaciones', 1, 1, 'C', true);

$query = "SELECT m.categoria, COUNT(DISTINCT m.idM) AS total_materiales, AVG(c.calificacion) AS promedio_calificacion 
FROM material m 
LEFT JOIN Calificacion c ON m.idM = c.materialId 
GROUP BY m.categoria 
ORDER BY total_materiales DESC;";

$result = mysqli_query($conn, $query);

$pdf->SetFont('Arial', '', 12);
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $pdf->Cell(60, 9, htmlspecialchars($row['categoria']), 1, 0, 'C'); // Categoría
        $pdf->Cell(60, 9, $row['total_materiales'], 1, 0, 'C'); // Total de materiales
        $pdf->Cell(60, 9, number_format($row['promedio_calificacion'], 2), 1, 1, 'C'); // Promedio
    }
}else{
    $pdf->Cell(0, 10, 'No se encontraron datos para mostrar', 1, 1, 'C');
}

$pdf->Output();
?>