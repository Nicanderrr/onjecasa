

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";



CREATE TABLE `invoice` (
  `SID` int(11) NOT NULL,
  `order_code` varchar(255) NOT NULL,
  `cname` varchar(255) NOT NULL,
  `GRAND_TOTAL` double(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;



INSERT INTO `invoice` (`SID`, `order_code`, `cname`, `GRAND_TOTAL`, `created_at`) VALUES
(94, 'OPTG-4270', 'fda502aa067ee6', 60.00, '2023-01-09 13:39:25'),
(95, 'GIRZ-4709', 'c1ae19ee026e27', 120.00, '2023-01-09 15:27:03'),
(96, 'CGBR-7560', 'dd03baa4eba807', 711.00, '2023-01-09 15:43:56'),
(97, 'DJCW-9450', '6fbcf5c17ce5a6', 120.00, '2023-01-09 15:52:03'),
(98, 'QEHM-2504', '26bb83dafd27b6', 2500.00, '2023-01-09 15:55:25'),
(99, 'OLVP-6813', 'cd7874e323b2d9', 2500.00, '2023-01-09 15:55:42'),
(100, 'IMHJ-5631', '2088ad21a58cb7', 110000.00, '2023-01-09 15:56:09'),
(101, 'PVTU-0164', '211181ee73eade', 7042.00, '2023-01-09 16:37:46'),
(102, 'SQLX-7908', '9f52b633ed4299', 7865.00, '2023-01-09 18:27:58'),
(103, 'BPQO-2130', 'c2b7a7ac7a40cc', 5120.00, '2023-01-10 07:43:29'),
(104, 'ULJC-5206', 'b001bf451357eb', 11760.00, '2023-01-10 18:49:18'),
(105, 'ZOAQ-1654', '8f31b70fb9824c', 360.00, '2023-01-10 23:36:09'),
(106, 'SDFY-7235', '4d58314505660b', 60.00, '2023-01-10 23:44:07'),
(107, 'XDTC-0427', 'ec6065f38a987b', 2512.00, '2023-01-11 09:07:52'),
(108, 'BZKT-9063', '9db84b7f9da72f', 20.00, '2023-01-11 09:25:20'),
(109, 'XIUO-2019', '11c48dd87ed3e6', 36.00, '2023-01-11 09:27:07'),
(110, 'UMBN-5206', 'ffe8c5c966fb98', 480.00, '2023-01-11 22:28:44'),
(111, 'PWGZ-1708', 'cbbd1b3657b921', 5000.00, '2023-01-11 12:47:01'),
(112, 'VSWG-6140', 'Evans', 150.00, '2023-01-11 13:37:21'),
(113, 'ZIYX-5368', '5fbb05323e555c', 6760.00, '2023-01-11 14:23:34'),
(114, 'TJRM-7185', '0d920361e17dca', 360.00, '2023-01-12 12:39:23'),
(115, 'YVMP-3894', 'Evans', 865.00, '2023-01-12 12:46:35'),
(116, 'DCOL-1328', 'a766c88ae8c204', 150.00, '2023-01-12 12:48:02'),
(117, 'MDIZ-8790', '4d5e3244efc2b2', 180.00, '2023-01-12 12:48:23'),
(118, 'YLUQ-1946', '3b0ce1ea0b4269', 180.00, '2023-01-12 12:49:56'),
(119, 'LYUA-6395', '9046475c4fdad9', 5150.00, '2023-01-12 12:59:10'),
(120, 'WONY-5920', '6327a0d6c2a4b2', 120.00, '2023-01-12 15:07:48');



CREATE TABLE `invoice_products` (
  `ID` int(11) NOT NULL,
  `SID` varchar(255) NOT NULL,
  `PNAME` varchar(100) NOT NULL,
  `PRICE` double(10,2) NOT NULL,
  `QTY` int(11) NOT NULL,
  `order_status` varchar(255) NOT NULL,
  `TOTAL` double(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;



INSERT INTO `invoice_products` (`ID`, `SID`, `PNAME`, `PRICE`, `QTY`, `order_status`, `TOTAL`, `created_at`) VALUES
(175, 'PF2BODHEYZ', 'Camera', 6700.00, 1, 'Paid', 6700.00, '2023-01-09 09:42:41'),
(176, 'PF2BODHEYZ', 'Original Shirt', 120.00, 1, 'Paid', 120.00, '2023-01-09 09:42:41'),
(177, 'RLIB9HCYAM', 'pa', 2500.00, 1, 'Paid', 2500.00, '2023-01-09 08:06:15'),
(178, 'TRNPEM4YH9', 'Original Shirt', 120.00, 2, 'Paid', 240.00, '2023-01-09 11:09:25'),
(181, 'HSWG27B95R', 'Original Shirt', 120.00, 3, 'Paid', 360.00, '2023-01-09 12:23:20'),
(182, 'DKGY8BHVEC', 'Pepsi', 20.00, 3, 'Paid', 60.00, '2023-01-09 13:39:36'),
(183, 'XK8YTUO3ZH', 'Original Shirt', 120.00, 1, 'Paid', 120.00, '2023-01-10 07:42:50'),
(184, 'V8REJ4XYUW', 'welch', 111.00, 1, 'Paid', 111.00, '2023-01-10 07:41:52'),
(185, 'V8REJ4XYUW', 'Original Shirt', 120.00, 1, 'Paid', 120.00, '2023-01-10 07:41:52'),
(186, 'V8REJ4XYUW', 'Swang', 120.00, 4, 'Paid', 480.00, '2023-01-10 07:41:52'),
(187, 'IGBY1WL32Z', 'Original Shirt', 120.00, 1, 'Paid', 120.00, '2023-01-10 07:42:33'),
(188, 'QEHM-2504', 'Series 7', 2500.00, 1, 'Paid', 2500.00, '2023-01-09 18:35:15'),
(189, 'OLVP-6813', 'Products', 2500.00, 1, 'Paid', 2500.00, '2023-01-09 17:57:41'),
(190, 'IMHJ-5631', 'pa', 2500.00, 44, 'Paid', 110000.00, '2023-01-09 15:56:26'),
(191, 'PVTU-0164', 'welch', 111.00, 2, 'Paid', 222.00, '2023-01-09 16:38:01'),
(192, 'PVTU-0164', 'Original Shirt', 120.00, 1, 'Paid', 120.00, '2023-01-09 16:38:01'),
(193, 'PVTU-0164', 'Camera', 6700.00, 1, 'Paid', 6700.00, '2023-01-09 16:38:01'),
(194, 'SQLX-7908', 'welch', 111.00, 3, 'Paid', 333.00, '2023-01-09 18:28:55'),
(195, 'SQLX-7908', 'pa', 2500.00, 3, 'Paid', 7500.00, '2023-01-09 18:28:55'),
(196, 'SQLX-7908', 'Pepsi', 20.00, 1, 'Paid', 20.00, '2023-01-09 18:28:55'),
(197, 'SQLX-7908', 'Glass', 12.00, 1, 'Paid', 12.00, '2023-01-09 18:28:55'),
(198, 'BPQO-2130', 'pa', 2500.00, 2, 'Paid', 5000.00, '2023-01-10 07:45:12'),
(199, 'BPQO-2130', 'Swang', 120.00, 1, 'Paid', 120.00, '2023-01-10 07:45:12'),
(200, 'ULJC-5206', 'Camera', 6700.00, 1, 'Paid', 6700.00, '2023-01-10 18:50:45'),
(201, 'ULJC-5206', 'pa', 2500.00, 2, 'Paid', 5000.00, '2023-01-10 18:50:45'),
(202, 'ULJC-5206', 'Vacumn Bottle', 60.00, 1, 'Paid', 60.00, '2023-01-10 18:50:45'),
(203, 'ZOAQ-1654', 'Coco Channel', 150.00, 2, 'Paid', 300.00, '2023-01-10 23:38:43'),
(204, 'ZOAQ-1654', 'Vacumn Bottle', 60.00, 1, 'Paid', 60.00, '2023-01-10 23:38:43'),
(205, 'SDFY-7235', 'Vacumn Bottle', 60.00, 1, 'Paid', 60.00, '2023-01-10 23:44:18'),
(206, 'XDTC-0427', 'Glass', 12.00, 1, 'Paid', 12.00, '2023-01-11 09:08:04'),
(207, 'XDTC-0427', 'Series 7', 2500.00, 1, 'Paid', 2500.00, '2023-01-11 09:08:04'),
(208, 'BZKT-9063', 'Pepsi', 20.00, 1, 'Paid', 20.00, '2023-01-11 09:25:27'),
(209, 'XIUO-2019', 'Glass', 12.00, 3, 'Paid', 36.00, '2023-01-11 09:27:14'),
(210, 'UMBN-5206', 'Original Shirt', 120.00, 4, 'Paid', 480.00, '2023-01-11 22:28:53'),
(211, 'PWGZ-1708', 'Series 7', 2500.00, 2, 'Paid', 5000.00, '2023-01-11 12:47:13'),
(215, 'TJRM-7185', 'Coco Channel', 150.00, 2, 'Paid', 300.00, '2023-01-12 12:48:57'),
(216, 'TJRM-7185', 'Vacumn Bottle', 60.00, 1, 'Paid', 60.00, '2023-01-12 12:48:57'),
(221, 'DCOL-1328', 'Coco Channel', 150.00, 1, '', 150.00, '2023-01-12 12:48:02'),
(222, 'MDIZ-8790', 'Vacumn Bottle', 60.00, 3, '', 180.00, '2023-01-12 12:48:23'),
(223, 'YLUQ-1946', 'Vacumn Bottle', 60.00, 3, 'Paid', 180.00, '2023-01-12 12:50:08'),
(224, 'LYUA-6395', 'Series 7', 2500.00, 2, 'Paid', 5000.00, '2023-01-12 12:59:34'),
(225, 'LYUA-6395', 'Coco Channel', 150.00, 1, 'Paid', 150.00, '2023-01-12 12:59:34'),
(226, 'WONY-5920', 'Vacumn Bottle', 60.00, 2, '', 120.00, '2023-01-12 15:07:48');



CREATE TABLE `rpos_admin` (
  `admin_id` varchar(200) NOT NULL,
  `admin_name` varchar(200) NOT NULL,
  `admin_email` varchar(200) NOT NULL,
  `admin_password` varchar(200) NOT NULL,
  `admin_pincode` varchar(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;



INSERT INTO `rpos_admin` (`admin_id`, `admin_name`, `admin_email`, `admin_password`, `admin_pincode`) VALUES
('10e0b6dc958adfb5b094d8935a13aeadbe783c25', 'Evans Danso', 'admin@mail.com', 'Enter2net', '2222');




CREATE TABLE `rpos_cart` (
  `cart_id` int(11) NOT NULL,
  `order_code` varchar(255) NOT NULL,
  `prod_id` varchar(200) NOT NULL,
  `prod_name` varchar(200) NOT NULL,
  `prod_code` varchar(255) NOT NULL,
  `prod_price` varchar(200) NOT NULL,
  `prod_qty` varchar(200) NOT NULL,
  `prod_img` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `rpos_cart` (`cart_id`, `order_code`, `prod_id`, `prod_name`, `prod_code`, `prod_price`, `prod_qty`, `prod_img`, `created_at`) VALUES
(69, '', '', 'Logo', 'WJAO-5703', '343', '1', 'logo.png', '2023-01-07 19:44:56'),
(70, '', '', 'Coco Channel', 'ZDGW-6317', '150', '1', 'Cocochannel.avif', '2023-01-07 19:44:59'),
(71, '', '', 'Series 7', 'LXDI-2364', '2500', '1', 'series 7.avif', '2023-01-07 19:45:03'),
(72, '', '', 'Pepsi', 'IYLS-2953', '20', '1', 'pepsi.avif', '2023-01-07 19:45:13');



CREATE TABLE `rpos_categories` (
  `catg_id` int(11) NOT NULL,
  `catg_code` varchar(255) NOT NULL,
  `catg_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



INSERT INTO `rpos_categories` (`catg_id`, `catg_code`, `catg_name`) VALUES
(3, 'TXSM-7194', 'Cosmetics'),
(4, 'XBYP-7649', 'Clothing'),
(6, 'FPYJ-5163', 'Devices & Accessories'),
(7, 'SMHK-3718', 'Electronics'),
(8, 'QFIV-3869', 'Utensils'),
(9, 'GOTX-2179', 'Food &Drink'),
(10, 'YZXU-0386', 'Detergents'),
(11, 'VATJ-9803', 'Medication');



CREATE TABLE `rpos_customers` (
  `customer_id` varchar(200) NOT NULL,
  `customer_name` varchar(200) NOT NULL,
  `customer_phoneno` varchar(200) NOT NULL,
  `customer_email` varchar(200) NOT NULL,
  `customer_password` varchar(200) NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;



INSERT INTO `rpos_customers` (`customer_id`, `customer_name`, `customer_phoneno`, `customer_email`, `customer_password`, `created_at`) VALUES
('0b0d49b9e894', 'Sam Kwaakye', '0232323232', '', '', '2023-01-02 20:47:57.288881'),
('10a9b14f298e', 'Leonard Teiko Lartey', '0550323122', '', '', '2022-12-29 14:58:36.028840'),
('2260cea03ee8', 'Peter Drury', '34304394304', '', '', '2022-12-28 13:41:30.582267'),
('23678cf139aa', 'Elvis Danso', '0549878123', '', '', '2023-01-02 21:31:57.069819'),
('3222a6adec05', 'Emmanuel k Asante', '34304394304', 'rags@gmail.com', '13dc56b84130bcb0d9201ed4a485a2005f6bc634', '2022-12-29 14:58:50.315654'),
('7d044aa1fd98', 'Yaw Asamoah', '0242338237', '', '', '2022-12-29 16:01:27.310297'),
('9398d78690e5', 'Bill Clinton', '0292032343', '', '', '2022-12-29 15:05:33.571686'),
('9ce7e53fcd17', 'Nicander Mensah', '02202323233', '', '', '2022-12-29 14:56:55.176700'),
('a77067bcc260', 'Samuel Ankrah', '03292032', '', '', '2022-12-29 15:05:16.424600'),
('b364bfcce828', 'Evans Atta Danso', '34304394304', '', '', '2022-12-29 14:58:00.147176'),
('fc51fd287772', 'Klyne Green', '2039232323', '', '', '2022-12-29 15:06:02.305332');


CREATE TABLE `rpos_order` (
  `order_id` varchar(255) NOT NULL,
  `order_code` varchar(255) NOT NULL,
  `prod_name` varchar(255) NOT NULL,
  `prod_price` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



INSERT INTO `rpos_order` (`order_id`, `order_code`, `prod_name`, `prod_price`, `created_at`) VALUES
('6a3150ecf9', 'BCYS-5367', 'Vacumn Bottle (4) , Logo (2) , Series 7 (1) , Original Shirt (1) , Coco Channel (2) , Pepsi (1) , welch (1) , Swang (6) ', '4697', '2023-01-05 20:16:13'),
('12b23af0a3', 'LNTX-6715', 'Vacumn Bottle (4) , Logo (2) , Series 7 (1) , Original Shirt (1) , Coco Channel (2) , Pepsi (1) , welch (1) , Swang (6) ', '4697', '2023-01-05 20:17:38'),
('4f07838c7e', 'JQYP-4369', 'Vacumn Bottle (4) , Logo (2) , Series 7 (1) , Original Shirt (1) , Coco Channel (2) , Pepsi (1) , welch (1) , Swang (6) ', '4697', '2023-01-05 20:18:59'),
('56444bce8f', 'GPBF-6203', 'Vacumn Bottle (4) , Logo (2) , Series 7 (1) , Original Shirt (1) , Coco Channel (2) , Pepsi (1) , welch (1) , Swang (6) ', '4697', '2023-01-05 20:19:42'),
('b55134f1ad', 'NIXO-2836', 'Vacumn Bottle (4) , Logo (2) , Series 7 (1) , Original Shirt (1) , Coco Channel (2) , Pepsi (1) , welch (1) , Swang (6) ', '4697', '2023-01-05 20:21:28'),
('262eac16fc', 'RCLM-9708', 'Vacumn Bottle (4) , Logo (2) , Series 7 (1) , Original Shirt (1) , Coco Channel (2) , Pepsi (1) ', '3866', '2023-01-05 20:23:00'),
('00fd117271', 'NEIP-0836', 'Vacumn Bottle (4) , Logo (2) , Series 7 (1) , Original Shirt (1) , Coco Channel (2) , Pepsi (1) ', '3866', '2023-01-05 20:23:44'),
('075cdfc7df', 'STFQ-0281', 'Vacumn Bottle (4) , Logo (2) , Series 7 (1) , Original Shirt (1) , Coco Channel (2) , Pepsi (1) ', '3866', '2023-01-05 20:26:35'),
('c3d0f31823', 'PJXK-4038', 'Logo (1) , Coco Channel (1) , pa (1) , Camera (1) ', '9693', '2023-01-05 20:32:39'),
('5d773fc2f9', 'PSHO-0319', 'Logo (1) , Coco Channel (1) , pa (1) , Camera (1) ', '9693', '2023-01-05 20:33:27'),
('dd5308ab22', 'XGAO-4120', 'Logo (1) , Coco Channel (1) , pa (1) , Camera (1) ', '9693', '2023-01-05 20:35:54'),
('c198c5fc19', 'JDSC-8532', 'Logo (1) , Coco Channel (1) , pa (1) , Camera (1) , Pepsi (1) ', '9713', '2023-01-06 20:07:31'),
('e9c07f991b', 'LVCI-4583', 'Logo (1) , Vacumn Bottle (1) , Coco Channel (1) ', '553', '2023-01-07 19:43:45'),
('333826257a', 'CRFK-0593', 'Logo (1) , Coco Channel (1) , Series 7 (1) , Pepsi (1) ', '3013', '2023-01-07 19:45:38');



CREATE TABLE `rpos_orders` (
  `order_id` varchar(200) NOT NULL,
  `order_code` varchar(200) NOT NULL,
  `customer_id` varchar(200) NOT NULL,
  `customer_name` varchar(200) NOT NULL,
  `prod_id` varchar(200) NOT NULL,
  `prod_name` varchar(200) NOT NULL,
  `prod_price` varchar(200) NOT NULL,
  `prod_qty` varchar(200) NOT NULL,
  `order_status` varchar(200) NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;


INSERT INTO `rpos_orders` (`order_id`, `order_code`, `customer_id`, `customer_name`, `prod_id`, `prod_name`, `prod_price`, `prod_qty`, `order_status`, `created_at`) VALUES
('0acf81543e', 'IEDV-9618', '10a9b14f298e', 'Leonard Teiko Lartey', '2694df619c', 'Vacumn Bottle', '60', '1', 'Paid', '2022-12-30 21:10:11.152027'),
('0ad7f7bb2a', 'XDJT-7506', '9398d78690e5', 'Bill Clinton', '771854045a', 'Original Shirt', '120', '1', 'Paid', '2022-12-31 09:08:36.495264'),
('0cbd132cee', 'OYHD-0354', 'b364bfcce828', 'Evans Atta Danso', '2694df619c', 'Vacumn Bottle', '60', '2', 'Paid', '2023-01-03 18:07:22.970225'),
('16b7a7fc93', 'DXUJ-1769', 'b364bfcce828', 'Evans Atta Danso', '771854045a', 'Original Shirt', '120', '3', 'Paid', '2022-12-31 07:49:49.809393'),
('29d96f0a8e', 'NKMG-6351', '10a9b14f298e', 'Leonard Teiko Lartey', '236a66e00e', 'Logo', '343', '1', 'Paid', '2023-01-05 20:28:29.853948'),
('317456591b', 'WATS-1965', 'b364bfcce828', 'Evans Atta Danso', '35160acc4e', 'Series 7', '2500', '1', 'Paid', '2022-12-29 15:56:58.763401'),
('34bb962872', 'SFOL-1379', '10a9b14f298e', 'Leonard Teiko Lartey', '34a08304d4', 'Coco Channel', '150', '1', 'Paid', '2023-01-01 18:19:15.217053'),
('53026d6fb9', 'BWIO-2731', 'a77067bcc260', 'Samuel Ankrah', 'e47eb3666a', 'Aspirin', '125', '1', '', '2023-01-09 15:45:35.647303'),
('6474862bb0', 'NGDA-5816', '7d044aa1fd98', 'Yaw Asamoah', 'e47eb3666a', 'Aspirin', '125', '1', '', '2023-01-08 21:16:04.204618'),
('6c399bba5b', 'ZWGN-3012', '23678cf139aa', 'Elvis Danso', '2694df619c', 'Vacumn Bottle', '60', '1', 'Paid', '2023-01-02 21:32:59.630261'),
('756e7c1dc2', 'TKLP-9846', '9ce7e53fcd17', 'Nicander Mensah', '906f66277d', 'Pepsi', '20', '1', 'Paid', '2022-12-31 09:08:44.361596'),
('8cd6f9040c', 'VZTG-7921', 'b364bfcce828', 'Evans Atta Danso', 'c103db87be', 'Swang', '120', '3', 'Paid', '2023-01-04 20:07:11.856810'),
('adb9fabec4', 'LQTV-7695', '7d044aa1fd98', 'Yaw Asamoah', '236a66e00e', 'Logo', '343', '1', 'Paid', '2023-01-08 14:04:26.104737'),
('b4da3b72ec', 'OVQK-5417', '23678cf139aa', 'Elvis Danso', '72864e1a32', 'Glass', '12', '1', 'Paid', '2023-01-07 10:20:11.935279'),
('c09bca385f', 'VJTY-3596', '2260cea03ee8', 'Peter Drury', '771854045a', 'Original Shirt', '120', '5', 'Paid', '2022-12-29 15:57:21.453059'),
('c1edc4d51b', 'RIBN-6792', '10a9b14f298e', 'Leonard Teiko Lartey', '35160acc4e', 'Series 7', '2500', '1', 'Paid', '2022-12-30 00:58:29.019918'),
('c44c882fcf', 'TNPW-2154', '9398d78690e5', 'Bill Clinton', '906f66277d', 'Pepsi', '20', '1', 'Paid', '2022-12-29 15:56:37.685950'),
('c882eb8504', 'ESAR-6342', '9398d78690e5', 'Bill Clinton', '2694df619c', 'Vacumn Bottle', '60', '1', 'Paid', '2023-01-03 16:54:11.302341'),
('ce29c596cc', 'LDZJ-4071', '0b0d49b9e894', 'Sam Kwaakye', 'c103db87be', 'Swang', '120', '1', 'Paid', '2023-01-05 20:24:57.048220'),
('fd40853026', 'DLAE-1968', '10a9b14f298e', 'Leonard Teiko Lartey', '34a08304d4', 'Coco Channel', '150', '1', 'Paid', '2023-01-03 08:18:37.207562'),
('ffe55d1808', 'CEXS-1245', 'b364bfcce828', 'Evans Atta Danso', '2694df619c', 'Vacumn Bottle', '60', '1', 'Paid', '2022-12-31 09:21:55.793518');


CREATE TABLE `rpos_pass_resets` (
  `reset_id` int(20) NOT NULL,
  `reset_code` varchar(200) NOT NULL,
  `reset_token` varchar(200) NOT NULL,
  `reset_email` varchar(200) NOT NULL,
  `reset_status` varchar(200) NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;



INSERT INTO `rpos_pass_resets` (`reset_id`, `reset_code`, `reset_token`, `reset_email`, `reset_status`, `created_at`) VALUES
(1, '63KU9QDGSO', '4ac4cee0a94e82a2aedc311617aa437e218bdf68', 'sysadmin@icofee.org', 'Pending', '2020-08-17 15:20:14.318643');



CREATE TABLE `rpos_payments` (
  `pay_id` varchar(200) NOT NULL,
  `pay_code` varchar(200) NOT NULL,
  `SID` varchar(200) NOT NULL,
  `customer_id` varchar(200) NOT NULL,
  `pay_amt` varchar(200) NOT NULL,
  `pay_method` varchar(200) NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;



INSERT INTO `rpos_payments` (`pay_id`, `pay_code`, `SID`, `customer_id`, `pay_amt`, `pay_method`, `created_at`) VALUES
('10a94a', 'QP2VJW4S8H', 'IGBY1WL32Z', '', '120.00', 'Cash', '2023-01-10 07:42:33.084559'),
('20f6ed', 'VH619QTA2F', 'UMBN-5206', '', '480.00', 'Credit Card', '2023-01-11 22:28:53.328079'),
('3a0c4f', 'A1H2ROMWGU', 'XDTC-0427', '', '2512.00', 'Bank Transfer', '2023-01-11 09:08:04.591486'),
('4d7812', 'OS3V1HJP2E', 'YLUQ-1946', '', '180.00', 'Bank Transfer', '2023-01-12 12:50:08.661687'),
('59aa2a', 'XSIGL6OJQ7', 'XIUO-2019', '', '36.00', 'Mobile Money', '2023-01-11 09:27:14.423659'),
('6a643a', 'HCK7MQU5J8', 'ZOAQ-1654', '', '360.00', 'Mobile Money', '2023-01-10 23:38:43.697961'),
('6b457e', 'F1JHZ7M6UI', 'BPQO-2130', '', '5120.00', 'Bank Transfer', '2023-01-10 07:45:12.786162'),
('8bb4e5', 'VXTPD2HYSW', 'TJRM-7185', '', '360.00', 'Mobile Money', '2023-01-12 12:48:57.315630'),
('975d96', 'YRBHQDZOI1', 'XK8YTUO3ZH', '', '120.00', 'Cash', '2023-01-10 07:42:50.720404'),
('992c04', 'XVNHB8UP6F', 'TKLP-9846', '9ce7e53fcd17', '20', 'Cash', '2022-12-31 09:08:44.293962'),
('9cca63', 'DFZ9QTJM23', 'BZKT-9063', '', '20.00', 'Mobile Money', '2023-01-11 09:25:27.263493'),
('9ed9cd', '682MY4VJG1', 'LQTV-7695', '7d044aa1fd98', '343', 'Cash', '2023-01-08 14:04:25.974305'),
('a09194', '4LCOGUHNDB', 'CEXS-1245', 'b364bfcce828', '60', 'Credit Card', '2022-12-31 09:21:55.646707'),
('a65317', '7UR6GJWZNY', 'ULJC-5206', '', '11760.00', 'Mobile Money', '2023-01-10 18:50:45.619809'),
('a669e9', 'Z2SQXKP4A6', 'SFOL-1379', '10a9b14f298e', '150', 'Mobile Money', '2023-01-01 18:19:15.121303'),
('aba967', 'MDZTUR6F2N', 'A4HNYP1BR2', '', '9450.00', 'Credit Card', '2023-01-08 23:21:17.825144'),
('b023e2', 'PQWG4OM5EA', 'LDZJ-4071', '0b0d49b9e894', '120', 'Mobile Money', '2023-01-05 20:24:56.963334'),
('b20155', 'DZR9M8SYKO', 'OXCI-7261', '907bddfbb6a9', '15', 'Cash', '2022-12-28 18:54:30.183114'),
('e09ded', 'R1BEISD6P4', 'V8REJ4XYUW', '', '711.00', 'Credit Card', '2023-01-10 07:41:52.056848'),
('f21624', 'OGP3XRAJND', 'LYUA-6395', '', '5150.00', 'Credit Card', '2023-01-12 12:59:34.306426'),
('f96fc1', '1HISMA8XOC', 'PWGZ-1708', '', '5000.00', 'Credit Card', '2023-01-11 12:47:13.556324'),
('fcd75f', 'EFVPKAQSO1', 'SDFY-7235', '', '60.00', 'Mobile Money', '2023-01-10 23:44:18.194440');



CREATE TABLE `rpos_products` (
  `prod_id` varchar(200) NOT NULL,
  `prod_code` varchar(200) NOT NULL,
  `prod_name` varchar(200) NOT NULL,
  `prod_img` varchar(200) NOT NULL,
  `prod_desc` longtext NOT NULL,
  `prod_price` varchar(200) NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6),
  `prod_cost` varchar(255) NOT NULL,
  `prod_catg` varchar(255) NOT NULL,
  `prod_stock` varchar(255) NOT NULL,
  `prod_barcode` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;



INSERT INTO `rpos_products` (`prod_id`, `prod_code`, `prod_name`, `prod_img`, `prod_desc`, `prod_price`, `created_at`, `prod_cost`, `prod_catg`, `prod_stock`, `prod_barcode`) VALUES
('236a66e00e', 'WJAO-5703', 'Logo', 'logo.png', 'sd,sd;\r\n', '343', '2023-01-04 15:59:26.341280', '2300', 'Electronics', '223', '122'),
('2694df619c', 'WRKN-0283', 'Vacumn Bottle', 'Vacumn Bottle.avif', 'Vacumn Bottle', '60', '2023-01-03 16:43:35.683650', '55', 'Utensils', '33', '44'),
('34a08304d4', 'ZDGW-6317', 'Coco Channel', 'Cocochannel.avif', 'Coco Channel', '150', '2022-12-29 14:36:54.871262', '130', '', '190', ''),
('35160acc4e', 'LXDI-2364', 'Series 7', 'series 7.avif', 'Watch', '2500', '2022-12-29 14:25:59.762331', '2300', '', '565', '122'),
('72864e1a32', 'CNRI-5682', 'Glass', 'alex-haigh-fEt6Wd4t4j0-unsplash.jpg', 'dls,ss,s\r\n', '12', '2023-01-06 17:32:22.892162', '22', 'Clothing', '12', '21'),
('771854045a', 'ARSQ-3752', 'Original Shirt', 'alex-haigh-fEt6Wd4t4j0-unsplash.jpg', 'Shirt', '120', '2022-12-29 14:45:29.310266', '100', '', '2323', '12'),
('906f66277d', 'IYLS-2953', 'Pepsi', 'pepsi.avif', 'pepsi', '20', '2022-12-29 14:42:46.455767', '14', '', '78', '45'),
('a111cdcdee', 'IXSH-7836', 'Camera', 'camera.avif', 'Camera', '6700', '2022-12-31 09:31:05.795320', '6300', '', '45', '12'),
('b393daf4b7', 'RBHM-2601', 'pa', '', 'ewersdas\r\n', '2500', '2023-01-03 23:53:59.682552', '8', 'Devices & Accessories', '223', '21'),
('c103db87be', 'INVF-7263', 'Swang', '', 'sksd', '120', '2023-01-04 16:00:50.746348', '29', 'Food &Drink', '30', '122'),
('da4bc47fb0', 'DQCZ-8046', 'welch', '', 'a\r\nsa\r\n', '111', '2023-01-03 23:55:20.908994', '15', 'Cosmetics', '2323', '12'),
('e47eb3666a', 'QBHL-6154', 'Aspirin', 'fiverrthumbnail.png', '23ewewe3', '125', '2023-01-08 18:59:20.212497', '15', 'Medication', '565', '12');


CREATE TABLE `rpos_staff` (
  `staff_id` int(20) NOT NULL,
  `staff_name` varchar(200) NOT NULL,
  `staff_number` varchar(200) NOT NULL,
  `staff_email` varchar(200) NOT NULL,
  `staff_pincode` varchar(4) NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;



INSERT INTO `rpos_staff` (`staff_id`, `staff_name`, `staff_number`, `staff_email`, `staff_pincode`, `created_at`) VALUES
(2, 'Cashier James', 'QEUY-9042', 'cashier@mail.com', '2222', '2023-01-02 16:12:07.370036'),
(28, 'Fred Gyan', 'IORZ-2573', 'fred@gmail.com', '9999', '2023-01-02 10:02:07.658759'),
(29, 'Ben', 'PACD-9830', '123@gmail.com', '0000', '2023-01-07 19:17:48.976487');


ALTER TABLE `invoice`
  ADD PRIMARY KEY (`SID`);


ALTER TABLE `invoice_products`
  ADD PRIMARY KEY (`ID`);


ALTER TABLE `rpos_admin`
  ADD PRIMARY KEY (`admin_id`);

ALTER TABLE `rpos_cart`
  ADD PRIMARY KEY (`cart_id`);


ALTER TABLE `rpos_categories`
  ADD PRIMARY KEY (`catg_id`);

ALTER TABLE `rpos_customers`
  ADD PRIMARY KEY (`customer_id`);


ALTER TABLE `rpos_orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `CustomerOrder` (`customer_id`),
  ADD KEY `ProductOrder` (`prod_id`);

ALTER TABLE `rpos_pass_resets`
  ADD PRIMARY KEY (`reset_id`);

ALTER TABLE `rpos_payments`
  ADD PRIMARY KEY (`pay_id`),
  ADD KEY `order` (`SID`);


ALTER TABLE `rpos_products`
  ADD PRIMARY KEY (`prod_id`);


ALTER TABLE `rpos_staff`
  ADD PRIMARY KEY (`staff_id`);




ALTER TABLE `invoice`
  MODIFY `SID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;


ALTER TABLE `invoice_products`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=227;

ALTER TABLE `rpos_cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;


ALTER TABLE `rpos_categories`
  MODIFY `catg_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;


ALTER TABLE `rpos_pass_resets`
  MODIFY `reset_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;


ALTER TABLE `rpos_staff`
  MODIFY `staff_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;


ALTER TABLE `rpos_orders`
  ADD CONSTRAINT `CustomerOrder` FOREIGN KEY (`customer_id`) REFERENCES `rpos_customers` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ProductOrder` FOREIGN KEY (`prod_id`) REFERENCES `rpos_products` (`prod_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

