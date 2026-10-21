<?php
//ordenar el codigo klo

require_once __DIR__ . '/dompdf/autoload.inc.php';

//hacer uso de las clases necesarias
use Dompdf\Dompdf;
use Dompdf\Options;

//crear una instancia de Dompdf con opciones
$options = new Options();
$options->setChroot(__DIR__);

$dompdf = new Dompdf($options);

//cargar el contenido HTML del index.php
ob_start();
include "./index.php";
$html = ob_get_clean();

//agregar la imagen al contenido HTML
$rutaImagen = __DIR__ . "/iavoy.jpg";
$rutaImagen = str_replace("\\", "/", $rutaImagen);

$html .= "
    <h1>Una imagen</h1>
    <br>
    <img src='$rutaImagen' width='300'>
";


$dompdf->loadHtml($html);

$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$content = $dompdf->output();
//con esto queda guardado en la carpeta del proyecto
$nombreDelDocumento = "test.pdf";
$bytes = file_put_contents($nombreDelDocumento, $content);
//con el attachment en false se abre en el navegador
$dompdf->stream($nombreDelDocumento, [
    "Attachment" => false
]);

//salir del script para evitar que se ejecute el resto del index.php
exit;

