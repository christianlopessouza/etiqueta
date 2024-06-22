<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// run the security scan to find vulnerabilities

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize user input
    $uploadedFile = $_FILES['leiaute'];
    $altura = $_POST['altura'];
    $largura = $_POST['largura'];

    if (!is_uploaded_file($uploadedFile['tmp_name'])) {
        throw new Exception('Invalid file upload');
    }

    $fileContent = file_get_contents($uploadedFile['tmp_name']);
    $fileContent = str_replace('^XZ', '^PQ^XZ', $fileContent);

    try {
        // Supondo que request() seja uma função que processa o conteúdo do arquivo e retorna o caminho do PDF gerado
        $pdfFilePath = request($fileContent, $altura, $largura);
        $secureFileName = uniqid('label_', true) . '.pdf';
        rename($pdfFilePath, $secureFileName);

        // Usa o Imagick para converter o PDF em imagem
        $imagick = new Imagick();
        $imagick->setResolution(300, 300); // Define a resolução
        $imagick->readImage($secureFileName); // Lê a primeira página do PDF
        $imagick->setImageFormat('png');

        // Salva a imagem
        $imagePath = 'image.png';
        $imagick->writeImage($imagePath);

        chmod($secureFileName, 0644);

        // Libera a memória
        $imagick->clear();
        $imagick->destroy();
        $rand = time();
        echo "<img width=\"30%\" src=\"$imagePath?v=$rand\">";
        echo "<a href=\"$secureFileName\">Imprimir</a>";
    } catch (Exception $e) {
        echo $e->getMessage();
        http_response_code(500);
        echo 'An error occurred. Please try again later.';
    }
}



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

?>

<form method="post" enctype="multipart/form-data">
    <input type="file" name="leiaute">
    <div>
        <label for="altura">Altura (cm):</label>
        <input type="number" step="0.01" name="altura" value="0.32">
    </div>
    <div>
        <label for="largura">Largura (cm):</label>
        <input type="number" step="0.01" name="largura" value="3.15">
    </div>
    <button type="submit">Enviar</button>
</form>

<style>
    img {
        border: 1px solid black;
    }
</style>