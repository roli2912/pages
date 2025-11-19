<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Participanți - Bază de Date</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
        }

        .stats {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .stat-box h3 {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }

        .stat-box .number {
            font-size: 32px;
            font-weight: bold;
            color: #e91e63;
        }

        .table-container {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background: #f8f9fa;
            font-weight: bold;
            color: #333;
            position: sticky;
            top: 0;
        }

        td {
            color: #666;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }

        .badge-yes {
            background: #d4edda;
            color: #155724;
        }

        .badge-no {
            background: #f8d7da;
            color: #721c24;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            padding: 20px;
            background: white;
        }

        .pagination a,
        .pagination span {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: #333;
        }

        .pagination a:hover {
            background: #e91e63;
            color: white;
            border-color: #e91e63;
        }

        .pagination .active {
            background: #e91e63;
            color: white;
            border-color: #e91e63;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #999;
        }

        @media (max-width: 768px) {
            .stats {
                flex-direction: column;
            }

            table {
                font-size: 12px;
            }

            th, td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Participanți</h1>

    <div class="table-container">
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Nume</th>
                <th>Prenume</th>
                <th>Email</th>
                <th>Telefon</th>
                <th>Raspuns</th>
                <th>Newsletter</th>
                <th>Saptamana</th>
                <th>Data</th>
            </tr>
            </thead>
            <tbody>
            @forelse($participants as $participant)
                <tr>
                    <td>{{ $participant->id }}</td>
                    <td>{{ $participant->first_name }}</td>
                    <td>{{ $participant->last_name }}</td>
                    <td>{{ $participant->email }}</td>
                    <td>{{ $participant->phone }}</td>
                    <td>{{ Str::limit($participant->creative_answer, 50) }}</td>
                    <td>
                                <span class="badge {{ $participant->newsletter_subscription ? 'badge-yes' : 'badge-no' }}">
                                    {{ $participant->newsletter_subscription ? 'Da' : 'Nu' }}
                                </span>
                    </td>
                    <td>{{ $participant->week_identifier }}</td>
                    <td>{{ $participant->created_at->format('d.m.Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="no-data">Nu există participanți înregistrați</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="pagination">
            {{ $participants->links() }}
        </div>
    </div>
</div>
</body>
</html>
