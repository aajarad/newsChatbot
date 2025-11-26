# Chatbot News Site (Laravel)

This application provides the user-facing chatbot UI for the News Chatbot Platform. It renders the conversation interface, validates questions, and forwards them to the Python FastAPI service responsible for AI responses.

The Laravel app expects the FastAPI backend to be available at `PYTHON_CHATBOT_URL` (default `http://127.0.0.1:8001/ask`) and secured with `PYTHON_API_KEY`.

---

## Prerequisites

- PHP 8.2+
- Composer 2+
- Node.js 20+ (optional, for asset builds)

---

## Setup

1. Install PHP dependencies:

   ```bash
   composer install
   ```

2. Copy the environment template and update values as needed:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   Key values to set:

   ```env
   APP_URL=http://127.0.0.1:8000
   PYTHON_CHATBOT_URL=http://127.0.0.1:8001/ask
   PYTHON_API_KEY=laravel_secret_key_12345
   ```

3. (Optional) install and build front-end assets:

   ```bash
   npm install
   npm run build   # or npm run dev for Vite dev server
   ```

---

## Running locally

Start the development server:

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

Ensure the FastAPI service is running separately (see repository root README for instructions). Once both services are active, navigate to `http://127.0.0.1:8000/chatbot`.

---

## Key routes

| Method | Route           | Description                                   |
|--------|-----------------|-----------------------------------------------|
| GET    | `/chatbot`      | Render the chatbot UI                         |
| POST   | `/ask-chatbot`  | Validate question and proxy to FastAPI backend |

GET requests to `/ask-chatbot` will redirect to `/chatbot`.

---

## Troubleshooting

- **MethodNotAllowed for `/ask-chatbot`:** ensure requests are POSTed via the UI. Direct GET navigation is redirected to the chatbot page.
- **Timeout contacting FastAPI:** verify the Python service is running on the host/port specified in `PYTHON_CHATBOT_URL` and that the API key matches `PYTHON_API_KEY`.
- **CSRF token issues:** confirm the `<meta name="csrf-token">` tag is present in the rendered view. The bundled `chatbot.blade.php` already includes it.

---

## Tests

Run automated tests with:

```bash
php artisan test
```

Add new tests in `tests/Feature` to cover additional chatbot behaviors.

---

## License

Adopt the same license as the root project (update this section if a different license applies).
