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

        replies = REPLY_TEMPLATES.get(label, REPLY_TEMPLATES[0])
        saran = self._suggestions_for_text(text, label)

        return {
            "text": random.choice(replies),
            "saran": saran,
        }

    def _suggestions_for_text(self, text: str, label: int) -> list[str]:
        text_lower = text.lower()
        suggestions: list[str] = []

        if any(word in text_lower for word in ["tidur", "lelah", "capek"]):
            suggestions.extend([
                "Matikan layar 30 menit sebelum tidur dan usahakan tidur lebih awal malam ini",
                "Lakukan peregangan ringan 10 menit untuk melepas tegang",
            ])

        if any(word in text_lower for word in ["cemas", "khawatir", "panik", "overthinking"]):
            suggestions.extend([
                "Coba teknik napas 4-7-8 selama 3 putaran",
                "Lakukan grounding 5-4-3-2-1 untuk menenangkan pikiran",
            ])

        if any(word in text_lower for word in ["sendiri", "kesepian", "menangis"]):
            suggestions.extend([
                "Kirim pesan ke satu teman atau keluarga yang kamu percaya",
                "Tulis perasaanmu selama 10 menit tanpa menilai diri sendiri",
            ])

        if label >= 4:
            suggestions.insert(0, "Segera hubungi konselor kampus, kontak darurat, atau hotline kesehatan mental 119 ext. 8")
        elif label >= 3:
            suggestions.insert(0, "Jadwalkan sesi dengan konselor kampus secepatnya")

        suggestions.extend(SARAN_TEMPLATES.get(label, SARAN_TEMPLATES[0]))
        return list(dict.fromkeys(suggestions))[:3]


# ─── Singleton instance ──────────────────────────────────────
_predictor: Predictor | None = None


def get_predictor() -> Predictor:
    """Get or create the singleton predictor instance."""
    global _predictor
    if _predictor is None:
        _predictor = Predictor()
    return _predictor
