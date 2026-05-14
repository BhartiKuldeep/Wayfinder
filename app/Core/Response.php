<?php

declare(strict_types=1);

namespace App\Core;

class Response
{
    public static function json(array $payload, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public static function ok(array $data = [], string $message = 'OK'): never
    {
        self::json(['ok' => true, 'message' => $message, 'data' => $data]);
    }

    public static function error(string $message, int $status = 400, array $errors = []): never
    {
        self::json(['ok' => false, 'message' => $message, 'errors' => $errors], $status);
    }

    public static function downloadCsv(string $filename, array $headers, array $rows): never
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $out = fopen('php://output', 'w');
        fputcsv($out, $headers);
        foreach ($rows as $row) {
            fputcsv($out, array_map(fn ($header) => $row[$header] ?? '', $headers));
        }
        fclose($out);
        exit;
    }
}
