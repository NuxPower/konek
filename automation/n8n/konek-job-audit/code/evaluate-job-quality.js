// Keep synchronized with the "Evaluate Job Quality" n8n Code node.

const request = $('Normalize Audit Request').first().json;

return items.map((item) => {
  const job = item.json;
  const jobFound = Number.isInteger(Number(job.job_id)) && Number(job.job_id) > 0;

  if (!jobFound) {
    return {
      json: {
        ...request,
        job_found: false,
        title: '',
        posted_by: '',
        category: '',
        job_status: '',
        quality_score: 0,
        quality_passed: false,
        audit_flags: 'No KONEK job matched the supplied ID',
      },
    };
  }

  let score = 100;
  const flags = [];
  const penalize = (points, message) => {
    score -= points;
    flags.push(message);
  };

  const title = String(job.title || '').trim();
  const description = String(job.description || '').trim();
  const requirements = String(job.requirements || '').trim();
  const budgetMin = job.budget_min === null ? null : Number(job.budget_min);
  const budgetMax = job.budget_max === null ? null : Number(job.budget_max);
  const budgetType = String(job.budget_type || '');
  const deadline = job.deadline ? new Date(job.deadline) : null;
  const combinedText = `${description}\n${requirements}`;

  if (title.length < 10) penalize(15, 'Title is too short');
  if (description.length < 100) penalize(25, 'Description needs more detail');
  if (requirements.length < 50) penalize(20, 'Requirements need more detail');

  if (budgetType !== 'negotiable' && (budgetMin === null || budgetMax === null)) {
    penalize(20, 'Non-negotiable listing is missing a budget range');
  } else if (budgetMin !== null && budgetMax !== null && budgetMin > budgetMax) {
    penalize(30, 'Minimum budget exceeds maximum budget');
  }

  if (!deadline || Number.isNaN(deadline.getTime())) {
    penalize(10, 'No valid deadline was provided');
  } else {
    const remainingDays = (deadline.getTime() - Date.now()) / 86400000;
    if (remainingDays < 0) penalize(35, 'Deadline has already passed');
    else if (remainingDays < 3) penalize(15, 'Deadline is less than three days away');
  }

  const offPlatformContact = /[\w.+-]+@[\w.-]+\.[a-z]{2,}|\+?63\d{10}|(?:facebook|telegram|whatsapp)\s*[:@]/i;
  if (offPlatformContact.test(combinedText)) {
    penalize(25, 'Listing contains possible off-platform contact details');
  }

  score = Math.max(0, score);

  return {
    json: {
      ...request,
      job_found: true,
      title,
      posted_by: String(job.posted_by || ''),
      category: String(job.category || ''),
      job_status: String(job.status || ''),
      quality_score: score,
      quality_passed: score >= 70,
      audit_flags: flags.length ? flags.join('; ') : 'No quality issues detected',
      budget_min: budgetMin,
      budget_max: budgetMax,
      budget_type: budgetType,
      deadline: job.deadline || '',
    },
  };
});

