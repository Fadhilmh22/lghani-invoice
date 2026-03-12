# Task: Collapsible Scrollable Balance Display ✅ FIXED

## Summary:
- ✅ `resources/views/master.blade.php`:
  * 2 visible cards always (Lion Group + next)
  * "Lihat semua saldo (X)" toggle button when >2 airlines
  * ✅ FIXED: Click expands **scrollable** section (overflow-y:auto, 400px height → scrollbar appears when needed)
  * Smooth CSS transitions, button rotates chevron (▼→▲)
  * Responsive, works all pages

## Verify Scroll:
1. `php artisan serve`
2. Visit page with >4 airlines (e.g. /airline shows all airlines table → check if toggle + scroll appears)
3. Click toggle → see expanded section with scrollbar if many cards
4. Scroll works inside expanded area

Task complete! Scroll down now visible when expanded + many balances.

