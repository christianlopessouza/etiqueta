<?php

namespace Src\Server\Services;

use Src\Assets\CURL;
use Src\Assets\HandlerException;
use Src\Server\Helpers\ZebraHelper;
use Src\Server\Helpers\ServerHelper;

class ZebraService
{
    public static function pdfMerge($files)
    {
        try {
            // Cria um novo objeto Imagick
            $imagick = new \Imagick();

            foreach ($files as $file) {
                $pdf = new \Imagick();
                $pdf->setResolution(160, 160); // Define a resolução (DPI) para reduzir o tamanho do arquivo
                $pdf->readImage($file);

                // Reduzir a qualidade das imagens para cada página do PDF
                foreach ($pdf as $page) {
                    $page->setImageFormat('pdf');
                    $page->setImageCompression(\Imagick::COMPRESSION_ZIP); // Usa compressão ZIP
                    $page->setImageCompressionQuality(100); // Qualidade de compressão agressiva
                    $page->stripImage(); // Remove metadados
                }

                // Adiciona as páginas ao objeto principal
                $imagick->addImage($pdf);
            }

            // Define o formato de saída como PDF
            $imagick->setImageFormat('pdf');

            // Combina todas as páginas em um único documento PDF
            $combinedPDF = tempnam(sys_get_temp_dir(), 'pdf_');
            $imagick->writeImages($combinedPDF, true);

            // Limpa a memória
            $imagick->clear();
            $imagick->destroy();
        } catch (\Exception $e) {
            return false;
        }

        return $combinedPDF;
    }

    public static function reader($file_path, $width = 4, $height = 2, $image_converter = true)
    {
        $file_content = file_get_contents($file_path);

        $pdf_files = [];

        $zpl_files = self::fileTreatment($file_content);

        foreach ($zpl_files as $content)
            $pdf_files[] = self::zebraConverter($content, $width, $height);

        $tmp_pdf_path = (count($pdf_files) > 1) ? self::pdfMerge($pdf_files) : array_shift($pdf_files);

        $fate_path = ZebraHelper::basename();

        rename($tmp_pdf_path, $fate_path['internal']);

        chmod($fate_path['internal'], 0644);

        if ($image_converter) {
            $image_preview = self::imagePreview($fate_path['internal']);
        }

        return ['pdf' => $fate_path['public'], 'img' => $image_preview['public']];
    }

    private static function imagePreview($path)
    {
        $imagick = new \Imagick();
        $imagick->setResolution(300, 300); // Define a resolução
        $imagick->readImage($path); // Lê a primeira página do PDF
        $imagick->setImageFormat('png');

        $image_name = uniqid('img_', true) . '.png';
        $imagePath = __DIR__ . '/../../../archive/img/' . $image_name;
        $publicImagePath = sprintf('%s/%s', ServerHelper::hostname(), 'archive/img/' . $image_name);

        $imagick->writeImage($imagePath);

        return ['internal' => $imagePath, 'public' => $publicImagePath];
    }

    private static function fileTreatment($content)
    {
        $chunks = [];

        $label_limit = 50;

        $matches = [];

        // Usar expressão regular para encontrar todos os blocos entre ^XA e ^XZ
        preg_match_all('/(\^XA.*?\^XZ)/s', $content, $matches);

        $label_list = $matches[0];

        $const_variables = [];

        foreach ($label_list as $key => $label) {
            if (strpos($label, '~DG') !== false) {
                $const_variables[] = $label;
                unset($label_list[$key]);
            }
        }

        $label_list = array_values($label_list);

        $total_labels_counter = count($const_variables);

        foreach ($label_list as $label) {
            do {
                unset($new_quantity_label, $repeat_quantity_matches);

                $label_counter = (preg_match_all('/\^PQ(\d+)/', $label, $repeat_quantity_matches)) ?  $repeat_quantity_matches[1][0] : 1;

                $current_chunck_quantity = $total_labels_counter % $label_limit;

                $index = floor($total_labels_counter / 50);

                if ($current_chunck_quantity + $label_counter > $label_limit) {
                    $fixed_quantity_value = $label_limit - $current_chunck_quantity;
                    $total_labels_counter += $fixed_quantity_value;
                    $chunks[$index][] = preg_replace('/\^PQ(\d+)/', '^PQ' . $fixed_quantity_value, $label, 1);

                    $new_quantity_label = $label_counter - $fixed_quantity_value;
                    $label = preg_replace('/\^PQ(\d+)/', '^PQ' . $new_quantity_label, $label, 1);
                    $total_labels_counter += count($const_variables);
                } else {
                    $total_labels_counter += $label_counter;
                    $chunks[$index][] = $label;
                }
            } while (!!$new_quantity_label);
        }

        foreach ($chunks as $chunck_index => $chuck) {
            $chunks[$chunck_index] = array_merge($const_variables, $chuck);
        }


        $files = [];
        // Salvar cada lote em um arquivo separado
        foreach ($chunks as $index => $chunk) {
            $chunk_content = implode("\n", $chunk);
            $tmp_file_path = tempnam(sys_get_temp_dir(), 'zpl_');
            if (file_put_contents($tmp_file_path, $chunk_content)) {
                $files[] = $tmp_file_path;
            }
        }

        return $files;
    }

    private static function zebraConverter($file_path, $width, $height)
    {
        $width = $width == '' ? 4 : $width;
        $height = $height == '' ? 2 : $height;

        $file_content = file_get_contents($file_path);
        $request = new CURL('POST');
        $request->endpoint("http://api.labelary.com/v1/printers/8dpmm/labels/$width" . "x" . $height);
        $request->headers(["Accept: application/pdf", "Content-Type: application/x-www-form-urlencoded"]);
        $request->body($file_content);
        $request->send();

        if ($request->getHttpCode() == 200) {
            $tmp_file_path = tempnam(sys_get_temp_dir(), 'pdf_');
            file_put_contents($tmp_file_path, $request->response);
            return $tmp_file_path;
        }

        throw new HandlerException(2, 'Error processing');
    }
}
