<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Application Shortlisted - {{ $job->title }}</title>
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
            background-color: #3b82f6;
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
        .shortlist-badge {
            background-color: #3b82f6;
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
            border-left: 4px solid #3b82f6;
        }
        .cta-button {
            background-color: #3b82f6;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            display: inline-block;
            margin: 20px 0;
            font-weight: bold;
        }
        .status-info {
            background-color: #dbeafe;
            border: 1px solid #3b82f6;
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>⭐ Great News!</h1>
        <h2>You've Been Shortlisted!</h2>
    </div>
    
    <div class="content">
        <p>Hello <strong>{{ $freelancer->name }}</strong>,</p>
        
        <p>Congratulations! Your application has caught the client's attention and you've been shortlisted for this project.</p>
        
        <div class="shortlist-badge">⭐ Shortlisted Candidate</div>
        
        <div class="job-details">
            <h3>Job Details:</h3>
            <p><strong>Title:</strong> {{ $job->title }}</p>
            <p><strong>Client:</strong> {{ $client->name }}</p>
            <p><strong>Your Proposed Rate:</strong> ₱{{ number_format($application->proposed_rate, 2) }}</p>
            <p><strong>Estimated Duration:</strong> {{ $application->estimated_duration ?? 'Not specified' }}</p>
            <p><strong>Shortlisted On:</strong> {{ $application->reviewed_at->format('F j, Y \a\t g:i A') }}</p>
        </div>
        
        <div class="status-info">
            <h4>📋 What Does This Mean?</h4>
            <p>Being shortlisted means you're among the top candidates the client is considering. The client is likely reviewing multiple applications and will make a final decision soon.</p>
        </div>
        
        <p><strong>What to Expect Next:</strong></p>
        <ul>
            <li>The client may contact you with additional questions</li>
            <li>You might be asked for more details about your approach</li>
            <li>The client will review all shortlisted candidates</li>
            <li>A final decision will be made within a few days</li>
        </ul>
        
        <div style="text-align: center;">
            <a href="{{ route('freelancer.applications.show', $application) }}" class="cta-button">
                View Application Status
            </a>
        </div>
        
        <p><strong>Tips While You Wait:</strong></p>
        <ul>
            <li>Be ready to respond quickly if the client contacts you</li>
            <li>Continue applying to other jobs (don't put all eggs in one basket)</li>
            <li>Keep your profile and portfolio updated</li>
            <li>Stay professional and patient</li>
        </ul>
        
        <p>Good luck! We're rooting for you to get this project.</p>
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