{{-- Create this file: resources/views/emails/new-job-application.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Job Application</title>
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
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .job-details {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #3b82f6;
        }
        .freelancer-info {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            background-color: #3b82f6;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        .highlight {
            background-color: #fef3c7;
            padding: 2px 6px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎉 New Job Application Received!</h1>
        <p>Someone has applied for your job posting</p>
    </div>

    <div class="content">
        <p>Hello <strong>{{ $client->name ?? 'Client' }}</strong>,</p>

        <p>Great news! You've received a new application for your job posting. Here are the details:</p>

        <div class="job-details">
            <h3>📋 Job Details</h3>
            <p><strong>Title:</strong> {{ $job->title }}</p>
            <p><strong>Budget:</strong> 
                @if($job->budget_min && $job->budget_max)
                    ₱{{ number_format($job->budget_min, 2) }} - ₱{{ number_format($job->budget_max, 2) }}
                @elseif($job->budget_min)
                    From ₱{{ number_format($job->budget_min, 2) }}
                @elseif($job->budget_max)
                    Up to ₱{{ number_format($job->budget_max, 2) }}
                @else
                    Budget not specified
                @endif
            </p>
            <p><strong>Type:</strong> {{ ucfirst($job->type ?? 'Not specified') }}</p>
            <p><strong>Posted:</strong> {{ $job->created_at ? $job->created_at->format('M d, Y') : 'Not specified' }}</p>
        </div>

        <div class="freelancer-info">
            <h3>👤 Freelancer Information</h3>
            <p><strong>Name:</strong> {{ $freelancer->user->name ?? 'Not specified' }}</p>
            <p><strong>Email:</strong> {{ $freelancer->user->email ?? 'Not specified' }}</p>
            
            @if($freelancer->experience_level)
                <p><strong>Experience Level:</strong> <span class="highlight">{{ ucfirst($freelancer->experience_level) }}</span></p>
            @endif

            @if($freelancer->skills && $freelancer->skills->count() > 0)
                <p><strong>Skills:</strong> 
                    @foreach($freelancer->skills->take(5) as $skill)
                        <span class="highlight">{{ $skill->name }}</span>{{ !$loop->last ? ', ' : '' }}
                    @endforeach
                    @if($freelancer->skills->count() > 5)
                        <span class="highlight">+{{ $freelancer->skills->count() - 5 }} more</span>
                    @endif
                </p>
            @endif

            <h4>📝 Proposal Details</h4>
            <p><strong>Proposed Rate:</strong> ₱{{ number_format($application->proposed_rate, 2) }}</p>
            <p><strong>Estimated Duration:</strong> {{ $application->estimated_duration }}</p>
            
            <h4>💬 Cover Letter</h4>
            <div style="background-color: #f8f9fa; padding: 15px; border-radius: 6px; margin: 10px 0;">
                {!! nl2br(e($application->cover_letter)) !!}
            </div>

            @if($application->attachments && count($application->attachments) > 0)
                <h4>📎 Attachments</h4>
                <p>The freelancer has included {{ count($application->attachments) }} attachment(s) with their application.</p>
            @endif
        </div>

        <div style="text-align: center;">
            <p><strong>Ready to review this application?</strong></p>
            <a href="{{ config('app.url') }}/client/applications/{{ $application->id }}" class="button">
                Review Application
            </a>
        </div>

        <div style="background-color: #e0f2fe; padding: 15px; border-radius: 6px; margin: 20px 0;">
            <p><strong>💡 Next Steps:</strong></p>
            <ul>
                <li>Review the freelancer's profile and proposal</li>
                <li>Check their portfolio and previous work</li>
                <li>Contact them directly if you have questions</li>
                <li>Accept or decline the application</li>
            </ul>
        </div>
    </div>

    <div class="footer">
        <p>This email was sent from <strong>KONEK Freelance Platform</strong></p>
        <p>You're receiving this because you posted a job on our platform.</p>
        <p>If you have any questions, please contact our support team.</p>
    </div>
</body>
</html>