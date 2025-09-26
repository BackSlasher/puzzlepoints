<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'PuzzlePoints' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #4b5563;
            --primary-light: #6b7280;
            --primary-dark: #374151;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
        }

        body {
            background: linear-gradient(135deg, #1f2937 0%, #374151 50%, #4b5563 100%);
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            margin-top: 2rem;
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #4b5563, #6b7280);
            color: white;
            text-align: center;
            padding: 3rem 2rem 2rem;
            margin: 0;
            border-radius: 0;
        }

        .header h1 {
            font-size: 3rem;
            font-weight: 700;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        .nav {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
            padding: 1rem 0;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .nav .btn {
            margin: 0 0.5rem;
            border-radius: 25px;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
            border: 2px solid rgba(255, 255, 255, 0.4);
            background: rgba(255, 255, 255, 0.9);
            color: #374151;
            font-weight: 500;
        }

        .nav .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            background: rgba(255, 255, 255, 1);
            border: 2px solid rgba(255, 255, 255, 0.6);
            color: #374151;
        }

        .main-content {
            padding: 2rem;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            border: none;
            border-radius: 25px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(99, 102, 241, 0.4);
        }
        .game-body {
            font-family: 'SF Mono', Monaco, 'Cascadia Code', 'Roboto Mono', Consolas, 'Courier New', monospace;
            white-space: pre-line;
            background: linear-gradient(145deg, #f8f9fa, #e9ecef);
            padding: 1rem;
            border-radius: 10px;
            font-size: 14px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .timestamp {
            color: #6c757d;
            font-weight: 500;
        }

        .table {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .table th {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
            font-weight: 600;
            border: none;
            padding: 1rem;
        }

        .table td {
            padding: 1rem;
            border-color: rgba(0, 0, 0, 0.05);
        }

        .alert {
            border: none;
            border-radius: 15px;
            padding: 1rem 1.5rem;
        }

        .footer {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(5px);
            padding: 2rem;
            text-align: center;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
            color: #374151;
        }

        .footer a {
            color: #374151;
            text-decoration: none;
            font-weight: 500;
            transition: opacity 0.3s ease;
        }

        .footer a:hover {
            opacity: 0.8;
            color: #374151;
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 2rem;
            }

            .nav .btn {
                margin: 0.25rem;
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container-fluid">
        <div class="container">
            <div class="header">
                <h1><i class="bi bi-bullseye"></i> PuzzlePoints</h1>
            </div>
            <nav class="nav">
                <a href="/input" class="btn btn-outline-light">
                    <i class="bi bi-plus-circle"></i> Submit Results
                </a>
                <a href="/puzzles" class="btn btn-outline-light">
                    <i class="bi bi-puzzle"></i> Puzzles
                </a>
                <a href="/results" class="btn btn-outline-light">
                    <i class="bi bi-list-ul"></i> All Results
                </a>
            </nav>
            <main class="main-content">
                <?= $content ?>
            </main>
            <footer class="footer">
                By <a href="https://backslasher.net"> (\) Backslasher</a> |
                <a href="https://github.com/BackSlasher/puzzlepoints"><i class="bi bi-github"></i> Contribute on GitHub</a>
            </footer>
        </div>
    </div>
</body>
</html>
