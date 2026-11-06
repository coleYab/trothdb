# CLI & LocalJudge Integration Guide

This guide explains how `localjudge` or any custom CLI tool connects with the learnCP platform using the **Device Authorization Flow** (RFC 8628), similar to `code --login`, `gh auth login`, and `stripe login`.

---

## 1. Overview of the Connection Flow

```
┌──────────────┐                                       ┌──────────────┐
│  LocalJudge  │                                       │   learnCP    │
│  CLI Client  │                                       │ Web & Judge  │
└──────┬───────┘                                       └──────┬───────┘
       │                                                      │
       │ 1. POST /api/judge/auth/request                      │
       │    { "client_name": "LocalJudge", "port": 54321 }    │
       ├─────────────────────────────────────────────────────►│
       │                                                      │
       │ 2. Response: user_code ("JUDGE-8492"),               │
       │    device_code, verification_url                     │
       │◄─────────────────────────────────────────────────────┤
       │                                                      │
       │ 3. CLI opens verification_url in default browser     │
       │    (or displays code for manual browser entry)       │
       │                                                      │
       │                          ┌───────────────────────────┤
       │                          │ 4. User logs in & grants  │
       │                          │    permission in browser  │
       │                          │    "Authorize LocalJudge" │
       │                          └─────────────┬─────────────┘
       │                                        │
       │ 5a. Direct loopback callback (instant) │
       │◄───────────────────────────────────────┘
       │     http://127.0.0.1:54321/callback?token=arena_live_...
       │
       │  -- OR --
       │
       │ 5b. Polling: POST /api/judge/auth/poll
       │     { "device_code": "..." }
       ├─────────────────────────────────────────────────────►│
       │◄─────────────────────────────────────────────────────┤
       │     { "status": "approved", "token": "arena_live_..." }
       │
       │ 6. CLI saves token locally to ~/.arena/credentials.json
       │    and authenticates future requests:
       │    Authorization: Bearer arena_live_...
       ▼
```

---

## 2. API Endpoints

Base URL: `http://localhost:8000` (or production domain)

### A. Initiate Device Login

Initiates an authorization session. LocalJudge can optionally start a temporary local HTTP server on an available localhost port and provide `callback_port` to receive an instant callback once approved.

- **Endpoint**: `POST /api/judge/auth/request`  
  _(Alias: `POST /api/cli/auth/device-code`)_
- **Headers**:
    ```http
    Content-Type: application/json
    Accept: application/json
    ```
- **Request Body** (optional):
    ```json
    {
        "client_name": "LocalJudge v1.0",
        "callback_port": 54321
    }
    ```
- **Response** (`200 OK`):
    ```json
    {
        "status": "pending",
        "device_code": "8e3b2e7960fc0fa7585de7da8a67...",
        "user_code": "JUDGE-8492",
        "verification_url": "http://localhost:8000/cli/authorize?code=JUDGE-8492&device_code=8e3b...&port=54321",
        "expires_in": 900,
        "interval": 2
    }
    ```

---

### B. User Authorization Page (Web)

- **URL**: `GET /cli/authorize?code=JUDGE-8492&device_code=...&port=...`
- **Behavior**:
    - If the user is unauthenticated, Laravel redirects them to `/login` and returns to `/cli/authorize` immediately upon signing in.
    - The web page shows:
        - Target account (username and email)
        - Confirmation code (`JUDGE-8492`)
        - Scope of permissions (read problems, run test suites, submit code)
        - Buttons: **"Authorize LocalJudge"** and **"Cancel & Deny"**
    - Clicking **Authorize**:
        - Issues a persistent token: `arena_live_<32-character string>`
        - Saves the session under `cli_tokens` linked to the user
        - If `port` was specified, pings the loopback server:  
          `http://127.0.0.1:<port>/callback?token=<token>&user=<username>`
        - Displays a green confirmation: _"LocalJudge Connected! You can now close this tab."_

---

### C. Poll for Token (CLI Fallback / Polling)

If loopback callback is not used or the port is blocked, the CLI polls this endpoint every `interval` seconds (default: 2 seconds) until approved or expired.

- **Endpoint**: `POST /api/judge/auth/poll`  
  _(Alias: `POST /api/cli/auth/poll`)_
- **Headers**:
    ```http
    Content-Type: application/json
    Accept: application/json
    ```
- **Request Body**:

    ```json
    {
        "device_code": "8e3b2e7960fc0fa7585de7da8a67..."
    }
    ```

- **Responses**:
    - **Pending** (`200 OK`):
        ```json
        {
            "status": "pending",
            "message": "Waiting for user authorization in browser...",
            "interval": 2
        }
        ```
    - **Approved** (`200 OK`):
        ```json
        {
            "status": "approved",
            "token": "arena_live_RL9GYy820p1k9aL7...",
            "user": {
                "id": 1,
                "name": "Jane Doe",
                "username": "janedoe",
                "email": "jane@example.com"
            },
            "message": "LocalJudge authorization successful."
        }
        ```
    - **Denied** (`403 Forbidden`):
        ```json
        {
            "status": "denied",
            "message": "User denied the authorization request in the browser."
        }
        ```
    - **Expired** (`410 Gone`):
        ```json
        {
            "status": "expired",
            "message": "Authorization request has expired or was not found."
        }
        ```

---

### D. Verify Token & Current User

- **Endpoint**: `GET /api/judge/whoami`
- **Headers**:
    ```http
    Authorization: Bearer arena_live_RL9GYy820p1k9aL7...
    Accept: application/json
    ```
- **Response** (`200 OK`):
    ```json
    {
        "authenticated": true,
        "user": {
            "id": 1,
            "name": "Jane Doe",
            "username": "janedoe",
            "email": "jane@example.com"
        }
    }
    ```

---

## 3. Submitting Problem Solutions

Once authenticated, LocalJudge can fetch problems and submit solutions.

### Fetch Problem Details

- **Endpoint**: `GET /api/problems/{problem_id}`
- **Headers**:
    ```http
    Authorization: Bearer arena_live_...
    Accept: application/json
    ```

### Create a Submission

- **Endpoint**: `POST /api/problems/{problem_id}/submissions`
- **Headers**:
    ```http
    Authorization: Bearer arena_live_...
    Content-Type: application/json
    Accept: application/json
    ```
- **Request Body**:
    ```json
    {
        "language": "cpp",
        "code": "#include <vector>\nusing namespace std;\nclass Solution {\npublic:\n    vector<int> twoSum(vector<int>& nums, int target) {\n        return {0, 1};\n    }\n};"
    }
    ```
- **Response** (`201 Created`):
    ```json
    {
        "message": "Solution submitted & accepted!",
        "is_solved": true,
        "submission": {
            "id": 14,
            "problem_id": "two-sum",
            "language": "cpp",
            "status": "accepted",
            "runtime_ms": 3,
            "memory_mb": 14.2,
            "test_results": [
                { "name": "Basic pair", "status": "passed", "runtime_ms": 2 }
            ],
            "stdout": "Passed all 3 test cases cleanly."
        }
    }
    ```

---

### Submit from CLI with Score (0-100) & Source Code File Upload

When `localjudge` runs test suites locally on the user's machine, it can submit the computed score (0-100) and upload the full source code file in one request.

- **Endpoints**:
  - `POST /api/judge/problems/{problem_id}/submit`
  - `POST /api/cli/problems/{problem_id}/submit`
  - `POST /api/judge/submit` _(with `problem` or `problem_id` in body)_
  - `POST /api/cli/submit` _(with `problem` or `problem_id` in body)_
- **Headers**:
    ```http
    Authorization: Bearer arena_live_...
    Accept: application/json
    ```
- **Payload Options**:
  - **Multipart Form Data (`multipart/form-data`)**:
    - `score` (required): Number between `0` and `100` (e.g. `100`, `85.5`, `0`).
    - `file` or `source_code`: Source code file (e.g. `solution.cpp`, `solution.py`, `solution.js`). File extension is automatically used to detect the language.
    - `runtime_ms` (optional): Integer runtime in milliseconds.
    - `memory_mb` (optional): Float peak memory usage in megabytes.
    - `test_results` (optional): JSON array of individual test verdicts.
    - `stdout` / `stderr` (optional): Runner output strings.
  - **JSON (`application/json`)**:
    - `score` (required): `0` to `100`.
    - `code` (required if no file): Solution source code string.
    - `language` (optional): `cpp`, `python`, `javascript`, etc.
    - `problem` or `problem_id` (required if not in URL).
- **Example cURL (File Upload)**:
    ```bash
    curl -X POST http://localhost:8000/api/judge/problems/two-sum/submit \
      -H "Authorization: Bearer arena_live_RL9GYy820p1k9aL7..." \
      -F "score=100" \
      -F "file=@solution.cpp" \
      -F "runtime_ms=3" \
      -F "memory_mb=14.2"
    ```
- **Example Response** (`201 Created`):
    ```json
    {
        "message": "CLI submission accepted and problem marked as solved!",
        "is_solved": true,
        "score": 100,
        "submission": {
            "id": 15,
            "user_id": 1,
            "problem_id": "two-sum",
            "language": "cpp",
            "score": 100,
            "status": "accepted",
            "source_file_name": "solution.cpp",
            "source_file_path": "submissions/kQ7zL0...cpp",
            "runtime_ms": 3,
            "memory_mb": 14.2,
            "test_results": null,
            "stdout": "All local judge tests passed.",
            "stderr": null,
            "created_at": "2026-11-05T09:55:00.000000Z"
        }
    }
    ```

---

## 4. Complete Client Implementation Examples

### Option A: Python CLI Example

```python
import os
import json
import time
import webbrowser
import requests
from http.server import HTTPServer, BaseHTTPRequestHandler
from urllib.parse import urlparse, parse_qs

BASE_URL = "http://localhost:8000"
CREDENTIALS_FILE = os.path.expanduser("~/.arena/credentials.json")


class CallbackHandler(BaseHTTPRequestHandler):
    token = None

    def do_GET(self):
        query = parse_qs(urlparse(self.path).query)
        if "token" in query:
            CallbackHandler.token = query["token"][0]
            self.send_response(200)
            self.send_header("Content-Type", "text/html")
            self.end_headers()
            self.wfile.write(b"<h1>Authenticated!</h1><p>You can close this tab.</p>")
        else:
            self.send_response(400)
            self.end_headers()

    def log_message(self, format, *args):
        return  # Suppress server logs


def login_with_judge():
    # 1. Start temporary loopback server
    server = HTTPServer(("127.0.0.1", 0), CallbackHandler)
    port = server.server_address[1]

    # 2. Initiate login request
    res = requests.post(
        f"{BASE_URL}/api/judge/auth/request",
        json={"client_name": "LocalJudge CLI", "callback_port": port},
    ).json()

    user_code = res["user_code"]
    device_code = res["device_code"]
    url = res["verification_url"]

    print(f"\nYour confirmation code is: {user_code}")
    print(f"Opening browser at: {url}\n")
    webbrowser.open(url)

    # 3. Wait for callback or poll fallback
    server.timeout = 2
    token = None
    start_time = time.time()

    while time.time() - start_time < 300:
        server.handle_request()
        if CallbackHandler.token:
            token = CallbackHandler.token
            break

        # Polling fallback
        poll = requests.post(
            f"{BASE_URL}/api/judge/auth/poll", json={"device_code": device_code}
        ).json()

        if poll.get("status") == "approved":
            token = poll["token"]
            break
        elif poll.get("status") == "denied":
            print("Authorization was denied by user.")
            return None

        time.sleep(1)

    if not token:
        print("Login timed out.")
        return None

    # 4. Save token
    os.makedirs(os.path.dirname(CREDENTIALS_FILE), exist_ok=True)
    with open(CREDENTIALS_FILE, "w") as f:
        json.dump({"token": token}, f)

    print("Successfully connected LocalJudge!")
    return token


if __name__ == "__main__":
    login_with_judge()
```

---

### Option B: Node.js / TypeScript Example

```typescript
import http from 'http';
import { exec } from 'child_process';

const BASE_URL = 'http://localhost:8000';

async function connectLocalJudge() {
    // 1. Create loopback server
    let tokenResolver: (token: string) => void;
    const tokenPromise = new Promise<string>((resolve) => {
        tokenResolver = resolve;
    });

    const server = http.createServer((req, res) => {
        const url = new URL(req.url || '', `http://localhost`);
        const token = url.searchParams.get('token');

        if (token) {
            res.writeHead(200, { 'Content-Type': 'text/html' });
            res.end(
                '<h3>LocalJudge Connected!</h3><p>You can close this window.</p>',
            );
            tokenResolver(token);
            server.close();
        }
    });

    await new Promise<void>((resolve) =>
        server.listen(0, '127.0.0.1', resolve),
    );
    const port = (server.address() as any).port;

    // 2. Request authorization
    const response = await fetch(`${BASE_URL}/api/judge/auth/request`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
        },
        body: JSON.stringify({
            client_name: 'LocalJudge TS',
            callback_port: port,
        }),
    });
    const data = await response.json();

    console.log(`\nConfirm Code: ${data.user_code}`);
    console.log(`Opening browser: ${data.verification_url}\n`);

    // Open default browser
    const startCmd =
        process.platform === 'win32'
            ? 'start'
            : process.platform === 'darwin'
              ? 'open'
              : 'xdg-open';
    exec(`${startCmd} "${data.verification_url}"`);

    // 3. Await loopback token or poll
    const token = await Promise.race([
        tokenPromise,
        (async () => {
            for (let i = 0; i < 300; i++) {
                await new Promise((r) => setTimeout(r, 2000));
                const poll = await fetch(`${BASE_URL}/api/judge/auth/poll`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                    },
                    body: JSON.stringify({ device_code: data.device_code }),
                });
                const pollData = await poll.json();
                if (pollData.status === 'approved') return pollData.token;
                if (pollData.status === 'denied')
                    throw new Error('Authorization denied');
            }
            throw new Error('Timeout');
        })(),
    ]);

    console.log('Token acquired:', token);
    return token;
}

connectLocalJudge().catch(console.error);
```

---

### Option C: Bash / cURL (Headless Mode)

If you are running in a headless SSH environment without a browser:

```bash
# 1. Request device code
AUTH_DATA=$(curl -s -X POST http://localhost:8000/api/judge/auth/request \
  -H "Content-Type: application/json" \
  -d '{"client_name": "Headless Judge"}')

USER_CODE=$(echo $AUTH_DATA | jq -r .user_code)
DEVICE_CODE=$(echo $AUTH_DATA | jq -r .device_code)
URL=$(echo $AUTH_DATA | jq -r .verification_url)

echo "Please visit: $URL"
echo "Confirm code: $USER_CODE"

# 2. Poll until approved
while true; do
  POLL_DATA=$(curl -s -X POST http://localhost:8000/api/judge/auth/poll \
    -H "Content-Type: application/json" \
    -d "{\"device_code\": \"$DEVICE_CODE\"}")

  STATUS=$(echo $POLL_DATA | jq -r .status)

  if [ "$STATUS" == "approved" ]; then
    TOKEN=$(echo $POLL_DATA | jq -r .token)
    echo "Connected successfully! Token: $TOKEN"
    break
  elif [ "$STATUS" == "denied" ]; then
    echo "Authorization denied."
    exit 1
  fi

  sleep 2
done
```

---

## 5. Session Management

- Active tokens are visible on the **Setup page** (`/setup`) under **"Active CLI sessions"**.
- Users can review connected devices, token prefixes, last used timestamps, and click **Revoke** at any time to instantly invalidate a session.
