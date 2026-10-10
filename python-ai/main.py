import logging
from io import BytesIO

from fastapi import FastAPI, File, HTTPException, UploadFile
from PIL import Image, UnidentifiedImageError

from config import MAX_IMAGE_PIXELS, MAX_UPLOAD_SIZE_BYTES, MAX_UPLOAD_SIZE_MB

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger("visionfusion.ai")

ALLOWED_FORMATS = {"JPEG", "PNG", "WEBP"}

app = FastAPI(title="VisionFusion AI Service")


@app.get("/health")
def health_check() -> dict[str, str]:
    return {"status": "ok"}


@app.post("/api/v1/analyze-image")
async def analyze_image(file: UploadFile = File(...)) -> dict[str, object]:
    try:
        content = bytearray()
        while chunk := await file.read(64 * 1024):
            content.extend(chunk)
            if len(content) > MAX_UPLOAD_SIZE_BYTES:
                raise HTTPException(
                    status_code=413,
                    detail=f"Image exceeds the {MAX_UPLOAD_SIZE_MB} MB upload limit.",
                )

        if not content:
            raise HTTPException(status_code=422, detail="The uploaded file is empty.")

        try:
            with Image.open(BytesIO(content)) as image:
                image_format = image.format
                if image_format not in ALLOWED_FORMATS:
                    raise HTTPException(
                        status_code=415,
                        detail="Unsupported image format. Upload a JPG, PNG, or WEBP image.",
                    )
                width, height = image.size
                if width * height > MAX_IMAGE_PIXELS:
                    raise HTTPException(
                        status_code=422,
                        detail="The image dimensions exceed the supported limit.",
                    )
                image.verify()

            with Image.open(BytesIO(content)) as image:
                image.load()
        except HTTPException:
            raise
        except (UnidentifiedImageError, OSError, Image.DecompressionBombError, ValueError) as error:
            raise HTTPException(
                status_code=422,
                detail="The uploaded file is not a valid, readable image.",
            ) from error

        return {
            "success": True,
            "filename": file.filename or "image",
            "format": image_format,
            "width": width,
            "height": height,
            "file_size_bytes": len(content),
            "message": "Image validated successfully. Image analysis is not enabled yet.",
        }
    except HTTPException:
        raise
    except Exception:
        logger.exception("Unexpected image processing error")
        raise HTTPException(
            status_code=500,
            detail="The image could not be processed right now.",
        ) from None
    finally:
        await file.close()
