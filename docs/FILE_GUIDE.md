# 專案檔案說明

- `index.php`：PHP 主頁面；`index.html` 為保留的舊版靜態頁面。
- `login.html`、`register.html`：會員登入與註冊表單。
- `profile.php`、`seller.php`、`upload.php`：會員、賣家與刊登頁面。
- `communicate.php`：買賣雙方訊息頁面。
- `TransactionRecords.php`：交易紀錄頁面。
- `evaluate.php`、`evaluation_dashboard.php`：評價頁面。
- `app/auth/`：註冊、登入與登出邏輯。
- `app/books/`：書籍刊登及刪除。
- `app/transactions/`：下單、取消、完成與交易查詢。
- `app/messages/`：傳送、取得訊息與聊天對象。
- `app/ratings/`：提交與查詢評價。
- `app/users/`：修改會員資料。
- 專案根目錄同名 PHP 檔案為相容入口，保留原本表單 action / AJAX URL。
- `assets/css/`、`assets/js/`：前端靜態資源。
- `database/schema.sql`：資料庫結構；不包含真實使用者資料。
- `uploads/`：執行時上傳的圖片（Git 忽略）。
- `db_connection.php`：資料庫連線，使用環境變數。

## 注意
原始碼內仍有部分指向不存在的頁面（例如 `messages.php`、`evaluate.html`、`style.css`），本次不任意改變其業務邏輯。上線前應逐頁實測並修正。
