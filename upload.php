<?php
session_start(); // 啟動會話
$currentUser = $_SESSION['currentUser'] ?? null; // 從 session 獲取當前用戶資料
$userName = htmlspecialchars($currentUser['name'] ?? $currentUser['account'] ?? '訪客'); // 獲取用戶名稱或帳號

// 如果沒有登入，重定向到登入頁面並顯示提示
if (!$currentUser) {
    echo "<script>alert('請先登入！'); window.location.href='login.html';</script>";
    exit(); // 終止腳本執行，防止後續 HTML 內容發送
}

// 確保只有 'seller' 角色可以上傳書籍
if ($currentUser['role'] !== 'seller') {
    echo "<script>alert('只有賣家才能上傳書籍！'); window.location.href='index.php';</script>";
    exit(); // 終止腳本執行
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>📚 校園二手書交易平台 - 上傳書籍</title>
    <link rel="stylesheet" href="css/normalize.css">
    <style>
        body {
            margin: 0;
            background: #f7f8fa;
            color: #333;
            font-family: 'Noto Sans TC', sans-serif;
        }

        /* Header 樣式 - 與 index.php 保持一致 */
        header {
            background-color: #3f51b5; /* 藍色 */
            color: white; /* 確保 header 內所有文字都是白色 */
            padding: 15px 0; /* 垂直內邊距，水平由 .container 控制 */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2); /* 陰影效果 */
        }

        header .container {
            display: flex;
            justify-content: space-between; /* 標題和導覽列分開左右兩邊 */
            align-items: center;
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px; /* 為 .container 添加水平內邊距 */
        }

        header h1 {
            margin: 0;
            font-size: 28px; /* 放大標題字體 */
            color: white; /* 確保標題文字為白色 */
            text-align: left; /* 靠左對齊 */
            flex-shrink: 0; /* 防止標題縮小 */
        }

        header nav {
            display: flex;
            align-items: center; /* 確保導覽列內的項目垂直居中 */
        }

        header nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            gap: 20px; /* 導航連結間距 */
        }

        header nav ul li a {
            color: white; /* 導航連結文字為白色 */
            text-decoration: none;
            font-weight: bold;
            padding: 5px 10px; /* 與 upload.php 的連結內邊距一致 */
            border-radius: 5px;
            transition: background-color 0.3s ease;
            white-space: nowrap; /* 防止連結文字換行 */
        }

        header nav ul li a:hover {
            background-color: #5c6bc0; /* 懸停效果 */
        }

        /* 導航欄中活動連結樣式 (上傳書籍應該是活動的) */
        header nav ul li.active-nav-link a {
            background-color: #5c6bc0; /* 活動連結使用懸停色或更深的顏色 */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .user-info {
            color: white;
            font-weight: bold;
            margin-left: 20px; /* 與導航列的間距 */
            white-space: nowrap; /* 防止用戶名換行 */
        }
        
        .logout-btn {
            background-color: #f44336; /* 紅色登出按鈕 */
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9em;
            font-weight: bold;
            margin-left: 10px; /* 與用戶名間距 */
            transition: background-color 0.3s ease;
        }

        .logout-btn:hover {
            background-color: #d32f2f;
        }

        /* 以下是原 upload.php 的內容樣式 */
        main {
            padding: 40px 20px;
            max-width: 800px;
            margin: 20px auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #3f51b5;
            margin-bottom: 30px;
            font-size: 2em;
        }

        form {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }

        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group select,
        .form-group textarea {
            width: calc(100% - 20px);
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
            box-sizing: border-box; /* 確保 padding 不增加寬度 */
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .form-group input[type="file"] {
            padding: 5px;
        }

        .image-preview-container {
            text-align: center;
            margin-top: 15px;
        }

        #imagePreview {
            max-width: 100%;
            max-height: 200px;
            border: 1px solid #eee;
            border-radius: 5px;
            display: none; /* 預設隱藏 */
            margin-top: 10px;
        }

        button[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.1em;
            font-weight: bold;
            transition: background-color 0.3s ease;
            width: 100%; /* 按鈕佔滿寬度 */
            margin-top: 20px;
        }

        button[type="submit"]:hover {
            background-color: #45a049;
        }

        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 20px 0;
            margin-top: 40px;
            font-size: 0.9em;
        }

        /* 響應式設計 */
        @media (max-width: 768px) {
            header .container {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
                padding: 0 15px; /* 小螢幕下調整容器內邊距 */
            }
            header nav {
                width: 100%;
                flex-direction: column; /* 導航列在小螢幕上垂直堆疊 */
                align-items: flex-start;
            }
            header nav ul {
                flex-wrap: wrap;
                justify-content: center;
                width: 100%;
                gap: 10px;
            }
            header nav ul li {
                flex-grow: 1;
                text-align: center;
            }
            .user-info {
                margin: 10px 0 0 0; /* 調整用戶信息間距 */
                width: 100%;
                text-align: center;
            }
            .logout-btn {
                margin: 5px auto 0 auto; /* 登出按鈕居中 */
                width: calc(100% - 20px); /* 讓按鈕寬度適應容器 */
            }
            main {
                padding: 20px;
                margin: 15px auto;
                width: 95%;
            }
        }

        @media (max-width: 480px) {
            header h1 {
                font-size: 24px; /* 更小螢幕下標題字體再縮小 */
            }
            header nav ul {
                flex-direction: column;
                gap: 5px;
            }
        }
    </style>
</head>

<body>
    <header>
        <div class="container">
            <h1>📚 校園二手書交易平台</h1>
            <nav>
                <ul>
                    <li><a href="index.php">首頁</a></li>
                    <li class="active-nav-link"><a href="upload.php">上傳書籍</a></li>
                    <li><a href="communicate.php">溝通</a></li>
                    <li><a href="profile.php">個人中心</a></li>
                    <li><a href="TransactionRecords.php">交易紀錄</a></li>
                    <li><a href="evaluate.php">評價</a></li>
                </ul>
                <?php if (isset($_SESSION['currentUser'])): ?>
                    <span class="user-info">歡迎, <?= $userName ?></span>
                    <button class="logout-btn" onclick="location.href='logout.php'">登出</button>
                <?php else: ?>
                    <ul>
                        <li><a href="login.html">登入</a></li>
                        <li><a href="register.html">註冊</a></li>
                    </ul>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main>
        <h2>上傳您的書籍</h2>
        <form id="uploadForm" action="upload_process.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">書名:</label>
                <input type="text" id="title" name="title" required />
            </div>

            <div class="form-group">
                <label for="author">作者:</label>
                <input type="text" id="author" name="author" required />
            </div>

            <div class="form-group">
                <label for="version">版本:</label>
                <input type="text" id="version" name="version" placeholder="例如：第一版、第二版、2023年版" required />
            </div>

            <div class="form-group">
                <label for="department">適用科系:</label>
                <input type="text" id="department" name="department" placeholder="例如：資訊工程系、企業管理學系" required />
            </div>

            <div class="form-group">
                <label for="price">價格 (NTD):</label>
                <input type="number" id="price" name="price" min="0" required />
            </div>

            <div class="form-group">
                <label for="status">書本狀況:</label>
                <select id="status" name="status" required>
                    <option value="全新">全新</option>
                    <option value="良好">良好 (約八成新)</option>
                    <option value="普通">普通 (有筆記或磨損)</option>
                    <option value="可使用">可使用 (明顯損壞但不影響閱讀)</option>
                </select>
            </div>

            <div class="form-group">
                <label for="remark">備註 (選填):</label>
                <textarea id="remark" name="remark" rows="4" placeholder="例如：附有習題詳解、內頁有畫記、書皮輕微磨損..."></textarea>
            </div>

            <div class="form-group">
                <label for="bookImage">書籍圖片:</label>
                <input type="file" id="bookImage" name="bookImage" accept="image/*" />
                <div class="image-preview-container">
                    <img id="imagePreview" src="#" alt="圖片預覽" style="display: none;">
                </div>
            </div>

            <button type="submit">上傳書籍</button>
        </form>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 校園二手書交易平台. All rights reserved.</p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 圖片預覽功能
            document.getElementById('bookImage').addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const imgPreview = document.getElementById('imagePreview');
                        imgPreview.src = e.target.result;
                        imgPreview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                } else {
                    document.getElementById('imagePreview').style.display = 'none';
                    document.getElementById('imagePreview').src = '';
                }
            });

            // 可以在這裡加入表單提交前的客戶端驗證 (如果需要)
            document.getElementById('uploadForm').addEventListener('submit', function(event) {
                // 例如：再次檢查必填欄位 (雖然 input 標籤有 required 屬性，但仍可加強)
                const title = document.getElementById('title').value.trim();
                const author = document.getElementById('author').value.trim();
                const version = document.getElementById('version').value.trim();
                const department = document.getElementById('department').value.trim();
                const price = document.getElementById('price').value.trim();

                if (!title || !author || !version || !department || !price) {
                    alert('請填寫所有必填欄位！');
                    event.preventDefault(); // 阻止表單提交
                }
                // 可以在此處添加更多驗證邏輯，例如價格必須大於0等
            });
        });
    </script>
</body>

</html>