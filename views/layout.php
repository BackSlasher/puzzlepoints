<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'PuzzlePoints' ?></title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
        }
        .nav {
            text-align: center;
            margin-bottom: 30px;
        }
        .nav a {
            margin: 0 15px;
            text-decoration: none;
            color: #0066cc;
            font-weight: 500;
        }
        .nav a:hover {
            text-decoration: underline;
        }
        .error {
            background: #fee;
            border: 1px solid #fcc;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            color: #c00;
        }
        .success {
            background: #efe;
            border: 1px solid #cfc;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            color: #060;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        input[type="text"], textarea, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        textarea {
            height: 150px;
            resize: vertical;
        }
        button {
            background: #0066cc;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #0052a3;
        }
        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .results-table th, .results-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .results-table th {
            background: #f5f5f5;
            font-weight: 500;
        }
        .filters {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .filters h3 {
            margin-top: 0;
        }
        .filter-row {
            display: flex;
            gap: 15px;
            align-items: end;
        }
        .filter-row > div {
            flex: 1;
        }
        .game-body {
            font-family: monospace;
            white-space: pre-line;
            background: #f5f5f5;
            padding: 10px;
            border-radius: 5px;
            font-size: 14px;
        }
        .timestamp {
            color: #666;
        }
        .footer {
            margin-top: 50px;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
        }
        .footer a {
            color: #0066cc;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
    </style>
    <script>
        // Convert UTC timestamps to user's local timezone
        function convertTimestamps() {
            const timestamps = document.querySelectorAll('[data-timestamp]');
            timestamps.forEach(function(element) {
                const utcTime = element.getAttribute('data-timestamp');
                const localDate = new Date(utcTime + ' UTC');
                element.textContent = localDate.toLocaleString([], {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true
                });
            });
        }

        // Run on page load
        document.addEventListener('DOMContentLoaded', convertTimestamps);
    </script>
</head>
<body>
    <div class="header">
        <h1>🎯 PuzzlePoints</h1>
    </div>
    <nav class="nav">
        <a href="/input">Submit Results</a>
        <a href="/puzzles">Puzzles</a>
        <a href="/results">All Results</a>
    </nav>
    <main>
        <?= $content ?>
    </main>
    <footer class="footer">
        By <a href="https://backslasher.net">(\) Backslasher</a> |
        <a href="https://github.com/BackSlasher/puzzlepoints">Contribute on GitHub</a>
    </footer>
</body>
</html>