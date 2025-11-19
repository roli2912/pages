<!doctype html>
<html lang="ro">
<head>
    <meta charset="utf-8">
    <title>Înscrieri — Crossword</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <style>
        .wrap{max-width:1100px;margin:24px auto;padding:16px}
        .toolbar{display:flex;gap:10px;align-items:center;margin-bottom:12px}
        .toolbar input[type="text"]{padding:8px 10px;border:1px solid #d1d5db;border-radius:8px}
        .btn{padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;background:#fff;font-weight:700;cursor:pointer;text-decoration:none}
        table{width:100%;border-collapse:collapse;background:#fff}
        th,td{padding:10px 12px;border-bottom:1px solid #e5e7eb;text-align:left}
        th{background:#f8fafc;font-weight:700}
        .muted{color:#6b7280}
    </style>
</head>
<body>
<div class="wrap">
    <h1>Înscrieri corecte</h1>

    <table>
        <thead>
        <tr>
            <th>#</th>
            <th>Nume</th>
            <th>Email</th>
            <th>Creat la</th>
        </tr>
        </thead>
        <tbody>
        @forelse($entries as $e)
            <tr>
                <td>{{ $e->id }}</td>
                <td>{{ $e->name }}</td>
                <td>{{ $e->email }}</td>
                <td class="muted">{{ $e->created_at }}</td>
            </tr>
        @empty
            <tr><td colspan="4">Nu există înregistrări.</td></tr>
        @endforelse
        </tbody>
    </table>

    <div style="margin-top:12px">
        {{ $entries->appends(['q'=>$q ?? null, 'token'=>request('token')])->links() }}
    </div>
</div>
</body>
</html>
