import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import test from 'node:test';

const root = new URL('../', import.meta.url);
const workflow = JSON.parse(
  await readFile(new URL('workflow/konek-job-audit.json', root), 'utf8'),
);
const normalizeSource = await readFile(
  new URL('code/normalize-audit-request.js', root),
  'utf8',
);
const evaluateSource = await readFile(
  new URL('code/evaluate-job-quality.js', root),
  'utf8',
);

async function loadSample(name) {
  return JSON.parse(
    await readFile(new URL(`samples/${name}.json`, root), 'utf8'),
  );
}

function embeddedCode(name) {
  return workflow.nodes.find((node) => node.name === name).parameters.jsCode;
}

function executeNormalize(code, input) {
  const runner = new Function('items', code);
  return runner([{ json: input }])[0].json;
}

function executeEvaluation(code, job, request) {
  const n8nSelector = () => ({
    first: () => ({ json: request }),
  });
  const runner = new Function('items', '$', code);
  return runner([{ json: job }], n8nSelector)[0].json;
}

function withoutVolatileFields(value) {
  const copy = structuredClone(value);
  delete copy.requested_at;
  return copy;
}

test('workflow is an inactive credential-free template', () => {
  const serialized = JSON.stringify(workflow);
  assert.equal(workflow.active, false);
  assert.equal(serialized.includes('REPLACE_WITH_GOOGLE_SHEET_ID'), true);
  assert.equal(serialized.includes('"credentials"'), false);
});

test('workflow contains both triggers and every required native integration', () => {
  const types = new Set(workflow.nodes.map((node) => node.type));
  assert.equal(types.has('n8n-nodes-base.formTrigger'), true);
  assert.equal(types.has('n8n-nodes-base.webhook'), true);
  assert.equal(types.has('n8n-nodes-base.code'), true);
  assert.equal(types.has('n8n-nodes-base.mySql'), true);
  assert.equal(types.has('n8n-nodes-base.googleSheets'), true);
  assert.equal(types.has('n8n-nodes-base.if'), true);

  for (const name of [
    'Valid Job ID?',
    'Job Found?',
    'Job Published?',
    'Quality Passed?',
    'Prepare Invalid Request',
    'Prepare Job Not Found',
    'Prepare Not Published',
    'Prepare Passed Audit',
    'Prepare Needs Review',
  ]) {
    assert.ok(workflow.connections[name], `${name} must be connected`);
  }
});

test('database node is read-only against KONEK core tables', () => {
  const databaseNode = workflow.nodes.find((node) => node.name === 'Read KONEK Job');
  const query = databaseNode.parameters.query.trim().toUpperCase();

  assert.equal(databaseNode.parameters.operation, 'executeQuery');
  assert.equal(
    databaseNode.parameters.options.queryReplacement,
    '={{ [$json.job_id] }}',
  );
  assert.match(query, /WHERE JOBS\.ID = \$1/);
  assert.match(query, /^SELECT/);
  assert.doesNotMatch(query, /\b(INSERT|UPDATE|DELETE|DROP|ALTER|TRUNCATE)\b/);
  assert.match(query, /\bJOBS\b/);
  assert.match(query, /\bUSERS\b/);
  assert.match(query, /\bCATEGORIES\b/);
});

test('manual form request is normalized to a valid job audit', async () => {
  const output = executeNormalize(normalizeSource, await loadSample('manual-request'));
  assert.equal(output.job_id, 42);
  assert.equal(output.valid_job_id, true);
  assert.equal(output.request_source, 'Manual n8n form');
  assert.equal(output.requested_by, 'Yohan Lukin');
});

test('nested KONEK webhook payload is normalized correctly', async () => {
  const output = executeNormalize(normalizeSource, await loadSample('webhook-request'));
  assert.equal(output.job_id, 42);
  assert.equal(output.valid_job_id, true);
  assert.equal(output.request_source, 'KONEK webhook');
  assert.equal(output.requested_by, 'Sample Member');
  assert.equal(output.review_reason, 'Automatic audit after job creation');
});

test('invalid job IDs are rejected before the database node', () => {
  const output = executeNormalize(normalizeSource, {
    'KONEK job ID': 'not-a-number',
    'Requested by': 'Yohan Lukin',
  });
  assert.equal(output.job_id, null);
  assert.equal(output.valid_job_id, false);
  assert.match(output.request_error, /positive integer/);
});

test('complete published job passes the quality audit', async () => {
  const request = executeNormalize(
    normalizeSource,
    await loadSample('manual-request'),
  );
  const output = executeEvaluation(
    evaluateSource,
    await loadSample('quality-pass-job'),
    request,
  );

  assert.equal(output.job_found, true);
  assert.equal(output.job_status, 'published');
  assert.equal(output.quality_score, 100);
  assert.equal(output.quality_passed, true);
  assert.equal(output.audit_flags, 'No quality issues detected');
});

test('low-quality published job is routed for review', async () => {
  const request = executeNormalize(
    normalizeSource,
    await loadSample('manual-request'),
  );
  const output = executeEvaluation(
    evaluateSource,
    await loadSample('needs-review-job'),
    request,
  );

  assert.equal(output.job_found, true);
  assert.equal(output.quality_score, 0);
  assert.equal(output.quality_passed, false);
  assert.match(output.audit_flags, /Description needs more detail/);
  assert.match(output.audit_flags, /off-platform contact/);
});

test('missing database row becomes a job-not-found result', async () => {
  const request = executeNormalize(
    normalizeSource,
    await loadSample('manual-request'),
  );
  const output = executeEvaluation(evaluateSource, {}, request);

  assert.equal(output.job_found, false);
  assert.equal(output.quality_passed, false);
  assert.match(output.audit_flags, /No KONEK job matched/);
});

test('embedded n8n Code nodes match their maintained source copies', async () => {
  for (const name of ['manual-request', 'webhook-request']) {
    const input = await loadSample(name);
    const sourceOutput = executeNormalize(normalizeSource, input);
    const embeddedOutput = executeNormalize(
      embeddedCode('Normalize Audit Request'),
      input,
    );
    assert.deepEqual(
      withoutVolatileFields(embeddedOutput),
      withoutVolatileFields(sourceOutput),
    );
  }

  const request = executeNormalize(
    normalizeSource,
    await loadSample('manual-request'),
  );
  for (const name of [
    'quality-pass-job',
    'needs-review-job',
    'draft-job',
  ]) {
    const job = await loadSample(name);
    assert.deepEqual(
      executeEvaluation(embeddedCode('Evaluate Job Quality'), job, request),
      executeEvaluation(evaluateSource, job, request),
    );
  }
});
