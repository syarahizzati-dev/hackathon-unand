"""
CAMPUS-E FastAPI Backend — IndoBERT Mental Health Classifier
"""

from contextlib import asynccontextmanager
from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from .api.routes import router


@asynccontextmanager
async def lifespan(app: FastAPI):
    """Pre-load model on startup to avoid cold-start latency."""
    from .services.predictor import get_predictor
    print("[CAMPUS-E] Starting up — loading IndoBERT model...")
    get_predictor()
    print("[CAMPUS-E] Model ready! Server accepting requests.")
    yield
    print("[CAMPUS-E] Shutting down...")


app = FastAPI(
    title="CAMPUS-E AI Backend",
    description="Mental health text classification API using fine-tuned IndoBERT",
    version="1.0.0",
    lifespan=lifespan,
)

# ─── CORS ─────────────────────────────────────────────────────
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# ─── Routes ───────────────────────────────────────────────────
app.include_router(router)
