<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $title }}</title>
    <style>
        :root {
            color-scheme: light;
            font-family: Inter, "Segoe UI", Roboto, Arial, sans-serif;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            color: #102033;
            background: #fff;
        }
        .report {
            width: min(1120px, calc(100% - 48px));
            margin: 32px auto 40px;
        }
        .report__header { margin-bottom: 24px; }
        .report__title {
            margin: 0;
            font-size: 28px;
            line-height: 1.15;
        }
        .report__subtitle {
            margin: 10px 0 0;
            color: #587089;
            font-size: 14px;
        }
        .report__meta {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            margin: 18px 0 24px;
            font-size: 13px;
            color: #42576d;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #cfd8e3;
            padding: 10px 12px;
            text-align: left;
            vertical-align: top;
            font-size: 13px;
        }
        th {
            background: #eef4fb;
            font-weight: 700;
        }
        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
            .report {
                width: 100%;
                margin: 0;
            }
        }
    </style>
</head>
<body>
<main class="report">
    <header class="report__header">
        <h1 class="report__title">{{ $title }}</h1>
        <p class="report__subtitle">{{ $subtitle }}</p>
    </header>
    <section class="report__meta">
        <div>{{ $summary }}</div>
        <div>Generated at: {{ $generatedAt }}</div>
    </section>
    <table>
        <thead>
        <tr>
            @foreach ($columns as $column)
                <th>{{ $column }}</th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @forelse ($rows as $row)
            <tr>
                @foreach ($row as $cell)
                    <td>{{ $cell }}</td>
                @endforeach
            </tr>
        @empty
            <tr>
                <td colspan="{{ max(count($columns), 1) }}">No data</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</main>
<script>
    window.addEventListener('load', () => {
        window.focus();
        window.print();
    });
</script>
</body>
</html>
