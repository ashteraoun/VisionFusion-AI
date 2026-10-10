# VisionFusion AI

VisionFusion AI is a multimodal image-intelligence platform foundation with a Laravel web application and a Python FastAPI service. Step 2 adds a responsive image-upload interface and a temporary Laravel-to-FastAPI image validation flow.

**Current scope:** JPG, JPEG, PNG, and WEBP uploads are validated and their metadata is returned. No image is permanently stored. Object detection, OCR, image captioning, authentication, and analysis history are not implemented.

## Prerequisites

- Windows 10/11 and PowerShell
- PHP 8.2 or newer supported by Laravel 12, with `curl`, `fileinfo`, `mbstring`, `openssl`, `pdo_mysql`, and `xml` enabled
- Composer 2
- Python 3.10 or newer and pip
- Node.js and npm (Laravel Vite/Tailwind assets)
- MySQL, such as the service bundled with XAMPP
- Git

Check installed versions at the repository root:

```powershell
php --version
composer --version
python --version
python -m pip --version
node --version
npm --version
git --version
```

## Repository structure

```text
VisionFusion-AI/
├── docs/                   # Project documentation
├── laravel-app/            # Laravel 12 web application
├── python-ai/              # FastAPI service, Pillow image validation, pytest suite
│   ├── tests/
│   ├── main.py
│   └── requirements.txt
├── .gitignore
└── README.md
```

## Installation

Run commands from the repository root unless another directory is shown.

### Python service

```powershell
Set-Location .\python-ai
py -m venv .venv
.\.venv\Scripts\Activate.ps1
python -m pip install --upgrade pip
python -m pip install -r requirements.txt
Copy-Item .env.example .env
```

If PowerShell blocks activation, use `.\.venv\Scripts\python.exe` instead of `python`. Edit `python-ai\.env` to change `MAX_IMAGE_UPLOAD_MB` (default 10). `.env` is ignored by Git.

### Laravel application

Start MySQL in XAMPP and create a database named `visionfusion_ai` if you plan to use Laravel's database-backed features later. From a second terminal:

```powershell
Set-Location .\laravel-app
if (-not (Test-Path .env)) { Copy-Item .env.example .env }
composer install
php artisan key:generate
npm install
npm run build
```

Configure `laravel-app\.env` for your local environment. The image-upload page and AI request do not require a database connection.

## Configuration

Laravel reads these settings from `laravel-app\.env`:

| Variable | Default | Purpose |
| --- | --- | --- |
| `AI_SERVICE_URL` | `http://127.0.0.1:8000` | Base URL for the FastAPI service |
| `AI_SERVICE_ANALYZE_PATH` | `/api/v1/analyze-image` | Image-analysis API path |
| `AI_SERVICE_CONNECT_TIMEOUT` | `5` | Connection timeout in seconds |
| `AI_SERVICE_TIMEOUT` | `30` | Total request timeout in seconds |
| `MAX_IMAGE_UPLOAD_MB` | `10` | Laravel upload limit |

FastAPI reads `MAX_IMAGE_UPLOAD_MB` from its process environment. If the `.env` file has been created, the commands below load it with Uvicorn's `--env-file .env` option. Keep the Laravel and Python upload limits aligned.

## Run both applications

Open two PowerShell terminals in VS Code, both starting at the repository root.

**Terminal 1 - FastAPI:**

```powershell
Set-Location .\python-ai
.\.venv\Scripts\Activate.ps1
python -m uvicorn main:app --reload --host 127.0.0.1 --port 8000 --env-file .env
```

**Terminal 2 - Laravel:**

```powershell
Set-Location .\laravel-app
php artisan serve --host=127.0.0.1 --port=8001
```

Open the upload page at `http://127.0.0.1:8001/`. FastAPI's interactive API documentation is at `http://127.0.0.1:8000/docs`.

## API and health checks

- FastAPI health: `GET http://127.0.0.1:8000/health`
- Laravel health: `GET http://127.0.0.1:8001/health`
- Image validation: `POST http://127.0.0.1:8000/api/v1/analyze-image`
- Laravel browser upload: `POST http://127.0.0.1:8001/analyze-image`

The FastAPI endpoint accepts multipart/form-data with the field name `file`. It inspects decoded image content using Pillow, enforces the configured byte limit and a 40-megapixel dimension cap, then returns metadata without saving the image. Successful responses contain:

```json
{
  "success": true,
  "filename": "sample.png",
  "format": "PNG",
  "width": 640,
  "height": 480,
  "file_size_bytes": 18342,
  "message": "Image validated successfully. Image analysis is not enabled yet."
}
```

Invalid or unsupported images return HTTP 4xx responses; unexpected processing errors return a generic HTTP 500 response. Laravel validates the upload independently, forwards it as multipart data, and displays the returned metadata.

## Tests

From the repository root:

```powershell
Push-Location .\python-ai
.\.venv\Scripts\python.exe -m pytest
Pop-Location

Push-Location .\laravel-app
php artisan test
Pop-Location
```

Both suites generate or embed tiny test images; no user images are needed.

## Manual checks

Check both health endpoints:

```powershell
Invoke-RestMethod http://127.0.0.1:8000/health
Invoke-RestMethod http://127.0.0.1:8001/health
```

Test FastAPI directly with any local supported image:

```powershell
$form = @{ file = Get-Item .\sample.png }
Invoke-RestMethod -Uri http://127.0.0.1:8000/api/v1/analyze-image -Method Post -Form $form
```

To test the complete Laravel-to-Python flow, choose a JPG, PNG, or WEBP image from the browser upload page. The preview shows the selected name and size; after a successful response, the page displays its format, dimensions, and file size. Try an unsupported file, a corrupted image, or a file over the limit to see the error handling. To check service downtime, stop Uvicorn, submit a valid image, and confirm Laravel shows a temporary-unavailable message.

## Troubleshooting

- **Laravel upload reports a server error before validation:** PHP's `upload_max_filesize` and `post_max_size` must allow the multipart request. In XAMPP's `php.ini`, use values such as `upload_max_filesize=12M` and `post_max_size=12M` for a 10 MB application limit, then restart Apache or the PHP process.
- **FastAPI upload returns 422 or multipart parsing errors:** activate the `python-ai\.venv` environment and install `python-multipart` with `python -m pip install -r requirements.txt`.
- **Image validation dependencies are missing:** install Pillow with the same requirements command; Pillow verifies and decodes image data.
- **Laravel says the AI service is unavailable:** make sure Uvicorn is running, and check `AI_SERVICE_URL`, `AI_SERVICE_ANALYZE_PATH`, and the configured port in `laravel-app\.env`. Clear stale Laravel configuration with `php artisan config:clear`.
- **The upload page has no styling or JavaScript:** run `npm install` and `npm run build` in `laravel-app`.
- **Virtual environment activation is blocked:** run `Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass` in that terminal, or invoke `.\.venv\Scripts\python.exe` directly.
