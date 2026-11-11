# Course detail: API vs page comparison

## Summary

The **API** and the **course details page** use the **same enrollment/cart logic**. They differ only in **how the user is authenticated**.

| Aspect | Page (`Frontend\CourseController::courseDetails`) | API (`Api\Frontend\HomeController::courseDetailApi`) |
|--------|--------------------------------------------------|------------------------------------------------------|
| **Route** | `GET /course-details/{slug}` (web) | `GET /api/frontend/course/detail/{slug}` |
| **Auth** | `Auth::user()` → **web guard** (session cookie) | `auth('sanctum')->user()` → **Sanctum** (Bearer token) |
| **Middleware** | web (session) | `optional.auth` (checks Bearer, sets Sanctum user when valid) |

## Enrollment / cart logic (identical)

Both use the same logic:

1. **Default:** `course_exits = 0` (not enrolled, not in cart).
2. **If user present:**
   - **Live (course_type == 3):**  
     `Enrollment::where(['user_id', 'course_id', 'status' => ACCESS_PERIOD_ACTIVE])`  
     If any → `course_exits = 'enrolled'`.
   - **Self-learning (1 or 2):**  
     Same + `where('end_date', '>=', now())`.  
     If any → `course_exits = 'enrolled'`.
   - **Cart:**  
     `CartManagement::whereUserId()->whereCourseId()->where('created_by_type', 1)`  
     If exists → `course_exits = 'cartList'` (overwrites enrolled for CTA).

## API response (when authenticated)

The API returns the same semantics as the page uses for the CTA:

- `course_exits`: `'enrolled'` | `'cartList'` | `0`
- `auth_role`: user role or null
- `btn_text`: e.g. "Go to Course", "Buy Now", "Enroll Now"
- `btn_api_route`: URL or route for the button
- `course_learner_accessibility`: `'free'` | `'paid'` (or null)
- `already_requested_enrollment`: for live courses

So **at API level the backend is correctly sending the right stuff** when the request is authenticated with a valid **Bearer token**. If the academy sends no token or an invalid token, the API correctly returns guest data (`course_exits: 0`, `auth_role: null`).

## Why the page shows "Go to Course" but the academy shows "Buy Now"

- On **localhost:8000** you are logged in via **session** (web guard). The page uses `Auth::user()`, so it sees you and your enrollment and shows "Go to Course".
- The **academy** (Next.js) calls the **API** with the **cef_auth** cookie (Bearer token). If that token is missing or invalid for the API, the API sees no user and returns `course_exits: 0`, so the academy shows "Buy Now".

To get "Go to Course" on the academy, the same request must be authenticated with a **valid Sanctum token** (e.g. by logging in on the academy so it gets a token from the same backend).
