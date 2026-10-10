from io import BytesIO

from fastapi.testclient import TestClient
from PIL import Image

from config import MAX_UPLOAD_SIZE_BYTES
from main import app

client = TestClient(app)


def make_image(image_format: str) -> bytes:
    image = BytesIO()
    Image.new("RGB", (3, 2), (42, 96, 168)).save(image, format=image_format)
    return image.getvalue()


def test_health_check() -> None:
    response = client.get("/health")

    assert response.status_code == 200
    assert response.json() == {"status": "ok"}


def test_analyzes_valid_jpeg() -> None:
    response = client.post(
        "/api/v1/analyze-image",
        files={"file": ("sample.jpg", make_image("JPEG"), "image/jpeg")},
    )

    assert response.status_code == 200
    assert response.json() == {
        "success": True,
        "filename": "sample.jpg",
        "format": "JPEG",
        "width": 3,
        "height": 2,
        "file_size_bytes": len(make_image("JPEG")),
        "message": "Image validated successfully. Image analysis is not enabled yet.",
    }


def test_analyzes_valid_png() -> None:
    response = client.post(
        "/api/v1/analyze-image",
        files={"file": ("sample.png", make_image("PNG"), "image/png")},
    )

    assert response.status_code == 200
    assert response.json()["format"] == "PNG"
    assert response.json()["width"] == 3
    assert response.json()["height"] == 2


def test_rejects_unsupported_content() -> None:
    unsupported_image = BytesIO()
    Image.new("RGB", (3, 2), (42, 96, 168)).save(unsupported_image, format="GIF")

    response = client.post(
        "/api/v1/analyze-image",
        files={"file": ("animation.gif", unsupported_image.getvalue(), "image/gif")},
    )

    assert response.status_code == 415


def test_rejects_corrupted_image() -> None:
    response = client.post(
        "/api/v1/analyze-image",
        files={"file": ("corrupt.jpg", b"not a readable image", "image/jpeg")},
    )

    assert response.status_code == 422


def test_rejects_image_larger_than_limit() -> None:
    response = client.post(
        "/api/v1/analyze-image",
        files={"file": ("large.jpg", b"x" * (MAX_UPLOAD_SIZE_BYTES + 1), "image/jpeg")},
    )

    assert response.status_code == 413
