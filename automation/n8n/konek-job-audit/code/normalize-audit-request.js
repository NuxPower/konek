// Keep synchronized with the "Normalize Audit Request" n8n Code node.

return items.map((item) => {
  const envelope = item.json;
  const input = envelope.body && typeof envelope.body === 'object'
    ? { ...envelope, ...envelope.body }
    : envelope;

  const read = (...keys) => {
    for (const key of keys) {
      if (input[key] !== undefined && input[key] !== null) {
        return input[key];
      }
    }

    return '';
  };

  const rawJobId = read('KONEK job ID', 'job_id');
  const jobId = Number(rawJobId);
  const validJobId = Number.isInteger(jobId) && jobId > 0;
  const automatic = read('event') === 'job.created';

  return {
    json: {
      job_id: validJobId ? jobId : null,
      valid_job_id: validJobId,
      request_source: automatic ? 'KONEK webhook' : 'Manual n8n form',
      requested_by: String(
        read('Requested by', 'requested_by', 'posted_by') || 'KONEK automation',
      ).trim(),
      review_reason: String(
        read('Review reason', 'review_reason')
          || (automatic ? 'Automatic audit after job creation' : ''),
      ).trim(),
      job_url: String(read('url')).trim(),
      request_error: validJobId ? '' : 'KONEK job ID must be a positive integer',
      requested_at: new Date().toISOString(),
    },
  };
});

