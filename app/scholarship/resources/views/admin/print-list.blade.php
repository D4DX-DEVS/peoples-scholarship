<!DOCTYPE html>
{{--
    Printable rendering of a whole table.

    The admin tables page ten rows at a time, so the browser never holds the
    full list and cannot print it. "Print all" opens this instead, which is
    rebuilt server-side from the same search and filters the table has
    applied, and prints itself on load.
--}}
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Peoples Scholar' }}</title>
    <style>
        body  { font-family: "Source Sans Pro", Helvetica, Arial, sans-serif; margin: 24px; color: #111; }
        h1    { font-size: 18px; margin: 0 0 4px; }
        .meta { font-size: 12px; color: #555; margin-bottom: 16px; }
        table { border-collapse: collapse; width: 100%; font-size: 12px; }
        th, td{ border: 1px solid #999; padding: 4px 6px; text-align: left; vertical-align: top; }
        th    { background: #eee; }
        tr    { page-break-inside: avoid; }
        thead { display: table-header-group; }
        @media print { body { margin: 0; } }
    </style>
</head>
<body>
    <h1>{{ $title ?? 'Peoples Scholar' }}</h1>
    <div class="meta">
        {{ count($rows) }} {{ \Illuminate\Support\Str::plural('record', count($rows)) }}
        @if(!empty($filterSummary)) &mdash; {{ $filterSummary }} @endif
    </div>

    <table>
        <thead>
            <tr>@foreach($headings as $heading)<th>{{ $heading }}</th>@endforeach</tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
                <tr>@foreach((array) $row as $cell)<td>{{ $cell }}</td>@endforeach</tr>
            @endforeach
        </tbody>
    </table>

    <script>window.onload = function () { window.print(); };</script>
</body>
</html>
