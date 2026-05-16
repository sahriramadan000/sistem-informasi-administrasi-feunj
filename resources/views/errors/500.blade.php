<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error 500 - Internal Server Error</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .error-container {
            background: white;
            padding: 3rem;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            max-width: 600px;
            text-align: center;
        }
        .error-code {
            font-size: 4rem;
            color: #e74c3c;
            font-weight: bold;
            margin: 0;
        }
        .error-message {
            font-size: 1.5rem;
            color: #333;
            margin-top: 1rem;
        }
        .error-id {
            background: #f8f9fa;
            padding: 1rem;
            border-left: 4px solid #e74c3c;
            margin: 1.5rem 0;
            text-align: left;
            border-radius: 4px;
        }
        .error-id-label {
            font-size: 0.875rem;
            color: #666;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .error-id-value {
            font-family: 'Courier New', monospace;
            font-size: 1.1rem;
            color: #e74c3c;
            margin-top: 0.5rem;
            word-break: break-all;
        }
        .error-description {
            color: #666;
            font-size: 1rem;
            margin: 1.5rem 0;
            line-height: 1.6;
        }
        .btn-group-custom {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn-custom {
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            border-radius: 4px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .btn-home {
            background: #667eea;
            color: white;
            border: 2px solid #667eea;
        }
        .btn-home:hover {
            background: #764ba2;
            border-color: #764ba2;
            color: white;
        }
        .btn-back {
            background: transparent;
            color: #667eea;
            border: 2px solid #667eea;
        }
        .btn-back:hover {
            background: #667eea;
            color: white;
        }
        .contact-admin {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #eee;
            font-size: 0.95rem;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1 class="error-code">500</h1>
        <h2 class="error-message">Internal Server Error</h2>
        
        <p class="error-description">
            Something went wrong on our end. Our team has been notified and we're working to fix it.
            Please try again in a few moments.
        </p>

        @if ($error_id)
            <div class="error-id">
                <div class="error-id-label">Error Reference ID</div>
                <div class="error-id-value">{{ $error_id }}</div>
                <small style="color: #999; margin-top: 0.5rem; display: block;">
                    Please provide this ID if you contact support
                </small>
            </div>
        @endif

        <div class="btn-group-custom">
            <a href="{{ route('dashboard') }}" class="btn-custom btn-home">Go to Dashboard</a>
            <button onclick="history.back()" class="btn-custom btn-back">Go Back</button>
        </div>

        <div class="contact-admin">
            If you continue to experience issues, please contact the administrator with your Error Reference ID.
        </div>
    </div>
</body>
</html>
