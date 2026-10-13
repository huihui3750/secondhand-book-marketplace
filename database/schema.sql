-- Schema only. No original user or transaction records.
-- Review compatibility with your MySQL/MariaDB version.

CREATE TABLE `book` (
  `Book_ID` int(10) NOT NULL COMMENT '書本ID',
  `Book_title` varchar(50) NOT NULL COMMENT '書名',
  `Book_author` varchar(10) NOT NULL COMMENT '書本作者',
  `Book_version` varchar(10) NOT NULL COMMENT '書籍版本',
  `Book_department` varchar(50) NOT NULL COMMENT '書本使用科系',
  `Book_price` int(5) NOT NULL COMMENT '價格',
  `Book_status` varchar(50) NOT NULL COMMENT '書況',
  `Book_image_path` varchar(255) NOT NULL COMMENT '書籍封面圖片',
  `Book_remark` varchar(50) NOT NULL COMMENT '備註',
  `Transaction_status` varchar(30) NOT NULL DEFAULT '未售出' COMMENT '書籍交易狀況',
  `S_ID` int(15) NOT NULL COMMENT '賣家ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `buyer` (
  `B_ID` int(10) NOT NULL COMMENT '買家ID',
  `B_name` varchar(15) NOT NULL COMMENT '買家名稱',
  `B_account` varchar(20) NOT NULL COMMENT '買家帳號',
  `B_password` varchar(255) NOT NULL COMMENT '買家密碼',
  `B_email` varchar(20) NOT NULL COMMENT '買家電子郵件',
  `B_department` varchar(20) NOT NULL COMMENT '買家科系',
  `B_telephone` int(15) NOT NULL COMMENT '買家電話',
  `B_personal_profile` varchar(50) NOT NULL COMMENT '買家個人簡介',
  `B_evaluate` int(11) NOT NULL COMMENT '買家評價'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `evaluations` (
  `evaluation_id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `rater_id` int(11) NOT NULL COMMENT '評價人的ID (買家或賣家)',
  `rated_user_id` int(11) NOT NULL COMMENT '被評價人的ID (買家或賣家)',
  `rated_user_role` varchar(50) NOT NULL COMMENT '被評價人的角色 (buyer 或 seller)',
  `rating` int(11) NOT NULL COMMENT '星級評價 (1-5星)',
  `comment` text DEFAULT NULL COMMENT '評價留言',
  `evaluation_date` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '評價時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL COMMENT '訊息ID',
  `sender_id` int(15) NOT NULL COMMENT '發送者ID (買家或賣家)',
  `sender_id_role` varchar(10) NOT NULL COMMENT '發送者角色 (buyer 或 seller)',
  `receiver_id` int(15) NOT NULL COMMENT '接收者ID (買家或賣家)',
  `receiver_id_role` varchar(10) NOT NULL COMMENT '接收者角色 (buyer 或 seller)',
  `book_id` int(10) DEFAULT NULL COMMENT '相關書籍ID (可選)',
  `message_content` text NOT NULL COMMENT '訊息內容',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '發送時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `seller` (
  `S_ID` int(15) NOT NULL COMMENT '賣家ID',
  `S_name` varchar(20) NOT NULL COMMENT '賣家名稱',
  `S_account` varchar(20) NOT NULL COMMENT '賣家帳號',
  `S_password` varchar(255) NOT NULL COMMENT '賣家密碼',
  `S_email` varchar(20) NOT NULL COMMENT '賣家電子郵件',
  `S_department` varchar(10) NOT NULL COMMENT '賣家科系',
  `S_telephone` int(15) NOT NULL COMMENT '賣家電話',
  `S_personal_profile` varchar(50) NOT NULL COMMENT '賣家個人簡介',
  `S_evaluate` int(10) NOT NULL COMMENT '賣家評價'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `transactions` (
  `transaction_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` enum('pending','paid','shipped','completed','cancelled') NOT NULL DEFAULT 'pending',
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `book`
  ADD PRIMARY KEY (`Book_ID`);

ALTER TABLE `buyer`
  ADD PRIMARY KEY (`B_ID`);

ALTER TABLE `evaluations`
  ADD PRIMARY KEY (`evaluation_id`);

ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `idx_sender_receiver` (`sender_id`,`receiver_id`),
  ADD KEY `idx_receiver_sender` (`receiver_id`,`sender_id`),
  ADD KEY `idx_book_id` (`book_id`);

ALTER TABLE `seller`
  ADD PRIMARY KEY (`S_ID`),
  ADD UNIQUE KEY `S_telephone` (`S_telephone`);

ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `fk_transaction_book` (`book_id`),
  ADD KEY `fk_transaction_buyer` (`buyer_id`),
  ADD KEY `fk_transaction_seller` (`seller_id`);

ALTER TABLE `book`
  MODIFY `Book_ID` int(10) NOT NULL AUTO_INCREMENT COMMENT '書本ID', AUTO_INCREMENT=2025019;

ALTER TABLE `evaluations`
  MODIFY `evaluation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '訊息ID', AUTO_INCREMENT=6;

ALTER TABLE `transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

ALTER TABLE `transactions`
  ADD CONSTRAINT `fk_transaction_book` FOREIGN KEY (`book_id`) REFERENCES `book` (`Book_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_transaction_buyer` FOREIGN KEY (`buyer_id`) REFERENCES `buyer` (`B_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_transaction_seller` FOREIGN KEY (`seller_id`) REFERENCES `seller` (`S_ID`) ON UPDATE CASCADE;
