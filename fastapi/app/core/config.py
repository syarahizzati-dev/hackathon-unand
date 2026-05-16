import os

# ─── Model Path ─────────────────────────────────────────────
BASE_DIR = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
MODEL_PATH = os.path.join(
    BASE_DIR,
    "ai_model",
    "campus_e_indobert_model",
)

# ─── Label & Risk Mapping ───────────────────────────────────
LABEL_MAP = {
    0: "NORMAL",
    1: "MENTAL_FATIGUE",
    2: "EMOTIONAL_STRESS",
    3: "DEPRESSION_RISK",
    4: "SUICIDAL_IDEATION",
}

RISK_MAP = {
    0: "LOW",
    1: "LOW",
    2: "MEDIUM",
    3: "HIGH",
    4: "CRITICAL",
}

# ─── Empathic Reply Templates ───────────────────────────────
REPLY_TEMPLATES = {
    0: [
        "Senang mendengar kamu baik-baik saja! Terus jaga kesehatan mentalmu ya. 😊",
        "Kamu terlihat dalam kondisi yang baik. Tetap lakukan hal-hal yang membuatmu bahagia! 💪",
        "Bagus sekali! Teruslah menjaga keseimbangan hidupmu. 🌟",
    ],
    1: [
        "Kamu mungkin sedang merasa sedikit lelah. Itu wajar — pastikan kamu cukup istirahat. 💙",
        "Sepertinya kamu butuh waktu untuk dirimu sendiri. Jangan lupa istirahat ya. 🌙",
        "Kelelahan mental itu nyata. Izinkan dirimu untuk beristirahat sejenak. 💤",
    ],
    2: [
        "Terima kasih sudah mau berbagi. Perasaanmu valid dan penting. Kamu tidak sendirian. 💙",
        "Kami mengerti ini tidak mudah. Kamu sudah kuat sampai di sini. Teruslah berjuang. 🌈",
        "Stres emosional bisa terasa berat, tapi kamu layak mendapat dukungan. 💛",
    ],
    3: [
        "Kami sangat peduli dengan kondisimu. Pertimbangkan untuk berbicara dengan konselor kampus. 💙",
        "Perasaanmu penting dan layak didengar. Jangan ragu untuk mencari bantuan profesional. 🤝",
        "Kamu tidak harus melewati ini sendirian. Ada orang-orang yang siap membantu. 💙",
    ],
    4: [
        "Kami sangat khawatir dengan kondisimu. Tolong hubungi orang terdekatmu atau konselor kampus sekarang. Kamu berharga. 💙",
        "Hidupmu sangat berharga. Tolong jangan ragu untuk menghubungi layanan krisis atau orang yang kamu percaya. ❤️",
        "Kamu penting dan dibutuhkan. Tolong segera bicara dengan seseorang — konselor, keluarga, atau teman. 🆘💙",
    ],
}

# ─── Suggestion Templates ───────────────────────────────────
SARAN_TEMPLATES = {
    0: [
        "Jalan-jalan santai di luar rumah selama 15 menit",
        "Tulis 3 hal yang kamu syukuri hari ini",
        "Dengarkan musik favoritmu",
    ],
    1: [
        "Tidur cukup 7-8 jam malam ini",
        "Lakukan peregangan ringan selama 10 menit",
        "Kurangi konsumsi kafein hari ini",
    ],
    2: [
        "Luangkan waktu untuk istirahat dan relaksasi",
        "Coba teknik pernapasan 4-7-8 untuk menenangkan diri",
        "Tulis 3 hal yang kamu syukuri hari ini",
    ],
    3: [
        "Hubungi konselor kampus untuk konsultasi",
        "Ceritakan perasaanmu ke orang yang kamu percaya",
        "Lakukan aktivitas grounding: tarik napas dalam 4-7-8",
    ],
    4: [
        "Segera hubungi konselor kampus atau saluran krisis",
        "Jangan sendirian — hubungi keluarga atau teman terdekat",
        "Hubungi hotline kesehatan mental: 119 ext. 8",
    ],
}

# ─── Server Config ──────────────────────────────────────────
HOST = os.getenv("FASTAPI_HOST", "127.0.0.1")
PORT = int(os.getenv("FASTAPI_PORT", "8000"))
