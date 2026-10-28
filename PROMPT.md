Build planned portfolio project id 8 (Raffle Desk) in THIS folder: C:\xampp\htdocs\raffle-desk

First, from C:\xampp\htdocs\NewPortfolio:
php artisan projects:planned
Require source=production. Use id 8. Follow its spec and ordered tasks. Work ONE pending task at a time. After each slice: php artisan projects:task-done {taskId}

There may already be a Laravel scaffold here. Reuse it. Do not create a second app inside NewPortfolio.

FORBIDDEN:
- Do not load, read, or run the Impeccable skill, impeccable.style, concept-seed, build-phase, or any design-world picker.
- Do not redesign the visual system. Keep Breeze + Tailwind (or a simple Laravel + Vue + Tailwind UI).
- Do not fetch images for inspiration.

When the product meets the spec, tests pass, and it runs locally:
- git init -b main if needed
- git config user.name "Oscar Munoz"
- git config user.email "oscarxxi@gmail.com"
- Do not commit .env, vendor, node_modules
- Commit: feat: add raffle desk
- Public GitHub repo https://github.com/oscarmmi/raffle-desk and push main
- php artisan projects:planned-done 8
- Stop. Print the GitHub URL.

English code. English README. UI copy may be Spanish. No Redis. No fake logos. No real payments.
