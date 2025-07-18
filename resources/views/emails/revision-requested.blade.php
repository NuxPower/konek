<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Revision Requested - {{ $job->title }}</title>
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
            background-color: #f59e0b;
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
        .revision-badge {
            background-color: #f59e0b;
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
            border-left: 4px solid #f59e0b;
        }
        .revision-notes {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
        }
        .cta-button {
            background-color: #4f46e5;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            display: inline-block;
            margin: 20px 0;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📝 Revision Request</h1>
        <h2>Your Client Has Requested Changes</h2>
    </div>
    
    <div class="content">
        <p>Hello <strong>{{ $freelancer->name }}</strong>,</p>
        
        <p>The client has reviewed your submitted work and is requesting some revisions before final approval.</p>
        
        <div class="revision-badge">🔄 Revision Requested</div>
        
        <div class="job-details">
            <h3>Job Details:</h3>
            <p><strong>Title:</strong> {{ $job->title }}</p>
            <p><strong>Client:</strong> {{ $client->name }}</p>
            <p><strong>Revision Requested:</strong> {{ $application->revision_requested_at->format('F j, Y \a\t g:i A') }}</p>
        </div>
        
        @if($application->revision_notes)
        <div class="revision-notes">
            <h4>📋 Client's Revision Notes:</h4>
            <p>{{ $application->revision_notes }}</p>
        </div>
        @endif
        
        <p>Please review the client's feedback carefully and make the necessary adjustments to your work. Once you've completed the revisions, you can resubmit your work through your dashboard.</p>
        
        <p><strong>Next Steps:</strong></p>
        <ul>
            <li>Log in to your KONEK dashboard</li>
            <li>Navigate to your active applications</li>
            <li>Update your submission with the requested changes</li>
            <li>Resubmit for client review</li>
        </ul>
        
        <p>Don't worry - revision requests are a normal part of the freelance process and help ensure the best possible outcome for both you and the client.</p>
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