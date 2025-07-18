<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Application {{ ucfirst($status) }} - {{ $job->title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #eee;
        }
        .status-accepted { color: #10b981; }
        .status-rejected { color: #6b7280; }
        .status-shortlisted { color: #3b82f6; }
        .job-details {
            background-color: #f9fafb;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            @if($status === 'accepted')
                <h1 class="status-accepted">🎉 Congratulations!</h1>
                <h2>Your Application Has Been Accepted!</h2>
            @elseif($status === 'shortlisted')
                <h1 class="status-shortlisted">⭐ Great News!</h1>
                <h2>You've Been Shortlisted!</h2>
            @else
                <h1 class="status-rejected">📋 Application Update</h1>
                <h2>Regarding Your Job Application</h2>
            @endif
        </div>
        
        <div class="content">
            <p>Hello <strong>{{ $freelancer->name }}</strong>,</p>
            
            @if($status === 'accepted')
                <p>Excellent news! The client has accepted your application and wants to work with you on this project.</p>
            @elseif($status === 'shortlisted')
                <p>Congratulations! Your application has caught the client's attention and you've been shortlisted for this project.</p>
            @else
                <p>Thank you for your interest in this project. After careful consideration, the client has decided to move forward with a different freelancer for this particular job.</p>
            @endif
            
            <div class="job-details">
                <h3>Job Details:</h3>
                <p><strong>Title:</strong> {{ $job->title }}</p>
                <p><strong>Client:</strong> {{ $client->name }}</p>
                <p><strong>Your Proposed Rate:</strong> ₱{{ number_format($application->proposed_rate, 2) }}</p>
                @if($application->accepted_at)
                <p><strong>Accepted On:</strong> {{ $application->accepted_at->format('F j, Y \a\t g:i A') }}</p>
                @elseif($application->reviewed_at)
                <p><strong>Reviewed On:</strong> {{ $application->reviewed_at->format('F j, Y \a\t g:i A') }}</p>
                @endif
            </div>
            
            @if($status === 'accepted')
                <p><strong>Next Steps:</strong></p>
                <ul>
                    <li>Start working on the project according to the requirements</li>
                    <li>Communicate regularly with the client for updates</li>
                    <li>Submit your completed work through the platform</li>
                </ul>
                <p>Good luck with your project!</p>
            @elseif($status === 'shortlisted')
                <p><strong>What to Expect:</strong></p>
                <ul>
                    <li>The client may contact you with additional questions</li>
                    <li>A final decision will be made soon</li>
                    <li>Keep your profile updated and be ready to respond</li>
                </ul>
                <p>We're rooting for you!</p>
            @else
                <p><strong>Don't get discouraged!</strong> Keep applying to other projects and improving your profile. The right opportunity is out there!</p>
            @endif
        </div>
        
        <div class="footer">
            <p>Best regards,<br>
            <strong>KONEK Team</strong><br>
            Central Mindanao University Freelance Platform</p>
            
            <p style="font-size: 12px; margin-top: 15px;">
                This is an automated message. Please do not reply to this email.
            </p>
        </div>
    </div>
</body>
</html>