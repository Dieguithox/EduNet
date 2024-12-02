<?php 
include 'plantillaReporte.php';
include "../Model/conexionBD.php";

$conn = obtenerConexion(); // Establece la conexión

$pdf = new PDF('P', 'mm', 'letter');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFillColor(127, 179, 213);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Reporte Semanal - Materiales Subidos', 0, 1, 'C');

$pdf->Cell(70, 8, 'Título', 1, 0, 'C', true);
$pdf->Cell(40, 8, 'Autor', 1, 0, 'C', true);
$pdf->Cell(30, 8, 'Fecha Subida', 1, 0, 'C', true);
$pdf->Cell(50, 8, 'Categoría', 1, 1, 'C', true);

$query = "SELECT m.titulo, u.usuario AS autor, m.fechaSubida, m.categoria FROM material m JOIN usuario u ON m.usuario_idU_M = u.idU WHERE m.fechaSubida >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) ORDER BY m.fechaSubida DESC;";

$result = mysqli_query($conn, $query);

$pdf->SetFont('Arial', '', 12);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $pdf->Cell(70, 9, htmlspecialchars($row['titulo']), 1, 0, 'C'); // Título
        $pdf->Cell(40, 9, htmlspecialchars($row['autor']), 1, 0, 'C'); // Autor
        $pdf->Cell(30, 9, htmlspecialchars($row['fechaSubida']), 1, 0, 'C'); // Fecha de subida
        $pdf->Cell(50, 9, htmlspecialchars($row['categoria']), 1, 1, 'C'); // Categoría
    }
} else {
    $pdf->Cell(0, 10, 'No se encontraron materiales subidos en la última semana', 1, 1, 'C');
}

$pdf->Output();
?>