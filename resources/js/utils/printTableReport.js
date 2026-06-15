const escapeHtml = (value) => String(value ?? '')
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#39;')

export const printTableReport = ({
    title,
    subtitle = '',
    columns = [],
    rows = [],
    summary = '',
    printedAt = '',
    emptyText = 'No data',
}) => {
    const reportWindow = window.open('', '_blank', 'width=1200,height=900')

    if (!reportWindow) {
        return false
    }

    const tableHead = columns
        .map((column) => `<th>${escapeHtml(column)}</th>`)
        .join('')

    const tableBody = rows.length
        ? rows
            .map((row) => `
                <tr>
                    ${row.map((cell) => `<td>${escapeHtml(cell)}</td>`).join('')}
                </tr>
            `)
            .join('')
        : `<tr><td colspan="${Math.max(columns.length, 1)}">${escapeHtml(emptyText)}</td></tr>`

    reportWindow.document.write(`
        <!DOCTYPE html>
        <html lang="en">
            <head>
                <meta charset="utf-8" />
                <title>${escapeHtml(title)}</title>
                <style>
                    :root {
                        color-scheme: light;
                        font-family: Inter, "Segoe UI", Roboto, Arial, sans-serif;
                    }
                    * { box-sizing: border-box; }
                    body {
                        margin: 0;
                        color: #102033;
                        background: #ffffff;
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
                        <h1 class="report__title">${escapeHtml(title)}</h1>
                        ${subtitle ? `<p class="report__subtitle">${escapeHtml(subtitle)}</p>` : ''}
                    </header>
                    <section class="report__meta">
                        ${summary ? `<div>${escapeHtml(summary)}</div>` : '<div></div>'}
                        ${printedAt ? `<div>${escapeHtml(printedAt)}</div>` : ''}
                    </section>
                    <table>
                        <thead>
                            <tr>${tableHead}</tr>
                        </thead>
                        <tbody>${tableBody}</tbody>
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
    `)

    reportWindow.document.close()

    return true
}
