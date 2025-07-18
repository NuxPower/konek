<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication Code</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 40px 30px;
            text-align: center;
        }
        .greeting {
            font-size: 18px;
            color: #333;
            margin-bottom: 20px;
        }
        .message {
            font-size: 16px;
            color: #555;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .code-container {
            background-color: #f8f9fa;
            border: 2px dashed #667eea;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
        }
        .code {
            font-size: 36px;
            font-weight: bold;
            color: #667eea;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
        }
        .code-label {
            font-size: 14px;
            color: #888;
            margin-bottom: 10px;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
            font-size: 14px;
            color: #856404;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #666;
            border-top: 1px solid #e9ecef;
        }
        .security-tips {
            text-align: left;
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .security-tips h3 {
            color: #333;
            margin-top: 0;
        }
        .security-tips ul {
            color: #555;
            padding-left: 20px;
        }
        .security-tips li {
            margin: 8px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Two-Factor Authentication</h1>
        </div>
        
        <div class="content">
            <div class="greeting">
                Hello {{ $userName }}!
            </div>
            
            <div class="message">
                You requested to log in to your account. For your security, please use the verification code below to complete your login.
            </div>
            
            <div class="code-container">
                <div class="code-label">Your verification code is:</div>
                <div class="code">{{ $code }}</div>
            </div>
            
            <div class="warning">
                ⚠️ <strong>Important:</strong> This code will expire in 5 minutes and can only be used once. You have 3 attempts to enter the correct code.
            </div>
            
            <div class="security-tips">
                <h3>Security Tips:</h3>
                <ul>
                    <li>Never share this code with anyone</li>
                    <li>If you didn't request this code, please ignore this email</li>
                    <li>Make sure you're on the official login page</li>
                    <li>Contact support if you suspect any suspicious activity</li>
                </ul>
            </div>
        </div>
        
        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>If you have any questions, please contact our support team.</p>
            <p>&copy; {{ date('Y') }} Your Application Name. All rights reserved.</p>
        </div>
    </div>
</body>
</html>