<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * "Print all" and spreadsheet export for the server-side admin tables.
 *
 * The tables only ever hold one page of rows in the browser, so neither can
 * be produced from the page. Both are rebuilt here against the same search
 * and filters the table is displaying, and the applied filters are printed
 * on the sheet so a partial list is not mistaken for the whole thing.
 */
trait ExportsTables
{
    /**
     * @param array<int, array> $columns column definitions; those without a
     *                                   path are action buttons and are left out
     * @param iterable          $rows    already-flattened row values
     */
    protected function exportResponse(Request $request, string $title, string $filename, array $columns, $rows)
    {
        $headings = collect($columns)
            ->filter(fn ($column) => ($column['path'] ?? null) !== null)
            ->pluck('title')
            ->values()
            ->all();

        if ($request->input('format') === 'csv') {
            return $this->streamCsv($filename, $headings, $rows);
        }

        return view('admin.print-list')
            ->with('title', $title)
            ->with('filterSummary', $this->filterSummary($request))
            ->with('headings', $headings)
            ->with('rows', $rows);
    }

    /**
     * Stream rows as CSV rather than building the whole file in memory.
     */
    protected function streamCsv(string $filename, array $headings, $rows)
    {
        return response()->streamDownload(function () use ($headings, $rows) {
            $out = fopen('php://output', 'w');
            // Byte-order mark, without which Excel mangles non-ASCII names.
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, $headings);

            foreach ($rows as $row) {
                fputcsv($out, (array) $row);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * Note of what was filtered, shown in the printed header.
     */
    protected function filterSummary(Request $request): string
    {
        $parts = [];

        foreach (['status', 'district', 'area', 'unit', 'category'] as $key) {
            if ($request->filled($key)) {
                $parts[] = ucfirst($key).': '.$request->input($key);
            }
        }

        $search = $request->input('search.value', $request->input('search'));

        if (is_string($search) && trim($search) !== '') {
            $parts[] = 'Search: "'.trim($search).'"';
        }

        return implode(', ', $parts);
    }
}
