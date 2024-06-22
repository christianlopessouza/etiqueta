<?

namespace Src\Server\Helpers;

class ZebraHelper
{
    public static function basename()
    {
        $pdf_name = 'archive/' . uniqid('label_', true) . '.pdf';
        $internal_dir = __DIR__ . '/../../..';
        $public_dir = ServerHelper::hostname();

        return  [
            'internal' => sprintf('%s/%s', $internal_dir, $pdf_name),
            'public' => sprintf('%s/%s', $public_dir, $pdf_name)
        ];
    }
}
