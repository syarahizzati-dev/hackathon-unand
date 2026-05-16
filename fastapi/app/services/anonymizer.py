"""
Anonymizer Service — Strip personally identifiable information from text
before sending to the AI model, to protect student privacy.
"""

import re


class Anonymizer:
    """Deteksi dan mask PII (nama, NIM, email, telepon) dari teks."""

    # Pattern: NIM (8-15 digit angka)
    NIM_PATTERN = re.compile(r"\b\d{8,15}\b")

    # Pattern: Email
    EMAIL_PATTERN = re.compile(
        r"\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b"
    )

    # Pattern: Nomor telepon Indonesia (08xx, +62xx, 62xx)
    PHONE_PATTERN = re.compile(
        r"(?:\+62|62|0)\s?8[1-9]\d{1,2}[\s.-]?\d{3,4}[\s.-]?\d{3,4}\b"
    )

    # Pattern: Nama lengkap (2-4 kata kapital berturut-turut)
    NAME_PATTERN = re.compile(
        r"\b(?:[A-Z][a-z]+(?:\s+[A-Z][a-z]+){1,3})\b"
    )

    def process(self, text: str) -> str:
        """
        Anonymize text by replacing PII with generic tokens.

        Args:
            text: Raw input text

        Returns:
            Anonymized text with PII replaced
        """
        result = text

        # Mask emails
        result = self.EMAIL_PATTERN.sub("[EMAIL]", result)

        # Mask phone numbers
        result = self.PHONE_PATTERN.sub("[TELEPON]", result)

        # Mask NIM (only if looks like a student ID)
        result = self.NIM_PATTERN.sub("[NIM]", result)

        # Mask potential names (heuristic — only clear proper noun sequences)
        result = self.NAME_PATTERN.sub("[NAMA]", result)

        return result


# ─── Singleton instance ──────────────────────────────────────
_anonymizer: Anonymizer | None = None


def get_anonymizer() -> Anonymizer:
    """Get or create the singleton anonymizer instance."""
    global _anonymizer
    if _anonymizer is None:
        _anonymizer = Anonymizer()
    return _anonymizer
