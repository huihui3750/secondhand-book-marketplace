<?php
session_start(); // 啟動會話

// 檢查是否已登入
$currentUser = $_SESSION['currentUser'] ?? null;

// 如果沒有登入，重定向到登入頁面並顯示提示
if (!$currentUser) {
    echo "<script>alert('請先登入才能進入溝通介面！'); window.location.href='login.html';</script>";
    exit(); // 終止腳本執行，防止後續 HTML 內容發送
}

// 如果已登入，可以獲取當前用戶的帳號和 ID
$userAccount = $currentUser['account'];
$userID = $currentUser['id'];
$userRole = $currentUser['role'];
$userName = htmlspecialchars($currentUser['name'] ?? $currentUser['account']); // 獲取用戶名稱或帳號

// 包含資料庫連線檔案
include 'db_connection.php';

$talkToID = null;
$talkToAccount = null;
$relatedBookID = null; // 新增變數用於儲存相關書籍ID
$relatedBookTitle = null; // 新增變數用於儲存相關書籍標題

// 從 URL 參數獲取 seller_id 或 buyer_id (作為 talkToID) 和 book_id
if (isset($_GET['seller_id'])) {
    $talkToID = $_GET['seller_id'];
    $relatedBookID = $_GET['book_id'] ?? null;

    // 查詢 seller 表來獲取賣家帳號和名稱
    $stmt_seller = $conn->prepare("SELECT S_account, S_name FROM seller WHERE S_ID = ?");
    if ($stmt_seller) {
        $stmt_seller->bind_param("i", $talkToID);
        $stmt_seller->execute();
        $result_seller = $stmt_seller->get_result();
        if ($seller_data = $result_seller->fetch_assoc()) {
            $talkToAccount = htmlspecialchars($seller_data['S_name'] ?? $seller_data['S_account']);
        }
        $stmt_seller->close();
    }
} elseif (isset($_GET['buyer_id'])) {
    $talkToID = $_GET['buyer_id'];
    $relatedBookID = $_GET['book_id'] ?? null;

    // 查詢 buyer 表來獲取買家帳號和名稱
    $stmt_buyer = $conn->prepare("SELECT B_account, B_name FROM buyer WHERE B_ID = ?");
    if ($stmt_buyer) {
        $stmt_buyer->bind_param("i", $talkToID);
        $stmt_buyer->execute();
        $result_buyer = $stmt_buyer->get_result();
        if ($buyer_data = $result_buyer->fetch_assoc()) {
            $talkToAccount = htmlspecialchars($buyer_data['B_name'] ?? $buyer_data['B_account']);
        }
        $stmt_buyer->close();
    }
}

// 如果有相關書籍ID，查詢書籍標題
if ($relatedBookID) {
    $stmt_book = $conn->prepare("SELECT Book_title FROM book WHERE Book_ID = ?");
    if ($stmt_book) {
        $stmt_book->bind_param("i", $relatedBookID);
        $stmt_book->execute();
        $result_book = $stmt_book->get_result();
        if ($book_data = $result_book->fetch_assoc()) {
            $relatedBookTitle = htmlspecialchars($book_data['Book_title']);
        }
        $stmt_book->close();
    }
}

// 關閉資料庫連線
$conn->close();
?>

<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>📚 校園二手書交易平台 - 溝通</title>
    <link rel="stylesheet" href="css/normalize.css">
    <style>
        body {
            margin: 0;
            background: #f4f7f6;
            color: #333;
            font-family: 'Arial', sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
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
            padding: 5px 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            white-space: nowrap; /* 防止連結文字換行 */
        }

        header nav ul li a:hover {
            background-color: #5c6bc0; /* 懸停效果 */
        }

        /* 導航欄中活動連結樣式 (溝通應該是活動的) */
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

        /* Main content styles */
        .main-wrapper {
            display: flex;
            flex-grow: 1;
            padding: 20px;
            gap: 20px;
            max-width: 1200px;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            min-height: 70vh; /* 確保內容區域至少佔用一定高度 */
        }

        .chat-users-sidebar {
            flex: 0 0 250px; /* 固定寬度 */
            background-color: #f0f2f5;
            border-right: 1px solid #e0e0e0;
            padding: 15px;
            border-radius: 8px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .chat-users-sidebar h3 {
            margin-top: 0;
            color: #3f51b5;
            font-size: 1.2em;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .user-item {
            display: flex;
            align-items: center;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.2s ease;
            background-color: #ffffff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .user-item:hover,
        .user-item.active {
            background-color: #e8eaf6;
            /* Light blue for active/hover */
        }

        .user-item-content {
            flex-grow: 1;
        }

        .user-item h4 {
            margin: 0;
            font-size: 1em;
            color: #333;
        }

        .user-item p {
            margin: 0;
            font-size: 0.8em;
            color: #666;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .chat-area {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
        }

        .chat-header {
            background-color: #3f51b5;
            color: white;
            padding: 15px;
            font-size: 1.2em;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .chat-header .book-title {
            font-size: 0.9em;
            font-weight: normal;
            opacity: 0.8;
        }

        .messages-container {
            flex-grow: 1;
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 10px;
            background-color: #fdfdfe; /* Slightly off-white for message area */
        }

        .message-bubble {
            max-width: 70%;
            padding: 10px 15px;
            border-radius: 18px;
            line-height: 1.5;
            word-wrap: break-word; /* 確保長單詞也能換行 */
        }

        .message-bubble.sent {
            align-self: flex-end;
            background-color: #e8eaf6;
            /* Light blue for sent messages */
            color: #333;
            border-bottom-right-radius: 2px;
        }

        .message-bubble.received {
            align-self: flex-start;
            background-color: #e0e0e0;
            /* Light grey for received messages */
            color: #333;
            border-bottom-left-radius: 2px;
        }

        .message-time {
            font-size: 0.7em;
            color: #888;
            margin-top: 5px;
            text-align: right;
        }

        .message-bubble.received .message-time {
            text-align: left;
        }

        .chat-input-area {
            display: flex;
            padding: 15px;
            border-top: 1px solid #e0e0e0;
            background-color: #f0f2f5;
        }

        .chat-input-area input[type="text"] {
            flex-grow: 1;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 20px;
            outline: none;
            font-size: 1em;
            margin-right: 10px;
        }

        .chat-input-area button {
            background-color: #3f51b5;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease;
        }

        .chat-input-area button:hover {
            background-color: #303f9f;
        }

        .no-chat-selected {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-grow: 1;
            color: #888;
            font-size: 1.2em;
        }
        
        .no-users-message {
            color: #777;
            text-align: center;
            padding: 20px;
        }

        /* Footer 樣式 */
        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 20px 0;
            margin-top: auto; /* 將 footer 推到底部 */
            font-size: 0.9em;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            header .container {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
                padding: 0 15px;
            }
            header nav {
                width: 100%;
                flex-direction: column;
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
                margin: 10px 0 0 0;
                width: 100%;
                text-align: center;
            }
            .logout-btn {
                margin: 5px auto 0 auto;
                width: calc(100% - 20px);
            }

            .main-wrapper {
                flex-direction: column;
                padding: 15px;
                margin: 15px auto;
                width: 95%;
            }

            .chat-users-sidebar {
                flex: none; /* 取消固定寬度 */
                width: 100%;
                border-right: none;
                border-bottom: 1px solid #e0e0e0;
                order: 2; /* 讓側邊欄在手機上顯示在下方 */
            }

            .chat-area {
                order: 1; /* 讓聊天區在手機上顯示在上方 */
            }
        }

        @media (max-width: 480px) {
            header h1 {
                font-size: 24px;
            }
            header nav ul {
                flex-direction: column;
                gap: 5px;
            }
            .chat-input-area {
                flex-direction: column;
            }
            .chat-input-area input[type="text"] {
                margin-right: 0;
                margin-bottom: 10px;
                width: 100%;
            }
            .chat-input-area button {
                width: 100%;
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
                    <li><a href="upload.php">上傳書籍</a></li>
                    <li class="active-nav-link"><a href="communicate.php">溝通</a></li>
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

    <div class="main-wrapper">
        <div class="chat-users-sidebar">
            <h3>您的聊天對象</h3>
            <div id="chatUsersList">
                <p class="no-users-message">載入中...</p>
            </div>
        </div>

        <div class="chat-area">
            <?php if ($talkToID): ?>
                <div class="chat-header">
                    <span>與 <?= $talkToAccount ?> 的對話</span>
                    <?php if ($relatedBookTitle): ?>
                        <span class="book-title">關於書籍: <?= $relatedBookTitle ?></span>
                    <?php endif; ?>
                </div>
                <div class="messages-container" id="messagesContainer">
                    <p class="no-chat-selected">載入訊息中...</p>
                </div>
                <div class="chat-input-area">
                    <input type="text" id="messageInput" placeholder="輸入訊息..." />
                    <button onclick="sendMessage()">發送</button>
                </div>
            <?php else: ?>
                <div class="no-chat-selected">
                    請從左側列表選擇一個對話。
                </div>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <div class="container">
            <p>&copy; 2025 校園二手書交易平台. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // 從 PHP 獲取當前用戶和預選的聊天對象/書籍資訊
        const currentUserID = <?= json_encode($userID); ?>;
        const currentUserRole = <?= json_encode($userRole); ?>;
        const userAccount = <?= json_encode($userAccount); ?>; // 用於 JavaScript 顯示

        let selectedChatUserID = <?= json_encode($talkToID); ?>;
        let selectedChatUserAccount = <?= json_encode($talkToAccount); ?>;
        let selectedRelatedBookID = <?= json_encode($relatedBookID); ?>;

        // 載入聊天對象列表
        async function fetchChatUsers() {
            try {
                const response = await fetch('get_chat_users.php');
                const data = await response.json();
                const chatUsersList = document.getElementById('chatUsersList');
                chatUsersList.innerHTML = ''; // 清空現有內容

                if (data.success && data.chat_users.length > 0) {
                    data.chat_users.forEach(user => {
                        const userItem = document.createElement('div');
                        userItem.className = 'user-item';
                        // 添加 data 屬性以便 JavaScript 獲取
                        userItem.dataset.talkToId = user.id;
                        userItem.dataset.talkToAccount = user.account;
                        userItem.dataset.relatedBookId = user.book_id;

                        // 判斷是否為預選的對話，如果是則添加 active 類
                        if (selectedChatUserID == user.id && selectedRelatedBookID == user.book_id) {
                            userItem.classList.add('active');
                        }

                        userItem.innerHTML = `
                            <div class="user-item-content">
                                <h4>${user.account}</h4>
                                <p>關於：${user.book_title}</p>
                            </div>
                        `;
                        userItem.onclick = () => selectChat(user.id, user.account, user.book_id, user.book_title);
                        chatUsersList.appendChild(userItem);
                    });
                } else {
                    chatUsersList.innerHTML = '<p class="no-users-message">目前沒有任何對話。</p>';
                }
            } catch (error) {
                console.error("Error fetching chat users:", error);
                const chatUsersList = document.getElementById('chatUsersList');
                chatUsersList.innerHTML = '<p class="no-users-message">載入對話列表失敗。</p>';
            }
        }

        // 選擇聊天對象並載入訊息
        function selectChat(talkToId, talkToAccount, bookId, bookTitle) {
            selectedChatUserID = talkToId;
            selectedChatUserAccount = talkToAccount;
            selectedRelatedBookID = bookId;

            // 更新 URL 參數
            const url = new URL(window.location.href);
            url.searchParams.set(currentUserRole === 'buyer' ? 'seller_id' : 'buyer_id', talkToId);
            url.searchParams.set('book_id', bookId);
            window.history.pushState({}, '', url);

            // 更新聊天標頭
            const chatHeader = document.querySelector('.chat-header');
            const chatArea = document.querySelector('.chat-area');
            const messagesContainer = document.getElementById('messagesContainer');
            const chatInputArea = document.querySelector('.chat-input-area');

            if (chatHeader) {
                chatHeader.innerHTML = `
                    <span>與 ${selectedChatUserAccount} 的對話</span>
                    ${bookTitle ? `<span class="book-title">關於書籍: ${bookTitle}</span>` : ''}
                `;
            } else {
                // 如果 chat-header 不存在，則創建它和輸入區域
                chatArea.innerHTML = `
                    <div class="chat-header">
                        <span>與 ${selectedChatUserAccount} 的對話</span>
                        ${bookTitle ? `<span class="book-title">關於書籍: ${bookTitle}</span>` : ''}
                    </div>
                    <div class="messages-container" id="messagesContainer">
                        <p class="no-chat-selected">載入訊息中...</p>
                    </div>
                    <div class="chat-input-area">
                        <input type="text" id="messageInput" placeholder="輸入訊息..." />
                        <button onclick="sendMessage()">發送</button>
                    </div>
                `;
                // 重新獲取新的 messagesContainer 和 messageInput
                const newMessagesContainer = document.getElementById('messagesContainer');
                const newMessageInput = document.getElementById('messageInput');
                newMessageInput.addEventListener("keypress", (e) => {
                    if (e.key === "Enter") {
                        sendMessage();
                    }
                });
            }

            // 清空舊訊息
            document.getElementById('messagesContainer').innerHTML = '<p class="no-chat-selected">載入訊息中...</p>';
            
            // 重新載入訊息
            fetchMessages();

            // 更新側邊欄活躍狀態
            document.querySelectorAll('.user-item').forEach(item => {
                item.classList.remove('active');
            });
            const activeItem = document.querySelector(`.user-item[data-talk-to-id="${talkToId}"][data-related-book-id="${bookId}"]`);
            if (activeItem) {
                activeItem.classList.add('active');
            }
        }

        // 載入特定對話的訊息
        async function fetchMessages() {
            if (!selectedChatUserID || !selectedRelatedBookID) {
                document.getElementById('messagesContainer').innerHTML = '<p class="no-chat-selected">請從左側列表選擇一個對話。</p>';
                return;
            }

            try {
                const response = await fetch(`get_messages.php?talk_to_id=${selectedChatUserID}&book_id=${selectedRelatedBookID}`);
                const data = await response.json();
                const messagesContainer = document.getElementById('messagesContainer');
                messagesContainer.innerHTML = ''; // 清空現有訊息

                if (data.success && data.messages.length > 0) {
                    data.messages.forEach(msg => {
                        const messageBubble = document.createElement('div');
                        messageBubble.className = 'message-bubble ' + (msg.sender_id == currentUserID ? 'sent' : 'received');
                        const messageText = document.createElement('p');
                        messageText.textContent = msg.message_content;
                        const messageTime = document.createElement('div');
                        messageTime.className = 'message-time';
                        messageTime.textContent = new Date(msg.timestamp).toLocaleString();

                        messageBubble.appendChild(messageText);
                        messageBubble.appendChild(messageTime);
                        messagesContainer.appendChild(messageBubble);
                    });
                    // 滾動到最新訊息
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                } else {
                    messagesContainer.innerHTML = '<p class="no-chat-selected">沒有訊息，開始您的對話吧！</p>';
                }
            } catch (error) {
                console.error("Error fetching messages:", error);
                document.getElementById('messagesContainer').innerHTML = '<p class="no-chat-selected">載入訊息失敗。</p>';
            }
        }

        // 發送訊息
        async function sendMessage() {
            const messageInput = document.getElementById('messageInput');
            const messageContent = messageInput.value.trim();

            if (!messageContent || !selectedChatUserID || !selectedRelatedBookID) {
                alert("請輸入訊息並選擇聊天對象。");
                return;
            }

            try {
                const response = await fetch('send_message.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        receiver_id: selectedChatUserID,
                        book_id: selectedRelatedBookID,
                        message_content: messageContent,
                        sender_role: currentUserRole
                    }),
                });

                const data = await response.json();

                if (data.success) {
                    messageInput.value = ''; // 清空輸入框
                    fetchMessages(); // 重新載入訊息以顯示新發送的訊息
                } else {
                    alert("發送訊息失敗: " + data.message);
                }
            } catch (error) {
                console.error("發送過程中發生錯誤！", error);
                alert("發送過程中發生錯誤！");
            }
        }

        // 監聽 Enter 鍵發送訊息
        document.addEventListener("DOMContentLoaded", () => {
            const messageInput = document.getElementById("messageInput");
            if (messageInput) {
                messageInput.addEventListener("keypress", (e) => {
                    if (e.key === "Enter") {
                        sendMessage();
                    }
                });
            }
            
            // 頁面載入時執行獲取聊天對象和訊息
            fetchChatUsers();
            if (selectedChatUserID && selectedRelatedBookID) {
                fetchMessages();
            }
            // 每 5 秒刷新一次訊息 (可以根據需求調整或改為 WebSocket)
            setInterval(fetchMessages, 5000);
        });
    </script>
</body>

</html>