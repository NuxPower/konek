# Submission Draft

Hello, this is Yohan Lukin for the Backend Developer CHALLENGE.

What I did in the workflow:
I integrated n8n with KONEK, a Laravel student marketplace, to audit real job
listings. Administrators can request an audit through an n8n form, while KONEK
can also trigger the same workflow automatically when a job is created. The
workflow reads the job from KONEK's MariaDB database, calculates a quality
score, routes the result, and appends an audit record to Google Sheets.

What third-party integrations I used:
I used the native MySQL node with a SELECT-only account to read KONEK's
existing jobs, users, and categories. I used the native Google Sheets node for
the administrative audit log.

What condition filters were applied and why:
I reject invalid job IDs before querying the database. I then check whether the
job exists, whether it is published, and whether its quality score meets the
threshold. The result is classified as Invalid Request, Job Not Found, Not
Published, Needs Review, or Passed.

I used any custom code: yes
I used a database: yes
I used Google Sheets integration: yes

Explanation of how I approached the problem:
I kept Laravel as the only writer to KONEK's marketplace records and gave n8n
read-only database access. The custom code normalizes both form and webhook
payloads and applies deterministic checks to the listing's title, description,
requirements, budget, deadline, and possible off-platform contact details.
Google Sheets acts as an operational review queue rather than a second source
of truth. KONEK's job creation remains successful even if n8n is unavailable.

Thank you,
Yohan Lukin
[your WhatsApp phone number]

