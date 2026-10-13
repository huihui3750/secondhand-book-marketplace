# 二手書交易平台 | Secondhand Book Marketplace

使用 PHP、MySQL、HTML、CSS 與 JavaScript 製作的二手書交易系統，包含會員、書籍刊登、交易、訊息與評價等模組。

## 專案結構

- `app/`：依功能分類的後端處理程式。
- 根目錄 `*.php`：網頁入口及舊路徑相容檔案。
- `assets/`：CSS、JavaScript。
- `database/schema.sql`：資料表結構。
- `docs/FILE_GUIDE.md`：詳細檔案用途。
- `uploads/`：使用者上傳檔案，預設不納入 Git。

## 本機啟動

1. 安裝 XAMPP，啟動 Apache 與 MySQL。
2. 將本資料夾放進 XAMPP `htdocs/`。
3. 在 phpMyAdmin 建立 `second_hand_book` 資料庫，匯入 `database/schema.sql`。
4. 依本機環境設定 `DB_HOST`、`DB_PORT`、`DB_USER`、`DB_PASSWORD`、`DB_NAME`（參考 `.env.example`）。注意：PHP 不會自動讀取 `.env`，需自行於執行環境設定。
5. 開啟 `http://localhost/secondhand-book-marketplace/index.php`。

## 整理說明

保留原本對外 PHP 路徑，將後端處理程式移到 `app/`，以減少既有表單和 AJAX 連結受到影響。尚未完成瀏覽器及資料庫端到端測試；部署前需測試登入、上架、下單、訊息及評價功能。

> 本專案為學習作品，正式公開部署前需再檢查驗證、授權與安全性。
