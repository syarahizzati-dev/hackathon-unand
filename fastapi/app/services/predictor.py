"""
Predictor Service — Load & inference IndoBERT model for mental health classification.
"""

import os
import random
import torch
from transformers import BertTokenizer, BertForSequenceClassification
from ..core.config import MODEL_PATH, LABEL_MAP, RISK_MAP, REPLY_TEMPLATES, SARAN_TEMPLATES


class Predictor:
    """Singleton predictor that loads the IndoBERT model once at startup."""

    def __init__(self):
        self.device = torch.device("cuda" if torch.cuda.is_available() else "cpu")
        print(f"[Predictor] Loading model from {MODEL_PATH} on {self.device}...")

        # Validasi path model sebelum loading
        if not os.path.exists(MODEL_PATH):
            raise FileNotFoundError(
                f"[Predictor] Model directory not found: {MODEL_PATH}. "
                f"Pastikan folder ai_model/campus_e_indobert_model/ berisi file model."
            )

        required_files = ["config.json", "model.safetensors", "vocab.txt"]
        missing = [f for f in required_files if not os.path.exists(os.path.join(MODEL_PATH, f))]
        if missing:
            raise FileNotFoundError(
                f"[Predictor] Missing model files: {missing}. "
                f"Pastikan semua file model ada di {MODEL_PATH}."
            )

        try:
            self.tokenizer = BertTokenizer.from_pretrained(MODEL_PATH)
            self.model = BertForSequenceClassification.from_pretrained(MODEL_PATH)
            self.model.to(self.device)
            self.model.eval()
            print("[Predictor] Model loaded successfully!")
        except Exception as e:
            raise RuntimeError(
                f"[Predictor] Gagal memuat model IndoBERT: {e}"
            ) from e

    def predict(self, text: str) -> dict:
        """
        Predict mental health label from text.

        Returns:
            dict with keys: label (int), label_name (str),
            risk_level (str), confidence (float)
        """
        if not text or not text.strip():
            return {
                "label": 0,
                "label_name": LABEL_MAP[0],
                "risk_level": RISK_MAP[0],
                "confidence": 0.0,
            }

        inputs = self.tokenizer(
            text,
            return_tensors="pt",
            max_length=512,
            truncation=True,
            padding="max_length",
        )
        inputs = {k: v.to(self.device) for k, v in inputs.items()}

        with torch.no_grad():
            outputs = self.model(**inputs)
            logits = outputs.logits
            probabilities = torch.softmax(logits, dim=-1)
            predicted_label = torch.argmax(probabilities, dim=-1).item()
            confidence = probabilities[0][predicted_label].item()

        return {
            "label": predicted_label,
            "label_name": LABEL_MAP.get(predicted_label, "UNKNOWN"),
            "risk_level": RISK_MAP.get(predicted_label, "LOW"),
            "confidence": round(confidence, 4),
        }

    def generate_reply(self, text: str, label: int) -> dict:
        """
        Generate empathic reply and activity suggestions based on label.

        Returns:
            dict with keys: text (str), saran (list[str])
        """
        # Clamp label ke range valid
        label = max(0, min(label, 4))

        # Pick random template for variety
        replies = REPLY_TEMPLATES.get(label, REPLY_TEMPLATES[0])
        saran = SARAN_TEMPLATES.get(label, SARAN_TEMPLATES[0])

        return {
            "text": random.choice(replies),
            "saran": saran,
        }


# ─── Singleton instance ──────────────────────────────────────
_predictor: Predictor | None = None


def get_predictor() -> Predictor:
    """Get or create the singleton predictor instance."""
    global _predictor
    if _predictor is None:
        _predictor = Predictor()
    return _predictor
