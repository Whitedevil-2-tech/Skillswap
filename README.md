# SkillSwap — Peer-to-Peer Skill Barter Board

Trade skills, not money. Built with HTML5, CSS3, vanilla JavaScript, PHP 8 (PDO) and MySQL.

## Features
- Secure registration/login (`password_hash`, sessions, CSRF tokens, prepared statements)
- Post skills you offer and want; delete your listings
- Browse + search board with instant live filter
- ★ Perfect Match badge when two users' offer/want mutually line up
- Swap requests: send, accept, reject (duplicate and self-request protection)
- Responsive layout with automatic dark mode

## Setup (XAMPP/WAMP)
1. Copy this folder to `htdocs/skillswap/`
2. Start Apache and MySQL, open phpMyAdmin, import `database.sql`
3. Check credentials in `config.php`
4. Visit http://localhost/skillswap/

## Test flow
Register two users → each posts a listing → cross-request a swap → accept from the other dashboard.
