
<!-- resources/views/emails/application-rejected.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Application Update - {{ $job->title }}</title>
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
            background-color: #6b7280;
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
        .status-badge {
            background-color: #6b7280;
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
            border-left: 4px solid #6b7280;
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
        .encouragement {
            background-color: #eff6ff;
            border: 1px solid #3b82f6;
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📋 Application Update</h1>
        <h2>Regarding Your Job Application</h2>
    </div>
    
    <div class="content">
        <p>Hello <strong>{{ $freelancer->name }}</strong>,</p>
        
        <p>Thank you for your interest in this project. After careful consideration, the client has decided to move forward with a different freelancer for this particular job.</p>
        
        <div class="status-badge">📝 Application Not Selected</div>
        
        <div class="job-details">
            <h3>Job Details:</h3>
            <p><strong>Title:</strong> {{ $job->title }}</p>
            <p><strong>Client:</strong> {{ $client->name }}</p>
            <p><strong>Your Proposed Rate:</strong> ₱{{ number_format($application->proposed_rate, 2) }}</p>
            <p><strong>Applied On:</strong> {{ $application->created_at->format('F j, Y') }}</p>
            <p><strong>Reviewed On:</strong> {{ $application->reviewed_at->format('F j, Y') }}</p>
        </div>
        
        <div class="encouragement">
            <h4>💪 Don't Get Discouraged!</h4>
            <p>Not being selected for this project doesn't reflect on your skills or abilities. Competition on freelance platforms is tough, and clients often have specific preferences or requirements.</p>
        </div>
        
        <p><strong>Tips for Future Applications:</strong></p>
        <ul>
            <li>Customize your cover letter for each job</li>
            <li>Highlight relevant experience and skills</li>
            <li>Provide competitive but fair pricing</li>
            <li>Build a strong portfolio and profile</li>
            <li>Respond quickly to job postings</li>
        </ul>
        
        <div style="text-align: center;">
            <a href="{{ route('freelancer.jobs.browse') }}" class="cta-button">
                Browse More Jobs
            </a>
        </div>
        
        <p>Keep applying and improving your profile. The right opportunity is out there waiting for you!</p>
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