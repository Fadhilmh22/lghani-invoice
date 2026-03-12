# Airlines Logo Upload Feature

## Status: [ ] In Progress

### Steps:
- [x] 1. Generate & implement migration: add_logo_path_to_airlines_table
- [x] 2. Install Intervention Image: composer require intervention/image
- [x] 3. Update AirlinesController.php: handle logo upload/resize/convert PNG, store public/airlines-logo/{CODE}.png
- [x] 4. Update create.blade.php: add logo file input, preview JS, enctype=multipart
- [x] 5. Update edit.blade.php: add logo input + current preview + change logic

- [x] 6. Update index.blade.php: add logo thumbnail column
- [x] 7. Update ticket/print.blade.php: use $airline->logo_path ?: static fallback

- [ ] 8. Run migration & test full flow (create/edit/index/print)
- [ ] 9. Handle edge cases: delete old logo on update, validation errors

**Notes:**
- Logo filename: uppercase airlines_code + .png
- Resize: width 100px (for storage), thumbs auto
- Dir: public/airlines-logo/ (ensure writable)
- Print size: width 35px height auto

