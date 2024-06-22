<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
define('URL', $_SERVER['HTTP_ORIGIN']);

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);
$response = (object)[];


$uploadedFile = $_FILES['leiaute'];
$altura = $_POST['altura'] ? $_POST['altura'] : 2;
$largura = $_POST['largura'] ? $_POST['largura'] : 4;

$fileContent = file_get_contents($uploadedFile['tmp_name']);
$fileContent = str_replace('^XZ', '^PQ^XZ', $fileContent);
$fileContent = str_replace('^XA~TA000~JSN^LT0^MNW^MTD^PON^PMN^LH0^XZ', '', $fileContent);


try {
    // Supondo que request() seja uma função que processa o conteúdo do arquivo e retorna o caminho do PDF gerado
    $pdfFilePath = request($fileContent, $altura, $largura);
    $pdfName = uniqid('label_', true) . '.pdf';
    $secureFileName = __DIR__ . '/../process/tmp/' . $pdfName;
    rename($pdfFilePath, $secureFileName);

    // Usa o Imagick para converter o PDF em imagem
    $imagick = new Imagick();
    $imagick->setResolution(300, 300); // Define a resolução
    $imagick->readImage($secureFileName); // Lê a primeira página do PDF
    $imagick->setImageFormat('png');

    $page_counter = $imagick->getNumberImages();
    for ($i = 0; $i < $page_counter; $i++) {
        $imagick->setIteratorIndex($i); // Define o índice da página
        $imagick->setImageFormat('png'); // Define o formato da imagem

        $image_name = uniqid('img_', true) . '.png';
        $imagePath = __DIR__ . '/../process/tmp/img/' . $image_name;

        $imagick->writeImage($imagePath);

        chmod($secureFileName, 0644);

        $response->images[] = URL . '/process/tmp/img/' . $image_name;
    }

    $response->url = URL . '/process/tmp/' . $pdfName;
    $response->sucesso = true;

    // Libera a memória
    $imagick->clear();
    $imagick->destroy();
} catch (Exception $e) {
    echo $e->getMessage();
    $response->sucesso = false;
}


echo json_encode($response);


function request($zpl, $altura = 2, $largura = 4)
{
    $curl = curl_init();
    // Set cURL options
    curl_setopt($curl, CURLOPT_URL, "http://api.labelary.com/v1/printers/8dpmm/labels/$largura" . "x" . $altura . "/");
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $zpl);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, ["Accept: application/pdf"]);
    // Execute the cURL request
    $result = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    // Handle the response
    if ($httpCode == 200) {
        $tempFilePath = tempnam(sys_get_temp_dir(), 'pdf_');
        file_put_contents($tempFilePath, $result);
        // Close the cURL handle
        curl_close($curl);
        return $tempFilePath;
    } else {
        throw new Exception("Error: $result");
    }
}
