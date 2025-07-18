<!-- resources/views/emails/job-completion-notification.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Job Completion Submitted - {{ $job->title }}</title>
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
            background-color: #059669;
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
        .completion-badge {
            background-color: #059669;
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
            border-left: 4px solid #059669;
        }
        .freelancer-details {
            background-color: white;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
            border-left: 4px solid #4f46e5;
        }
        .completion-message {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
        }
        .cta-button {
            background-color: #059669;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            display: inline-block;
            margin: 20px 0;
            font-weight: bold;
        }
        .warning-box {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
        }
        .attachments {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎯 Job Completion Submitted!</h1>
        <h2>Ready for Your Review</h2>
    </div>
    
    <div class="content">
        <p>Hello <strong>{{ $client->name }}</strong>,</p>
        
        <p>The freelancer has completed the work and submitted it for your review. Please take some time to examine the deliverables.</p>
        
        <div class="completion-badge">✅ Work Submitted for Review</div>
        
        <div class="job-details">
            <h3>Job Details:</h3>
            <p><strong>Title:</strong> {{ $job->title }}</p>
            <p><strong>Freelancer:</strong> {{ $freelancer->name }}</p>
            <p><strong>Submitted On:</strong> {{ $application->completed_at->format('F j, Y \a\t g:i A') }}</p>
            <p><strong>Original Budget:</strong> ${{ number_format($job->budget) }}</p>
            <p><strong>Agreed Rate:</strong> ${{ number_format($application->proposed_rate, 2) }}</p>
        </div>
        
        @if($application->completion_message)
        <div class="completion-message">
            <h4>📝 Freelancer's Completion Message:</h4>
            <p>{{ $application->completion_message }}</p>
        </div>
        @endif
        
        @if($application->completion_attachments && count($application->completion_attachments) > 0)
        <div class="attachments">
            <h4>📎 Completion Attachments ({{ count($application->completion_attachments) }} files):</h4>
            <ul>
                @foreach($application->completion_attachments as $attachment)
                <li>{{ $attachment['original_name'] ?? 'Attachment' }} 
                    ({{ number_format($attachment['size'] / 1024 / 1024, 2) }} MB)</li>
                @endforeach
            </ul>
            <p><small>You can download these files when you review the completion in your dashboard.</small></p>
        </div>
        @endif
        
        <div class="warning-box">
            <h4>⏰ Action Required</h4>
            <p>Please review the submitted work and take one of the following actions:</p>
            <ul>
                <li><strong>Approve:</strong> If you're satisfied with the work</li>
                <li><strong>Request Revisions:</strong> If changes are needed</li>
                <li><strong>Provide Feedback:</strong> Rate the freelancer's performance</li>
            </ul>
            <p>Timely feedback helps maintain good relationships with freelancers!</p>
        </div>
        
        <div style="text-align: center;">
            <a href="{{ route('client.applications.show', $application) }}" class="cta-button">
                Review Completion Now
            </a>
        </div>
        
        <p><strong>What happens next?</strong></p>
        <ul>
            <li>Log in to your dashboard to review the work</li>
            <li>Download and examine all submitted files</li>
            <li>Test any functionality if applicable</li>
            <li>Approve the work or request specific revisions</li>
            <li>Rate and provide feedback for the freelancer</li>
        </ul>
        
        <p>Remember: Clear communication and prompt feedback help build successful working relationships!</p>
    </div>
    
    <div class="footer">
        <p>Best regards,<br>
        <strong>KONEK Team</strong><br>
        Central Mindanao University Freelance Platform</p>
        
        <p style="font-size: 12px; margin-top: 15px;">
            This is an automated message. Please do not reply to this email.<br>
            For support, please contact us through your dashboard.
        </p>
    </div>
</body>
</html>