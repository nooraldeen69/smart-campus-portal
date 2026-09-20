<?php
/**
 * Mock university back-end for LOCAL DEVELOPMENT AND DEMOS ONLY.
 * Simulates: Identity Provider (OAuth2), SIS, LMS and Library APIs.
 *
 * Run (separate terminal, plain PHP - NOT Laravel, so it never blocks the portal):
 *     php -S 127.0.0.1:8001 mock-server/router.php
 *
 * Optional: MOCK_SIS_DELAY_MS=2500 php -S ... to simulate a slow legacy SIS (tests OBJ-2 / RSK-01).
 * Never deploy this. It has no real authentication.
 */

const CLIENT_SECRET = 'local-dev-secret';

$users = [
    '20260001' => [
        'name'    => 'Layla Hassan',
        'email'   => 'layla.hassan@student.example.edu',
        'grades'  => [
            ['course_name' => 'Database Systems', 'score' => 'A'],
            ['course_name' => 'IT Project Management', 'score' => 'A-'],
            ['course_name' => 'Computer Networks', 'score' => 'B+'],
        ],
        'courses' => [
            ['course_code' => 'IT301', 'title' => 'Database Systems', 'lms_link' => 'http://127.0.0.1:8001/lms/course/IT301'],
            ['course_code' => 'IT340', 'title' => 'IT Project Management', 'lms_link' => 'http://127.0.0.1:8001/lms/course/IT340'],
        ],
        'loans'   => [
            ['title' => 'PMBOK Guide, 5th Edition', 'due_date' => '2026-10-04'],
        ],
    ],
    '20260002' => [
        'name'    => 'Omar Khalid',
        'email'   => 'omar.khalid@student.example.edu',
        'grades'  => [
            ['course_name' => 'Operating Systems', 'score' => 'B'],
            ['course_name' => 'Web Engineering', 'score' => 'A'],
        ],
        'courses' => [
            ['course_code' => 'IT315', 'title' => 'Operating Systems', 'lms_link' => 'http://127.0.0.1:8001/lms/course/IT315'],
        ],
        'loans'   => [],
    ],
];

function json_out($data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
}

function local_url_ok(string $url): bool
{
    $host = parse_url($url, PHP_URL_HOST);
    return in_array($host, ['localhost', '127.0.0.1'], true);
}

$path   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// ---------------- Identity Provider ----------------
if ($path === '/idp/authorize' && $method === 'GET') {
    $redirect = $_GET['redirect_uri'] ?? '';
    $state    = $_GET['state'] ?? '';
    if (!local_url_ok($redirect)) {
        json_out(['error' => 'invalid_redirect_uri'], 400);
        return;
    }
    echo '<!doctype html><meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<title>Mock University SSO</title>';
    echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">';
    echo '<div class="container py-5" style="max-width:460px"><div class="card shadow-sm"><div class="card-body">';
    echo '<h1 class="h4">Mock University SSO</h1><p class="text-muted small">Demo only - pick an account to sign in as.</p>';
    foreach ($users as $id => $u) {
        $link = $redirect . (str_contains($redirect, '?') ? '&' : '?')
            . http_build_query(['code' => "demo-$id", 'state' => $state]);
        echo '<a class="btn btn-outline-primary w-100 mb-2 text-start" href="' . htmlspecialchars($link) . '">'
            . htmlspecialchars($u['name']) . ' <span class="text-muted">(' . $id . ')</span></a>';
    }
    $deny = $redirect . (str_contains($redirect, '?') ? '&' : '?') . http_build_query(['error' => 'access_denied', 'state' => $state]);
    echo '<a class="btn btn-link w-100" href="' . htmlspecialchars($deny) . '">Cancel</a>';
    echo '</div></div></div>';
    return;
}

if ($path === '/idp/token' && $method === 'POST') {
    $code = $_POST['code'] ?? '';
    if (($_POST['client_secret'] ?? '') !== CLIENT_SECRET || empty($_POST['code_verifier'])
        || !preg_match('/^demo-(\d+)$/', $code, $m) || !isset($users[$m[1]])) {
        json_out(['error' => 'invalid_grant'], 400);
        return;
    }
    json_out(['access_token' => 'mock-token-' . $m[1], 'token_type' => 'Bearer', 'expires_in' => 3600]);
    return;
}

if ($path === '/idp/userinfo' && $method === 'GET') {
    $auth = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (!preg_match('/^Bearer mock-token-(\d+)$/', $auth, $m) || !isset($users[$m[1]])) {
        json_out(['error' => 'invalid_token'], 401);
        return;
    }
    $u = $users[$m[1]];
    json_out(['sub' => $m[1], 'university_id' => $m[1], 'name' => $u['name'], 'email' => $u['email']]);
    return;
}

if ($path === '/idp/logout') {
    $back = $_GET['post_logout_redirect_uri'] ?? '';
    if (local_url_ok($back)) {
        header('Location: ' . $back);
    } else {
        echo 'Signed out of Mock SSO.';
    }
    return;
}

// ---------------- Legacy systems ----------------
if (preg_match('#^/(sis/grades|lms/courses|library/loans)/(\d+)$#', $path, $m) && $method === 'GET') {
    if ($m[1] === 'sis/grades' && ($delay = (int) getenv('MOCK_SIS_DELAY_MS')) > 0) {
        usleep($delay * 1000); // simulate a slow legacy SIS
    }
    if (!isset($users[$m[2]])) {
        json_out(['error' => 'not_found'], 404);
        return;
    }
    $key = ['sis/grades' => 'grades', 'lms/courses' => 'courses', 'library/loans' => 'loans'][$m[1]];
    json_out($users[$m[2]][$key]);
    return;
}

if (preg_match('#^/lms/course/([A-Z0-9]+)$#', $path, $m)) {
    echo "Mock LMS course page: {$m[1]}";
    return;
}

json_out(['error' => 'not_found'], 404);
