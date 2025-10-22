<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Internal Server Error</title>
    <link rel="stylesheet" href="/themes/default/assets/css/theme.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .error-container {
            text-align: center;
            color: #fff;
            padding: 2rem;
        }
        .error-code {
            font-size: 8rem;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 1rem;
            text-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .error-message {
            font-size: 1.5rem;
            margin-bottom: 2rem;
        }
        .btn-home {
            background: #fff;
            color: #f5576c;
            padding: 1rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
            transition: all 0.3s ease;
        }
        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            color: #f5576c;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">500</div>
        <h1 class="error-message">Internal Server Error</h1>
        <p class="mb-4">Something went wrong on our end. We're working to fix it!</p>
        <a href="/" class="btn-home">
            <i class="ri-home-line me-2"></i> Back to Home
        </a>
    </div>
</body>
</html>
