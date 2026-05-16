"""
API Routes — FastAPI endpoints for mental health prediction.
"""

from fastapi import APIRouter, HTTPException
from pydantic import BaseModel, Field
from ..services.predictor import get_predictor
from ..services.anonymizer import get_anonymizer

router = APIRouter()


# ─── Request/Response Models ─────────────────────────────────

class PredictRequest(BaseModel):
    text: str = Field(..., min_length=1, max_length=5000, description="Teks untuk dianalisis")


class PredictResponse(BaseModel):
    label: int = Field(..., description="Label klasifikasi (0-4)")
    label_name: str = Field(..., description="Nama label")
    risk_level: str = Field(..., description="Level risiko: LOW/MEDIUM/HIGH/CRITICAL")
    confidence: float = Field(..., description="Confidence score (0-1)")
    ai_reply: str = Field(..., description="Respons empatik dari AI")
    ai_saran: list[str] = Field(..., description="Saran kegiatan dari AI")


class HealthResponse(BaseModel):
    status: str
    model_loaded: bool


# ─── Endpoints ───────────────────────────────────────────────

@router.post("/predict", response_model=PredictResponse)
async def predict(request: PredictRequest):
    """
    Analisis teks menggunakan IndoBERT dan kembalikan prediksi
    label risiko kesehatan mental beserta respons empatik.
    """
    try:
        predictor = get_predictor()
        anonymizer = get_anonymizer()

        # 1. Anonymize PII sebelum prediksi
        anonymized_text = anonymizer.process(request.text)

        # 2. Prediksi label
        result = predictor.predict(anonymized_text)

        # 3. Generate empathic reply (pakai teks asli untuk konteks)
        reply = predictor.generate_reply(request.text, result["label"])

        return PredictResponse(
            label=result["label"],
            label_name=result["label_name"],
            risk_level=result["risk_level"],
            confidence=result["confidence"],
            ai_reply=reply["text"],
            ai_saran=reply["saran"],
        )

    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Prediction error: {str(e)}")


@router.get("/health", response_model=HealthResponse)
async def health_check():
    """Health check endpoint."""
    try:
        predictor = get_predictor()
        return HealthResponse(status="ok", model_loaded=predictor is not None)
    except Exception:
        return HealthResponse(status="error", model_loaded=False)
