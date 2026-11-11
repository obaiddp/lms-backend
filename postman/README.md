# LMS Backend Postman automation

Import `lms-backend.postman_collection.json` and `lms-backend.postman_environment.json` into Postman, or run the same suite with Newman:

```bash
BASE_URL=http://localhost ./scripts/run-postman.sh
```

The script downloads/runs Newman through `npx`, so Newman does not need to be added to the application dependencies.

Set the environment values for the four role accounts before running. The collection logs in as admin, organization, instructor, and student, stores their Sanctum tokens, and verifies role claims and cross-role access. It also checks public course discovery, student course access, cart/payment authentication and validation, and token revocation.

Course creation in this application is a web/session route rather than a JSON API. To run that request, set `web_session_cookie` and `web_csrf_token` to values obtained from an authenticated organization browser session. Without those values the request is explicitly skipped; no credentials or payment gateway data are committed to the repository.

The payment requests intentionally stop at authentication and input validation. Real gateway calls are not automated so the suite cannot create charges or require provider credentials.
