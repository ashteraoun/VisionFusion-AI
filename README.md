# VisionFusion AI

VisionFusion AI is a multimodal image-intelligence platform foundation. The project is a monorepo with a Laravel web application and a separate Python FastAPI service. This setup is **Step 1 only**: it provides service health checks and the base structure; image uploads, object detection, OCR, authentication, dashboards, and vision-language features are not implemented yet.

## Prerequisites

- Windows 10/11 and PowerShell
- PHP 8.2 or newer in the Laravel 12 supported range, with `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `xml`, and `curl` enabled
- Composer 2
- Python 3.10 or newer (Python 3.13.1 was used to set up this repository)
- pip
- Node.js and npm (for Laravel's frontend assets)
- Git
- MySQL, such as the MySQL service bundled with XAMPP

Check installed versions from the repository root in PowerShell:

```powershell
php --version
composer --version
python --version
python -m pip --version
node --version
npm --version
git --version
```

If the Python launcher is installed, `py --version` and `py -m pip --version` are also available.

## Repository structure

```text
VisionFusion-AI/
├── docs/                   # Project documentation
├── laravel-app/            # Laravel 12 web application (PHP ^8.2)
├── python-ai/              # FastAPI service and local Python virtual environment
│   ├── .venv/               # Generated locally; ignored by Git
│   ├── main.py
│   └── requirements.txt
├── .gitignore
└── README.md
```

Laravel and FastAPI are independent services. Laravel's `AI_SERVICE_URL` is configured for future API integration; this foundation does not yet make AI-service requests.

## Initial setup

Run these commands from a VS Code PowerShell terminal opened at the repository root.

### Python AI service

```powershell
Set-Location .\python-ai
py -m venv .venv
.\.venv\Scripts\Activate.ps1
python -m pip install --upgrade pip
python -m pip install -r requirements.txt
```

If PowerShell prevents virtual-environment activation, skip activation and use the environment's executable directly:

```powershell
.\.venv\Scripts\python.exe -m pip install -r requirements.txt
```

### Laravel web application

Start and configure MySQL in XAMPP, then create a database named `visionfusion_ai` (for example, with phpMyAdmin). From a separate terminal:

```powershell
Set-Location .\laravel-app
if (-not (Test-Path .env)) { Copy-Item .env.example .env }
composer install
php artisan key:generate
```

Edit `laravel-app\.env` if your MySQL host, port, database, username, or password differs from the defaults in `.env.example`. Then run the initial migrations and build the frontend assets:

```powershell
php artisan migrate
npm install
npm run build
```

The repository includes the Laravel Composer scaffold; `composer install` restores its dependencies after cloning. The local `.env` file should not be committed.

## Run both services

Open two separate VS Code PowerShell terminals at the repository root.

**Terminal 1 — FastAPI** (activate the venv first if it is not already active):

```powershell
Set-Location .\python-ai
.\.venv\Scripts\Activate.ps1
python -m uvicorn main:app --reload --host 127.0.0.1 --port 8000
```

Without activation:

```powershell
Set-Location .\python-ai
.\.venv\Scripts\python.exe -m uvicorn main:app --reload --host 127.0.0.1 --port 8000
```

**Terminal 2 — Laravel:**

```powershell
Set-Location .\laravel-app
php artisan serve --host=127.0.0.1 --port=8001
```

Verify the JSON health checks from another PowerShell terminal:

```powershell
Invoke-RestMethod http://127.0.0.1:8000/health
Invoke-RestMethod http://127.0.0.1:8001/health
```

Both endpoints return `{"status":"ok"}`. The FastAPI interactive API documentation is available at `http://127.0.0.1:8000/docs`.

## Health endpoints and configuration

- FastAPI: `GET http://127.0.0.1:8000/health`
- Laravel: `GET http://127.0.0.1:8001/health`
- Laravel's `config/services.php` reads `AI_SERVICE_URL`, which defaults to `http://127.0.0.1:8000`. Set it in `laravel-app\.env` to point Laravel at the FastAPI service.

## Scope

This repository currently contains only the project foundation and health checks. Computer vision, YOLO, EasyOCR, image-question answering, image storage/history, and user-facing application features are intentionally reserved for later steps.
