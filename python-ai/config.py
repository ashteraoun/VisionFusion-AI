import os


MAX_UPLOAD_SIZE_MB = int(os.getenv("MAX_IMAGE_UPLOAD_MB", "10"))
if MAX_UPLOAD_SIZE_MB < 1:
    raise ValueError("MAX_IMAGE_UPLOAD_MB must be a positive integer.")

MAX_UPLOAD_SIZE_BYTES = MAX_UPLOAD_SIZE_MB * 1024 * 1024
MAX_IMAGE_PIXELS = 40_000_000
