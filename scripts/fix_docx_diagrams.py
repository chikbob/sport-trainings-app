import math
import shutil
import textwrap
import zipfile
from pathlib import Path

from PIL import Image, ImageDraw, ImageFont


ROOT = Path(__file__).resolve().parent.parent
SOURCE_DOCX = ROOT / "Гогоша Переддипломна практика.docx"
OUTPUT_DOCX = ROOT / "Гогоша Переддипломна практика - fixed diagrams.docx"
OUT_DIR = ROOT / ".report_assets" / "fixed_diagrams"
TMP_DIR = ROOT / ".report_assets" / "tmp" / "fixed_docx"

FONT_REGULAR = "/System/Library/Fonts/Supplemental/Arial Unicode.ttf"
FONT_BOLD = "/System/Library/Fonts/Supplemental/Arial Bold.ttf"

WHITE = "#FFFFFF"
BLACK = "#111111"
NAVY = "#1F4E79"
BLUE = "#DCEAF8"
LIGHT_BLUE = "#EEF5FC"
LIGHT_ORANGE = "#FFF3E6"
ORANGE = "#C96A00"
GRAY = "#666666"
GREEN = "#2E7D32"


def font(size: int, bold: bool = False):
    return ImageFont.truetype(FONT_BOLD if bold else FONT_REGULAR, size)


def draw_centered_text(draw, box, text, size=30, bold=False, fill=BLACK, line_spacing=8):
    fnt = font(size, bold)
    x1, y1, x2, y2 = box
    wrapped = wrap_text(draw, text, fnt, x2 - x1 - 20)
    bbox = draw.multiline_textbbox((0, 0), wrapped, font=fnt, spacing=line_spacing, align="center")
    tw = bbox[2] - bbox[0]
    th = bbox[3] - bbox[1]
    tx = x1 + (x2 - x1 - tw) / 2
    ty = y1 + (y2 - y1 - th) / 2
    draw.multiline_text((tx, ty), wrapped, font=fnt, fill=fill, spacing=line_spacing, align="center")


def wrap_text(draw, text, fnt, max_width):
    lines = []
    for paragraph in text.split("\n"):
        words = paragraph.split()
        if not words:
            lines.append("")
            continue
        current = words[0]
        for word in words[1:]:
            probe = f"{current} {word}"
            width = draw.textbbox((0, 0), probe, font=fnt)[2]
            if width <= max_width:
                current = probe
            else:
                lines.append(current)
                current = word
        lines.append(current)
    return "\n".join(lines)


def rounded_box(draw, box, title, body_lines, title_fill=BLUE, body_fill=WHITE, outline=NAVY):
    x1, y1, x2, y2 = box
    draw.rounded_rectangle(box, radius=24, outline=outline, width=4, fill=body_fill)
    draw.rounded_rectangle((x1, y1, x2, y1 + 64), radius=24, outline=outline, width=4, fill=title_fill)
    draw.rectangle((x1, y1 + 40, x2, y1 + 64), fill=title_fill, outline=title_fill)
    draw.text((x1 + 18, y1 + 14), title, font=font(28, True), fill=BLACK)
    y = y1 + 84
    for line in body_lines:
        draw.text((x1 + 18, y), line, font=font(23, False), fill=BLACK)
        y += 34


def uml_class(draw, box, name, attrs, methods=None):
    x1, y1, x2, y2 = box
    draw.rounded_rectangle(box, radius=18, outline=NAVY, width=4, fill=WHITE)
    name_h = 62
    attrs_h = 34 * len(attrs) + 24
    draw.rectangle((x1, y1, x2, y1 + name_h), outline=NAVY, width=4, fill=BLUE)
    draw.line((x1, y1 + name_h, x2, y1 + name_h), fill=NAVY, width=4)
    draw.line((x1, y1 + name_h + attrs_h, x2, y1 + name_h + attrs_h), fill=NAVY, width=3)
    draw_centered_text(draw, (x1, y1, x2, y1 + name_h), name, size=28, bold=True)
    y = y1 + name_h + 14
    for attr in attrs:
        draw.text((x1 + 16, y), attr, font=font(22), fill=BLACK)
        y += 32
    if methods:
        y = y1 + name_h + attrs_h + 14
        for method in methods:
            draw.text((x1 + 16, y), method, font=font(21), fill=BLACK)
            y += 30


def component_box(draw, box, title, lines):
    x1, y1, x2, y2 = box
    draw.rounded_rectangle(box, radius=22, outline=NAVY, width=4, fill=LIGHT_BLUE)
    draw.text((x1 + 18, y1 + 14), title, font=font(28, True), fill=BLACK)
    draw.rectangle((x2 - 72, y1 + 14, x2 - 26, y1 + 54), outline=NAVY, width=3, fill=WHITE)
    draw.rectangle((x2 - 86, y1 + 22, x2 - 72, y1 + 36), outline=NAVY, width=3, fill=WHITE)
    draw.rectangle((x2 - 86, y1 + 40, x2 - 72, y1 + 54), outline=NAVY, width=3, fill=WHITE)
    y = y1 + 72
    for line in lines:
        draw.text((x1 + 18, y), line, font=font(22), fill=BLACK)
        y += 30


def table_box(draw, box, title, fields):
    x1, y1, x2, y2 = box
    draw.rounded_rectangle(box, radius=20, outline=NAVY, width=4, fill=WHITE)
    draw.rectangle((x1, y1, x2, y1 + 54), outline=NAVY, width=4, fill=BLUE)
    draw.text((x1 + 16, y1 + 12), title, font=font(28, True), fill=BLACK)
    y = y1 + 72
    for field in fields:
        draw.text((x1 + 16, y), field, font=font(22), fill=BLACK)
        y += 30


def arrow(draw, start, end, label=None, dashed=False, color=BLACK, width=4):
    if dashed:
        dash_line(draw, start, end, color=color, width=width)
    else:
        draw.line((start, end), fill=color, width=width)
    angle = math.atan2(end[1] - start[1], end[0] - start[0])
    head = 18
    for delta in (math.pi / 8, -math.pi / 8):
        x = end[0] - head * math.cos(angle + delta)
        y = end[1] - head * math.sin(angle + delta)
        draw.line((end, (x, y)), fill=color, width=width)
    if label:
        mx = (start[0] + end[0]) / 2
        my = (start[1] + end[1]) / 2
        draw.text((mx + 8, my - 28), label, font=font(20, True), fill=color)


def association(draw, start, end, left_label=None, right_label=None):
    draw.line((start, end), fill=BLACK, width=4)
    if left_label:
        draw.text((start[0] + 6, start[1] - 30), left_label, font=font(20, True), fill=BLACK)
    if right_label:
        draw.text((end[0] - 42, end[1] - 30), right_label, font=font(20, True), fill=BLACK)


def dash_line(draw, start, end, color=BLACK, width=3, dash=14, gap=10):
    total_len = math.dist(start, end)
    if total_len == 0:
        return
    dx = (end[0] - start[0]) / total_len
    dy = (end[1] - start[1]) / total_len
    drawn = 0
    while drawn < total_len:
        seg = min(dash, total_len - drawn)
        x1 = start[0] + dx * drawn
        y1 = start[1] + dy * drawn
        x2 = start[0] + dx * (drawn + seg)
        y2 = start[1] + dy * (drawn + seg)
        draw.line((x1, y1, x2, y2), fill=color, width=width)
        drawn += dash + gap


def stick_actor(draw, center_x, top_y, name):
    head_r = 26
    draw.ellipse((center_x - head_r, top_y, center_x + head_r, top_y + head_r * 2), outline=BLACK, width=4)
    y = top_y + head_r * 2
    draw.line((center_x, y, center_x, y + 80), fill=BLACK, width=4)
    draw.line((center_x - 46, y + 26, center_x + 46, y + 26), fill=BLACK, width=4)
    draw.line((center_x, y + 80, center_x - 42, y + 138), fill=BLACK, width=4)
    draw.line((center_x, y + 80, center_x + 42, y + 138), fill=BLACK, width=4)
    w = draw.textbbox((0, 0), name, font=font(26, True))[2]
    draw.text((center_x - w / 2, y + 156), name, font=font(26, True), fill=BLACK)


def ellipse_case(draw, box, text):
    draw.ellipse(box, outline=ORANGE, width=4, fill=LIGHT_ORANGE)
    draw_centered_text(draw, box, text, size=24)


def activation(draw, x, y1, y2):
    draw.rectangle((x - 10, y1, x + 10, y2), outline=BLACK, fill="#D9EAFB", width=2)


def crow_foot(draw, start, end, many_at_end=True):
    draw.line((start, end), fill=BLACK, width=4)
    angle = math.atan2(end[1] - start[1], end[0] - start[0])
    if not many_at_end:
        angle += math.pi
        pivot = start
    else:
        pivot = end
    for delta in (-0.45, 0, 0.45):
        x = pivot[0] - 28 * math.cos(angle + delta)
        y = pivot[1] - 28 * math.sin(angle + delta)
        draw.line((pivot, (x, y)), fill=BLACK, width=3)


def make_image(name, size):
    OUT_DIR.mkdir(parents=True, exist_ok=True)
    img = Image.new("RGB", size, WHITE)
    draw = ImageDraw.Draw(img)
    return img, draw, OUT_DIR / name


def build_use_case():
    img, draw, path = make_image("image4.png", (2550, 1750))
    draw.rounded_rectangle((520, 80, 2060, 1640), radius=30, outline=NAVY, width=5, fill="#F8FBFF")
    draw.text((585, 112), "Інформаційна система організації занять спортивних секцій", font=font(34, True), fill=BLACK)

    stick_actor(draw, 170, 180, "Гість")
    stick_actor(draw, 170, 760, "Користувач")
    stick_actor(draw, 2360, 880, "Тренер")
    stick_actor(draw, 2360, 1210, "Адміністратор")

    cases = {
        "register": (760, 210, 1220, 320, "Зареєструватися"),
        "login": (1320, 210, 1780, 320, "Увійти до системи"),
        "browse_sports": (860, 395, 1680, 515, "Переглядати спортивні секції"),
        "browse_trainings": (860, 560, 1680, 680, "Переглядати календар\nта сторінки тренувань"),
        "register_training": (860, 735, 1680, 855, "Записатися на тренування"),
        "re_register": (1720, 735, 1975, 835, "Повторно\nзаписатися"),
        "profile": (860, 900, 1680, 1020, "Переглядати профіль\nта історію записів"),
        "cancel_registration": (1720, 900, 1975, 1000, "Скасувати\nзапис"),
        "coach_own": (1230, 1140, 1940, 1260, "Переглядати власні тренування"),
        "coach_status": (1230, 1300, 1940, 1420, "Оновлювати статуси учасників"),
        "coach_finish": (1230, 1460, 1560, 1560, "Завершити\nтренування"),
        "coach_cancel": (1610, 1460, 1940, 1560, "Скасувати\nтренування"),
        "admin_dash": (620, 1140, 1070, 1240, "Переглядати статистику"),
        "admin_sports": (620, 1270, 1070, 1370, "Керувати секціями"),
        "admin_trainings": (620, 1400, 1070, 1500, "Керувати тренуваннями"),
        "admin_users": (620, 1530, 1070, 1630, "Керувати користувачами,\nтренерами та записами"),
    }
    for _, box in cases.items():
        ellipse_case(draw, box[:4], box[4])

    # Associations
    for target in ("register", "login", "browse_sports", "browse_trainings"):
        x1 = 240
        y = (cases[target][1] + cases[target][3]) // 2
        draw.line((x1, y, cases[target][0], y), fill=BLACK, width=4)

    for target in ("browse_sports", "browse_trainings", "register_training", "profile"):
        x1 = 240
        y = (cases[target][1] + cases[target][3]) // 2
        draw.line((x1, y, cases[target][0], y), fill=BLACK, width=4)

    for target in ("coach_own", "coach_status", "coach_finish", "coach_cancel"):
        x2 = 2290
        y = (cases[target][1] + cases[target][3]) // 2
        draw.line((x2, y, cases[target][2], y), fill=BLACK, width=4)

    for target in ("admin_dash", "admin_sports", "admin_trainings", "admin_users"):
        x2 = 2290
        y = (cases[target][1] + cases[target][3]) // 2
        draw.line((x2, y, cases[target][2], y), fill=BLACK, width=4)

    # Include / extend
    arrow(draw, (1720, 785), (1680, 785), "<<extend>>", dashed=True, color=GRAY, width=3)
    arrow(draw, (1720, 950), (1680, 950), "<<extend>>", dashed=True, color=GRAY, width=3)
    arrow(draw, (1230, 1360), (1230, 1210), "<<include>>", dashed=True, color=GRAY, width=3)
    img.save(path, dpi=(300, 300))
    return path


def build_class_diagram():
    img, draw, path = make_image("image5.png", (2300, 1600))
    uml_class(draw, (80, 90, 720, 760), "User", [
        "+ id: bigint",
        "+ name: string",
        "+ email: string",
        "+ password: string",
        "+ role: enum(user, admin, coach)",
        "+ deleted_at: timestamp?",
    ], [
        "+ registrations()",
        "+ coach()",
        "+ scopeSearch(search)",
        "+ scopeRole(role)",
    ])
    uml_class(draw, (850, 120, 1450, 650), "Coach", [
        "+ id: bigint",
        "+ user_id: bigint",
        "+ bio: text?",
        "+ phone: string?",
        "+ specialization: string?",
    ], [
        "+ user()",
        "+ sports()",
    ])
    uml_class(draw, (1580, 90, 2220, 720), "Sport", [
        "+ id: bigint",
        "+ name: string",
        "+ description: text?",
        "+ location: string?",
        "+ coach_id: bigint?",
    ], [
        "+ coach()",
        "+ trainings()",
        "+ registrations()",
    ])
    uml_class(draw, (170, 930, 940, 1520), "Registration", [
        "+ id: bigint",
        "+ user_id: bigint",
        "+ training_id: bigint",
        "+ status: enum",
        "+ created_at: timestamp",
    ], [
        "+ user()",
        "+ training()",
        "+ STATUS_PENDING",
        "+ STATUS_APPROVED",
        "+ STATUS_CANCELLED",
        "+ STATUS_REJECTED",
        "+ STATUS_ATTENDED",
        "+ STATUS_NO_SHOW",
    ])
    uml_class(draw, (1260, 900, 2160, 1520), "Training", [
        "+ id: bigint",
        "+ sport_id: bigint",
        "+ date: date",
        "+ time: time",
        "+ place: string?",
        "+ notes: string?",
        "+ is_cancelled: bool",
        "+ is_completed: bool",
        "+ cancelled_at: timestamp?",
        "+ completed_at: timestamp?",
    ], [
        "+ sport()",
        "+ registrations()",
    ])

    association(draw, (720, 240), (850, 240), "1", "0..1")
    association(draw, (1450, 260), (1580, 260), "1", "0..*")
    association(draw, (500, 760), (470, 930), "1", "0..*")
    association(draw, (940, 1220), (1260, 1220), "0..*", "1")
    association(draw, (1900, 720), (1800, 900), "1", "0..*")

    draw.text((990, 40), "Предметна модель системи", font=font(30, True), fill=BLACK)
    img.save(path, dpi=(300, 300))
    return path


def build_component_diagram():
    img, draw, path = make_image("image6.png", (2400, 1600))
    component_box(draw, (90, 130, 650, 430), "Вебклієнт", [
        "Браузер користувача",
        "Vue 3 сторінки",
        "Layout / компоненти",
        "Локалізація та форматери",
    ])
    component_box(draw, (840, 100, 1570, 450), "Inertia.js + Ziggy", [
        "Навігація без повного reload",
        "Передача props від Laravel",
        "Маршрути для фронтенду",
    ])
    component_box(draw, (820, 560, 1600, 1010), "Laravel Web Application", [
        "routes/web.php",
        "Middleware: auth, admin, coach, locale",
        "Controllers: Auth, Sport, Training,",
        "Registration, Admin, Coach",
        "Eloquent Models: User, Coach, Sport,",
        "Training, Registration",
    ])
    component_box(draw, (1810, 140, 2310, 430), "MySQL 8", [
        "users, coaches, sports,",
        "trainings, registrations",
        "сховище предметних даних",
    ])
    component_box(draw, (1810, 560, 2310, 820), "Redis", [
        "сесії",
        "черги",
        "кеш та допоміжні дані",
    ])
    component_box(draw, (140, 720, 650, 1090), "Інфраструктура Docker", [
        "nginx",
        "app",
        "vite",
        "db",
        "redis",
        "queue",
    ])
    component_box(draw, (140, 1180, 650, 1460), "Nginx / Vite / Queue", [
        "Nginx віддає HTTP",
        "Vite збирає ресурси",
        "Queue обробляє фоні задачі",
    ])

    arrow(draw, (650, 280), (840, 280))
    draw.text((720, 238), "HTTP + props", font=font(20, True), fill=BLACK)
    arrow(draw, (1200, 450), (1200, 560))
    draw.text((1216, 490), "запити Inertia", font=font(20, True), fill=BLACK)
    arrow(draw, (1570, 280), (1810, 280))
    draw.text((1605, 238), "SQL", font=font(20, True), fill=BLACK)
    arrow(draw, (1600, 750), (1810, 690))
    draw.text((1665, 730), "cache / session", font=font(18, True), fill=BLACK)
    arrow(draw, (650, 900), (820, 800))
    draw.text((690, 840), "контейнери", font=font(18, True), fill=BLACK)
    arrow(draw, (390, 1180), (390, 1090))
    draw.text((404, 1118), "мережевий та build-рівень", font=font(18, True), fill=BLACK)
    draw.text((900, 40), "Компонентна архітектура системи", font=font(30, True), fill=BLACK)
    img.save(path, dpi=(300, 300))
    return path


def build_sequence_diagram():
    img, draw, path = make_image("image7.png", (2400, 1500))
    draw.text((820, 40), "Сценарій: запис користувача на тренування", font=font(30, True), fill=BLACK)
    lifelines = [
        ("Користувач", 180),
        ("Браузер", 560),
        ("Vue / Inertia", 940),
        ("RegistrationController", 1380),
        ("Registration model", 1780),
        ("MySQL", 2180),
    ]
    for name, x in lifelines:
        draw_centered_text(draw, (x - 120, 90, x + 120, 150), name, size=22, bold=True)
        draw.line((x, 170, x, 1390), fill=GRAY, width=2)

    activation(draw, 560, 220, 1320)
    activation(draw, 940, 300, 1260)
    activation(draw, 1380, 380, 1180)
    activation(draw, 1780, 520, 1060)
    activation(draw, 2180, 660, 880)

    steps = [
        (180, 560, 240, "1. Відкриває сторінку тренування"),
        (560, 940, 320, "2. Відображення даних тренування"),
        (180, 560, 430, "3. Натискає «Записатися»"),
        (560, 940, 520, "4. POST /trainings/{id}/register"),
        (940, 1380, 620, "5. Передача запиту"),
        (1380, 1780, 720, "6. Перевірка: дата, is_cancelled,\nнаявність попереднього запису"),
        (1780, 2180, 820, "7. SELECT / INSERT / UPDATE"),
        (2180, 1780, 920, "8. Підтвердження БД"),
        (1780, 1380, 1010, "9. Повернення результату"),
        (1380, 940, 1100, "10. Redirect back + flash message"),
        (940, 560, 1190, "11. Оновлення props / стану сторінки"),
        (560, 180, 1280, "12. Користувач бачить успішний запис"),
    ]
    for x1, x2, y, text in steps:
        arrow(draw, (x1, y), (x2, y))
        draw.text((min(x1, x2) + 18, y - 46), text, font=font(20), fill=BLACK)

    img.save(path, dpi=(300, 300))
    return path


def build_er_diagram():
    img, draw, path = make_image("image8.png", (2400, 1600))
    table_box(draw, (90, 120, 760, 560), "users", [
        "PK id",
        "name",
        "email",
        "password",
        "role",
        "remember_token",
        "deleted_at",
    ])
    table_box(draw, (900, 120, 1490, 500), "coaches", [
        "PK id",
        "FK user_id -> users.id",
        "bio",
        "phone",
        "specialization",
    ])
    table_box(draw, (1640, 120, 2290, 560), "sports", [
        "PK id",
        "name",
        "description",
        "location",
        "FK coach_id -> coaches.id",
    ])
    table_box(draw, (220, 910, 980, 1490), "registrations", [
        "PK id",
        "FK user_id -> users.id",
        "FK training_id -> trainings.id",
        "status",
        "created_at",
        "updated_at",
    ])
    table_box(draw, (1370, 900, 2290, 1490), "trainings", [
        "PK id",
        "FK sport_id -> sports.id",
        "date",
        "time",
        "place",
        "notes",
        "is_cancelled",
        "is_completed",
        "cancelled_at",
        "completed_at",
    ])

    crow_foot(draw, (760, 300), (900, 300), many_at_end=False)
    draw.text((812, 250), "1", font=font(20, True), fill=BLACK)
    draw.text((840, 330), "0..1", font=font(20, True), fill=BLACK)

    crow_foot(draw, (1490, 300), (1640, 300), many_at_end=True)
    draw.text((1530, 250), "1", font=font(20, True), fill=BLACK)
    draw.text((1575, 330), "0..N", font=font(20, True), fill=BLACK)

    crow_foot(draw, (1960, 560), (1960, 900), many_at_end=True)
    draw.text((1980, 690), "1 : 0..N", font=font(20, True), fill=BLACK)

    crow_foot(draw, (500, 560), (600, 910), many_at_end=True)
    draw.text((440, 710), "1 : 0..N", font=font(20, True), fill=BLACK)

    crow_foot(draw, (980, 1180), (1370, 1180), many_at_end=False)
    draw.text((1130, 1130), "0..N : 1", font=font(20, True), fill=BLACK)

    draw.text((860, 40), "ER-діаграма предметної області", font=font(30, True), fill=BLACK)
    img.save(path, dpi=(300, 300))
    return path


def replace_media():
    TMP_DIR.mkdir(parents=True, exist_ok=True)
    unpacked = TMP_DIR / "unpacked"
    if unpacked.exists():
        shutil.rmtree(unpacked)
    unpacked.mkdir(parents=True)

    with zipfile.ZipFile(SOURCE_DOCX, "r") as zf:
        zf.extractall(unpacked)

    replacements = {
        "word/media/image4.png": build_use_case(),
        "word/media/image5.png": build_class_diagram(),
        "word/media/image6.png": build_component_diagram(),
        "word/media/image7.png": build_sequence_diagram(),
        "word/media/image8.png": build_er_diagram(),
    }

    for target, source in replacements.items():
        shutil.copy2(source, unpacked / target)

    if OUTPUT_DOCX.exists():
        OUTPUT_DOCX.unlink()

    with zipfile.ZipFile(OUTPUT_DOCX, "w", zipfile.ZIP_DEFLATED) as zf:
        for file_path in sorted(unpacked.rglob("*")):
            if file_path.is_file():
                zf.write(file_path, file_path.relative_to(unpacked))


if __name__ == "__main__":
    replace_media()
