<?php 
include 'plantillaReporte.php';
include "../Model/conexionBD.php";

$conn = obtenerConexion(); // Establece la conexión

$pdf = new PDF('P', 'mm', 'letter');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFillColor(127, 179, 213);

// Lista de Materiales Aprobados
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Materiales aprobados', 0, 1, 'C');
$pdf->Cell(10, 8, 'ID', 1, 0, 'C', true);
$pdf->Cell(23, 8, 'Categoria', 1, 0, 'C', true);
$pdf->Cell(90, 8, 'Titulo', 1, 0, 'C', true);
$pdf->Cell(38, 8, 'Fecha de subida', 1, 0, 'C', true);
$pdf->Cell(38, 8, 'Usuario', 1, 1, 'C', true);

$query1 = "
    SELECT m.idM, m.categoria, m.titulo, m.descripcion, m.fechaSubida, u.usuario AS usuario 
    FROM material m
    JOIN usuario u ON m.usuario_idU_M = u.idU 
    WHERE m.estado = 'aprobado';
";
$result1 = mysqli_query($conn, $query1);
$pdf->SetFont('Arial','',12);
$totalAprobados = 0; // Contador de materiales aprobados
while($row = mysqli_fetch_array($result1)) {
    $pdf->Cell(10,9,$row['idM'],1,0,'C');
    $pdf->Cell(23,9,$row['categoria'],1,0,'C');
    $pdf->Cell(90,9,$row['titulo'],1,0,'C');
    $pdf->Cell(38,9,$row['fechaSubida'],1,0,'C');
    $pdf->Cell(38,9,$row['usuario'],1,1,'C'); // Solo nombre del usuario
    $totalAprobados++;
}

$pdf->Ln(10); // Salto de línea

// Materiales Rechazados
$pdf->SetFont('Arial', 'B', 12); // Negrita para el título
$pdf->Cell(0, 10, 'Materiales rechazados', 0, 1, 'C');
$pdf->Cell(10, 8, 'ID', 1, 0, 'C', true);
$pdf->Cell(23, 8, 'Categoria', 1, 0, 'C', true);
$pdf->Cell(90, 8, 'Titulo', 1, 0, 'C', true);
$pdf->Cell(38, 8, 'Fecha de subida', 1, 0, 'C', true);
$pdf->Cell(38, 8, 'Usuario', 1, 1, 'C', true);

$query2 = "SELECT m.idM, m.categoria, m.titulo, m.descripcion, m.fechaSubida, u.usuario AS usuario 
    FROM material m
    JOIN usuario u ON m.usuario_idU_M = u.idU 
    WHERE m.estado = 'rechazado';
";
$result2 = mysqli_query($conn, $query2);
$pdf->SetFont('Arial', '', 12); // Texto normal
$totalRechazados = 0; // Contador de materiales rechazados
while($row = mysqli_fetch_array($result2)) {
    $pdf->Cell(10,9,$row['idM'],1,0,'C');
    $pdf->Cell(23,9,$row['categoria'],1,0,'C');
    $pdf->Cell(90,9,$row['titulo'],1,0,'C');
    $pdf->Cell(38,9,$row['fechaSubida'],1,0,'C');
    $pdf->Cell(38,9,$row['usuario'],1,1,'C'); // Solo nombre del usuario
    $totalRechazados++;
}

// Mostrar el total de materiales aprobados y rechazados
$pdf->Ln(10); // Salto de línea
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Total de materiales aprobados: ' . $totalAprobados, 0, 1, 'L');
$pdf->Cell(0, 10, 'Total de materiales rechazados: ' . $totalRechazados, 0, 1, 'L');

$pdf->Output();
?>