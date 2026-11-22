<?php

namespace App\Services;

use Smalot\PdfParser\Parser;

class KnowledgeDocumentExtractor
{
    public function extract(string $path, string $mimeType): string
    {
        if ($mimeType === 'application/pdf') {
            return $this->normalize((new Parser)->parseFile($path)->getText());
        }

        $archive = new \ZipArchive;

        if ($archive->open($path) !== true) {
            throw new \RuntimeException('The Word document could not be opened.');
        }

        $documentXml = $archive->getFromName('word/document.xml');
        $archive->close();

        if ($documentXml === false) {
            throw new \RuntimeException('The Word document does not contain readable text.');
        }

        return $this->normalize(strip_tags(str_replace('</w:p>', "\n", $documentXml)));
    }

    private function normalize(string $text): string
    {
        if (! mb_check_encoding($text, 'UTF-8')) {
            $text = mb_convert_encoding($text, 'UTF-8', 'Windows-1252');
        }

        return str_replace(["\xC2\xA0", "\r\n", "\r"], [' ', "\n", "\n"], $text);
    }
}
