<?php
require 'fpdf/fpdf.php';
    class PDF extends FPDF
    {
        function Header()
    {
        // Logo de EduNet
        $this->Image('../Views/img/LogoEduNet.png', 10, 8, 30); // Ajusta la ruta y dimensiones del logo
        $this->Ln(5); // Salto de línea antes del título

        // Título principal del reporte
        $this->SetFont('Arial', 'B', 25); // Fuente más grande
        $this->SetTextColor(6,7,7); // Gris oscuro
        $this->Cell(0, 10, 'EduNet', 0, 1, 'C');
        
        // Subtítulo o descripción opcional
        $this->SetFont('Arial', '', 15); // Fuente más pequeña para el subtítulo
        $this->SetTextColor(100, 100, 100); // Gris más claro
        $this->Cell(0, 10, 'Reporte de materiales educativos', 0, 1, 'C');
        
        // Línea decorativa
        $this->Ln(3); // Salto de linea de que se muestre la linea azul
        $this->SetDrawColor(28, 150, 199);
        $this->Line(10, $this->GetY(), 200, $this->GetY()); // Línea en la parte inferior del encabezado
        $this->Ln(5); // Espacio después de la línea
    }

    // Pie de página
    function Footer()
    {
        $this->SetY(-15); // Coloca el pie de página 15 mm antes del final
        $this->SetFont('Arial', 'I', 10); // Fuente pequeña
        $this->SetTextColor(128, 128, 128); // Gris claro
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo() . ' de {nb}', 0, 0, 'C'); // Número de página
    }
    }
?>