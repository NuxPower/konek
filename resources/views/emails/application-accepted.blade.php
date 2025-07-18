<!-- resources/views/emails/application-accepted.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Application Accepted - {{ $job->title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #10b981;
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }
        .content {
            background-color: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
        }
        .footer {
            background-color: #374151;
            color: white;
            padding: 20px;
            border-radius: 0 0 8px 8px;
            text-align: center;
            font-size: 14px;
        }
        .success-badge {
            background-color: #10b981;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            display: inline-block;
            margin: 10px 0;
            font-weight: bold;
        }
        .job-details {
            background-color: white;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
            border-left: 4px solid #10b981;
        }
        .cta-button {
            background-color: #10b981;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            display: inline-block;
            margin: 20px 0;
            font-weight: bold;
        }
        .next-steps {
            background-color: #ecfdf5;
            border: 1px solid #10b981;
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎉 Congratulations!</h1>
        <h2>Your Application Has Been Accepted!</h2>
    </div>
    
    <div class="content">
        <p>Hello <strong>{{ $freelancer->name }}</strong>,</p>
        
        <p>Excellent news! The client has accepted your application and wants to work with you.</p>
        
        <div class="success-badge">✅ Application Accepted</div>
        
        <div class="job-details">
            <h3>Job Details:</h3>
            <p><strong>Title:</strong> {{ $job->title }}</p>
            <p><strong>Client:</strong> {{ $client->name }}</p>
            <p><strong>Your Proposed Rate:</strong> ₱{{ number_format($application->proposed_rate, 2) }}</p>
            <p><strong>Estimated Duration:</strong> {{ $application->estimated_duration ?? 'Not specified' }}</p>
            <p><strong>Accepted On:</strong> {{ $application->accepted_at->format('F j, Y \a\t g:i A') }}</p>
        </div>
        
        <div class="next-steps">
            <h4>🚀 Next Steps:</h4>
            <ul>
                <li>Start working on the project according to the requirements</li>
                <li>Communicate regularly with the client for updates</li>
                <li>Submit your completed work through the platform</li>
                <li>Wait for client approval and feedback</li>
            </ul>
        </div>
        
        <div style="text-align: center;">
            <a href="{{ route('freelancer.applications.show', $application) }}" class="cta-button">
                View Application Details
            </a>
        </div>
        
        <p><strong>Important Reminders:</strong></p>
        <ul>
            <li>Deliver high-quality work on time</li>
            <li>Ask questions if anything is unclear</li>
            <li>Keep all communication within the platform</li>
            <li>Submit your work through the completion system</li>
        </ul>
        
        <p>We're excited to see you succeed on this project. Good luck!</p>
    </div>
    
    <div class="footer">
        <p>Best regards,<br>
        <strong>KONEK Team</strong><br>
        Central Mindanao University Freelance Platform</p>
        
        <p style="font-size: 12px; margin-top: 15px;">
            This is an automated message. Please do not reply to this email.
        </p>
    </div>
</body>
</html>