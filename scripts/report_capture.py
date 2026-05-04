import re
from pathlib import Path

from playwright.sync_api import sync_playwright


BASE_URL = "http://localhost"
OUT_DIR = Path(".report_assets/screenshots")


def save_public(page, path: str, name: str) -> None:
    page.goto(f"{BASE_URL}{path}", wait_until="networkidle")
    page.screenshot(path=str(OUT_DIR / name), full_page=True)


def login(context, email: str, password: str) -> None:
    response = context.request.get(f"{BASE_URL}/login")
    token = re.search(r'<meta name="csrf-token" content="([^"]+)"', response.text()).group(1)
    context.request.post(
        f"{BASE_URL}/login",
        form={
            "email": email,
            "password": password,
            "_token": token,
        },
        headers={"Referer": f"{BASE_URL}/login"},
    )


def main() -> None:
    OUT_DIR.mkdir(parents=True, exist_ok=True)

    with sync_playwright() as p:
        browser = p.chromium.launch(channel="chrome", headless=True)

        public_context = browser.new_context(
            viewport={"width": 1440, "height": 1200},
            locale="uk-UA",
        )
        public_page = public_context.new_page()
        public_page.add_init_script(
            """
            localStorage.setItem('lang', 'uk');
            document.cookie = 'lang=uk; path=/';
            """
        )
        save_public(public_page, "/", "01_home.png")
        save_public(public_page, "/login", "02_login.png")
        save_public(public_page, "/sports", "03_sports.png")
        save_public(public_page, "/sports/1", "04_sport_show.png")
        save_public(public_page, "/trainings", "05_trainings.png")
        save_public(public_page, "/trainings/1", "06_training_show.png")
        public_context.close()

        admin_context = browser.new_context(
            viewport={"width": 1440, "height": 1200},
            locale="uk-UA",
        )
        admin_page = admin_context.new_page()
        admin_page.add_init_script(
            """
            localStorage.setItem('lang', 'uk');
            document.cookie = 'lang=uk; path=/';
            """
        )
        login(admin_context, "admin1@gmail.com", "password")
        admin_page.goto(f"{BASE_URL}/admin", wait_until="networkidle")
        admin_page.screenshot(path=str(OUT_DIR / "07_admin_dashboard.png"), full_page=True)
        admin_page.goto(f"{BASE_URL}/admin/sports", wait_until="networkidle")
        admin_page.screenshot(path=str(OUT_DIR / "08_admin_sports.png"), full_page=True)
        admin_context.close()

        coach_context = browser.new_context(
            viewport={"width": 1440, "height": 1200},
            locale="uk-UA",
        )
        coach_page = coach_context.new_page()
        coach_page.add_init_script(
            """
            localStorage.setItem('lang', 'uk');
            document.cookie = 'lang=uk; path=/';
            """
        )
        login(coach_context, "coach@yandex.ru", "password")
        coach_page.goto(f"{BASE_URL}/coach", wait_until="networkidle")
        coach_page.screenshot(path=str(OUT_DIR / "09_coach_dashboard.png"), full_page=True)
        coach_page.goto(f"{BASE_URL}/coach/trainings", wait_until="networkidle")
        coach_page.screenshot(path=str(OUT_DIR / "10_coach_trainings.png"), full_page=True)
        coach_context.close()

        user_context = browser.new_context(
            viewport={"width": 1440, "height": 1200},
            locale="uk-UA",
        )
        user_page = user_context.new_page()
        user_page.add_init_script(
            """
            localStorage.setItem('lang', 'uk');
            document.cookie = 'lang=uk; path=/';
            """
        )
        login(user_context, "test@yandex.ru", "password")
        user_page.goto(f"{BASE_URL}/profile", wait_until="networkidle")
        user_page.screenshot(path=str(OUT_DIR / "11_profile.png"), full_page=True)
        user_context.close()

        browser.close()


if __name__ == "__main__":
    main()
