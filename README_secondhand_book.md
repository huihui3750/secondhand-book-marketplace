# 二手書交易平台｜Secondhand Book Marketplace

以 **PHP、MySQL、HTML、CSS 與 JavaScript** 製作的二手書交易網站，提供買家與賣家管理書籍資訊、交易及交流的操作介面。本專案展示網頁前後端整合與關聯式資料庫應用。

## 功能概覽

- **會員功能**：註冊、登入、登出與個人資料管理。
- **書籍功能**：書籍刊登、瀏覽及管理。
- **交易功能**：下單、交易紀錄及交易狀態處理。
- **訊息功能**：買賣雙方訊息交流。
- **評價功能**：交易評價及評價紀錄查詢。

> 上述為依專案程式及檔案整理的功能範圍；重構版本尚未完成所有功能的實際操作測試。

## 使用技術

| 類別 | 技術 |
| --- | --- |
| 前端 | HTML、CSS、JavaScript |
| 後端 | PHP |
| 資料庫 | MySQL／MariaDB |
| 本機環境 | XAMPP、phpMyAdmin |

## 專案目錄

```text
secondhand-book-marketplace/
├── app/
│   ├── auth/           # 會員驗證
│   ├── books/          # 書籍處理
│   ├── messages/       # 訊息處理
│   ├── ratings/        # 評價處理
│   ├── transactions/   # 交易處理
│   └── users/          # 使用者資料
├── assets/
│   ├── css/            # 樣式檔案
│   └── js/             # JavaScript
├── database/
│   └── schema.sql      # 資料庫結構（不含原始使用者資料）
├── docs/
│   └── FILE_GUIDE.md   # 檔案用途說明
├── uploads/            # 使用者上傳檔案（不納入版本控制）
├── db_connection.php   # 資料庫連線
├── index.php           # 網站入口
└── README.md
```

根目錄仍保留部分 PHP 頁面及舊路徑相容入口，降低重構對原本連結的影響。

## 本機執行

1. 安裝 XAMPP，啟動 **Apache** 與 **MySQL**。
2. 將專案資料夾放在 XAMPP 的 `htdocs/` 中。
3. 透過 phpMyAdmin 建立 `second_hand_book` 資料庫，匯入 `database/schema.sql`。
4. 依照 `.env.example` 的欄位設定執行環境變數 `DB_HOST`、`DB_PORT`、`DB_USER`、`DB_PASSWORD`、`DB_NAME`。**PHP 不會自動載入 `.env` 檔案。**
5. 瀏覽 `http://localhost/secondhand-book-marketplace/index.php`。

> 注意：資料庫結構與程式的完整相容性尚待測試；匯入成功不保證所有頁面可正常使用。

## 系統畫面

目前尚未加入正式截圖。建議完成測試後，補上首頁、會員登入、書籍刊登、交易紀錄及訊息頁面，讓瀏覽者更容易了解操作流程。

## 專案狀態

**作品集整理中／尚未完成端到端測試。** 已依功能分類後端處理檔案，並整理前端資源與資料庫結構。正式公開部署前，仍需確認會員驗證、檔案上傳、交易流程、權限控管及資料庫設定的正確性與安全性。

## 其他文件

參閱 [`docs/FILE_GUIDE.md`](docs/FILE_GUIDE.md) 了解各程式檔案的用途。
