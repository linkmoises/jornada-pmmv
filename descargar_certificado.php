<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero_identificacion = $_POST['numero_identificacion'];
    $encontrado = false;

    // Lee los datos del archivo CSV
    $archivo_csv = 'datos.csv';
    if (($handle = fopen($archivo_csv, 'r')) !== false) {
        while (($fila = fgetcsv($handle)) !== false) {
            if ($fila[1] === $numero_identificacion) { // Suponiendo que el número de identificación está en la segunda columna
                $nombre = $fila[0]; // Suponiendo que el nombre está en la primera columna
                $encontrado = true;
                break;
            }
        }
        fclose($handle);
    }

    if ($encontrado) {
        // Cargar la biblioteca FPDI y FPDF
        require_once('fpdf/fpdf.php');
        require_once('fpdi/autoload.php'); 

        // Cambia la codificación del nombre de UTF-8 a uno soportado por FPDF
        $nombre = iconv('UTF-8', 'windows-1252', $nombre);

        // Configura el modelo de certificado 
        $certificado_template = 'modelo_certificado.pdf';

        // Crear una instancia de FPDI
        $pdf = new \setasign\Fpdi\Fpdi();

        // Agrega la página del modelo de certificado
        $pdf->addPage('L', 'Letter');
        $pdf->setSourceFile($certificado_template);
        $tplId = $pdf->importPage(1);
        $pdf->useTemplate($tplId);

        // Personaliza la fuente, tamaño, color y posición para $nombre
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->SetTextColor(0, 0, 0); // Color de texto (negro)
        $pdf->SetXY(91, 78); // Posición para el nombre (ajustar según diseño)
        $pdf->Cell(0, 10, $nombre, 0, 1, 'L');

        // Personaliza la fuente, tamaño, color y posición para $numero_identificacion
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetTextColor(0, 0, 0); // Color de texto (negro)
        $pdf->SetXY(127, 92); // Posición para el número de identificación (ajusta según tu diseño)
        $pdf->Cell(0, 10, $numero_identificacion, 0, 1, 'L');

        // Nombre del archivo para descargar
        $archivo_descarga = 'certificado_jornada_pmmv_2023.pdf';

        // Salida del PDF para descarga
        $pdf->Output($archivo_descarga, 'D');
    } else {
        echo '<p style="color:white;font-size:36px;">No se encontró un nombre para el número de identificación proporcionado.</p>';
    }
}
?>
