-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 21, 2024 at 04:09 PM
-- Server version: 10.5.23-MariaDB-cll-lve-log
-- PHP Version: 8.1.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jzxzyfjmhosting_thptphandinhphung-hatinh`
--

-- --------------------------------------------------------

--
-- Table structure for table `assets`
--

CREATE TABLE `assets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `country` varchar(255) DEFAULT NULL,
  `year` varchar(255) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `unit` varchar(255) DEFAULT NULL,
  `price` varchar(255) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `device_type_id` bigint(20) UNSIGNED NOT NULL,
  `department_id` bigint(20) UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `classify_id` bigint(20) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `borrows`
--

CREATE TABLE `borrows` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `borrow_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `approved` varchar(255) DEFAULT NULL,
  `borrow_note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `borrow_devices`
--

CREATE TABLE `borrow_devices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `borrow_id` bigint(20) UNSIGNED NOT NULL,
  `device_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `room_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `quantity` int(11) DEFAULT NULL,
  `borrow_date` date DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `lecture_name` varchar(255) DEFAULT NULL,
  `lesson_name` varchar(255) DEFAULT NULL,
  `session` varchar(255) DEFAULT NULL,
  `image_first` varchar(255) DEFAULT NULL,
  `image_last` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `lecture_number` int(11) DEFAULT NULL,
  `tiet` int(11) NOT NULL DEFAULT 0,
  `lab_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(6, 'Vật Lý', '2023-11-28 13:25:45', '2023-11-28 13:25:45', NULL),
(7, 'Hóa học', '2023-11-28 13:25:45', '2023-11-28 13:25:45', NULL),
(8, 'Sinh học', '2023-11-28 13:25:45', '2023-11-30 13:09:39', NULL),
(9, 'Công nghệ', '2023-11-28 13:25:45', '2023-11-28 13:25:45', NULL),
(10, 'Tin học', '2023-11-28 13:25:45', '2023-11-28 13:25:45', NULL),
(11, 'Tiếng Anh', '2023-11-28 13:25:45', '2023-11-28 13:25:45', NULL),
(12, 'Thể dục, Quốc phòng - An Ninh', '2023-11-28 13:25:45', '2023-11-28 13:25:45', NULL),
(13, 'Toán học', '2023-11-28 13:25:45', '2023-11-28 13:25:45', NULL),
(14, 'Ngữ Văn', '2023-11-28 13:25:45', '2023-11-28 13:25:45', NULL),
(15, 'Lịch Sử', '2023-11-28 13:25:45', '2023-11-28 13:25:45', NULL),
(16, 'Vật Lý- CN', '2023-11-28 13:34:35', '2023-11-30 13:49:34', '2023-11-30 13:49:34'),
(17, 'Địa lý', '2023-11-28 13:45:54', '2023-11-28 13:45:54', NULL),
(19, 'Vật Lý-Trải nghiệm', '2023-11-28 17:21:37', '2023-11-30 13:09:13', '2023-11-30 13:09:13'),
(20, 'KHTN', '2023-11-29 15:39:44', '2023-11-30 13:50:05', '2023-11-30 13:50:05'),
(21, 'HDGLL', '2023-11-30 14:13:25', '2023-11-30 14:13:25', NULL),
(22, 'Mĩ thuật', '2023-12-05 07:35:04', '2023-12-05 07:35:04', NULL),
(23, 'GDCD- KTPT', '2023-12-05 07:38:20', '2023-12-05 07:38:20', NULL),
(24, 'KHTN (THCS)', '2023-12-05 07:54:40', '2023-12-05 07:54:40', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `devices`
--

CREATE TABLE `devices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `device_type_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `country_name` varchar(50) DEFAULT NULL,
  `year` varchar(50) DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `price` varchar(50) DEFAULT NULL,
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `device_types`
--

CREATE TABLE `device_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Phó Hiệu trưởng Quản lý thiết bị', NULL, '2023-10-10 19:01:59', NULL),
(2, 'Quản Lý', NULL, NULL, NULL),
(3, 'Giáo Viên', NULL, '2023-10-10 19:00:54', NULL),
(4, 'Quản Lý thiết bị', '2023-10-10 18:59:17', '2023-10-10 19:00:08', NULL),
(6, 'Phó Hiệu trưởng', '2023-10-10 19:24:10', '2023-10-10 19:24:10', NULL),
(7, 'Hiệu trưởng', '2023-10-10 19:41:06', '2023-10-10 19:41:06', NULL),
(8, 'Tổ trưởng', '2023-10-10 20:23:22', '2023-10-10 20:23:22', NULL),
(9, 'Tổ phó', '2023-10-10 20:23:31', '2023-10-10 20:23:31', NULL),
(10, 'Người xem', '2023-10-21 12:09:30', '2023-10-21 12:09:30', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `groups_roles`
--

CREATE TABLE `groups_roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `group_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `groups_roles`
--

INSERT INTO `groups_roles` (`id`, `group_id`, `role_id`, `created_at`, `updated_at`) VALUES
(2286, 2, 1, NULL, NULL),
(2287, 2, 3, NULL, NULL),
(2288, 2, 4, NULL, NULL),
(2289, 2, 5, NULL, NULL),
(2290, 2, 7, NULL, NULL),
(2291, 2, 84, NULL, NULL),
(2292, 2, 86, NULL, NULL),
(2293, 2, 87, NULL, NULL),
(2294, 2, 88, NULL, NULL),
(2295, 2, 90, NULL, NULL),
(2296, 2, 92, NULL, NULL),
(2297, 2, 93, NULL, NULL),
(2298, 2, 94, NULL, NULL),
(2299, 2, 95, NULL, NULL),
(2300, 2, 96, NULL, NULL),
(2301, 2, 57, NULL, NULL),
(2302, 2, 59, NULL, NULL),
(2303, 2, 60, NULL, NULL),
(2304, 2, 61, NULL, NULL),
(2305, 2, 63, NULL, NULL),
(2306, 2, 41, NULL, NULL),
(2307, 2, 43, NULL, NULL),
(2308, 2, 44, NULL, NULL),
(2309, 2, 45, NULL, NULL),
(2310, 2, 47, NULL, NULL),
(2311, 2, 73, NULL, NULL),
(2312, 2, 75, NULL, NULL),
(2313, 2, 76, NULL, NULL),
(2314, 2, 77, NULL, NULL),
(2315, 2, 79, NULL, NULL),
(2316, 2, 65, NULL, NULL),
(2317, 2, 67, NULL, NULL),
(2318, 2, 68, NULL, NULL),
(2319, 2, 69, NULL, NULL),
(2320, 2, 71, NULL, NULL),
(2321, 2, 25, NULL, NULL),
(2322, 2, 26, NULL, NULL),
(2323, 2, 97, NULL, NULL),
(2324, 2, 27, NULL, NULL),
(2325, 2, 28, NULL, NULL),
(2326, 2, 29, NULL, NULL),
(2327, 2, 31, NULL, NULL),
(2328, 2, 98, NULL, NULL),
(2329, 2, 99, NULL, NULL),
(2330, 2, 33, NULL, NULL),
(2331, 2, 35, NULL, NULL),
(2332, 2, 36, NULL, NULL),
(2333, 2, 37, NULL, NULL),
(2334, 2, 39, NULL, NULL),
(2335, 2, 9, NULL, NULL),
(2336, 2, 100, NULL, NULL),
(2337, 2, 11, NULL, NULL),
(2338, 2, 12, NULL, NULL),
(2339, 2, 13, NULL, NULL),
(2340, 2, 15, NULL, NULL),
(2341, 2, 101, NULL, NULL),
(2342, 2, 102, NULL, NULL),
(2343, 2, 103, NULL, NULL),
(2344, 2, 104, NULL, NULL),
(2345, 2, 105, NULL, NULL),
(2346, 2, 106, NULL, NULL),
(2347, 2, 107, NULL, NULL),
(2348, 2, 108, NULL, NULL),
(2349, 2, 109, NULL, NULL),
(2350, 2, 110, NULL, NULL),
(2351, 2, 111, NULL, NULL),
(2352, 2, 112, NULL, NULL),
(2353, 2, 113, NULL, NULL),
(2354, 2, 114, NULL, NULL),
(2355, 3, 1, NULL, NULL),
(2356, 3, 3, NULL, NULL),
(2357, 3, 4, NULL, NULL),
(2358, 3, 5, NULL, NULL),
(2359, 3, 7, NULL, NULL),
(2360, 3, 84, NULL, NULL),
(2361, 3, 86, NULL, NULL),
(2362, 3, 87, NULL, NULL),
(2363, 3, 88, NULL, NULL),
(2364, 3, 90, NULL, NULL),
(2365, 3, 92, NULL, NULL),
(2366, 3, 93, NULL, NULL),
(2367, 3, 94, NULL, NULL),
(2368, 3, 95, NULL, NULL),
(2369, 3, 96, NULL, NULL),
(2370, 3, 57, NULL, NULL),
(2371, 3, 59, NULL, NULL),
(2372, 3, 60, NULL, NULL),
(2373, 3, 61, NULL, NULL),
(2374, 3, 63, NULL, NULL),
(2375, 3, 41, NULL, NULL),
(2376, 3, 43, NULL, NULL),
(2377, 3, 44, NULL, NULL),
(2378, 3, 45, NULL, NULL),
(2379, 3, 47, NULL, NULL),
(2380, 3, 73, NULL, NULL),
(2381, 3, 75, NULL, NULL),
(2382, 3, 76, NULL, NULL),
(2383, 3, 77, NULL, NULL),
(2384, 3, 79, NULL, NULL),
(2385, 3, 65, NULL, NULL),
(2386, 3, 67, NULL, NULL),
(2387, 3, 68, NULL, NULL),
(2388, 3, 69, NULL, NULL),
(2389, 3, 71, NULL, NULL),
(2390, 3, 25, NULL, NULL),
(2391, 3, 26, NULL, NULL),
(2392, 3, 97, NULL, NULL),
(2393, 3, 27, NULL, NULL),
(2394, 3, 28, NULL, NULL),
(2395, 3, 29, NULL, NULL),
(2396, 3, 31, NULL, NULL),
(2397, 3, 98, NULL, NULL),
(2398, 3, 99, NULL, NULL),
(2399, 3, 33, NULL, NULL),
(2400, 3, 35, NULL, NULL),
(2401, 3, 36, NULL, NULL),
(2402, 3, 37, NULL, NULL),
(2403, 3, 39, NULL, NULL),
(2404, 3, 9, NULL, NULL),
(2405, 3, 100, NULL, NULL),
(2406, 3, 11, NULL, NULL),
(2407, 3, 12, NULL, NULL),
(2408, 3, 13, NULL, NULL),
(2409, 3, 15, NULL, NULL),
(2410, 3, 101, NULL, NULL),
(2411, 3, 102, NULL, NULL),
(2412, 3, 103, NULL, NULL),
(2413, 3, 104, NULL, NULL),
(2414, 3, 105, NULL, NULL),
(2415, 3, 106, NULL, NULL),
(2416, 3, 107, NULL, NULL),
(2417, 3, 108, NULL, NULL),
(2418, 3, 109, NULL, NULL),
(2419, 3, 110, NULL, NULL),
(2420, 3, 111, NULL, NULL),
(2421, 3, 112, NULL, NULL),
(2422, 3, 113, NULL, NULL),
(2423, 3, 114, NULL, NULL),
(2424, 4, 1, NULL, NULL),
(2425, 4, 3, NULL, NULL),
(2426, 4, 4, NULL, NULL),
(2427, 4, 5, NULL, NULL),
(2428, 4, 7, NULL, NULL),
(2429, 4, 84, NULL, NULL),
(2430, 4, 86, NULL, NULL),
(2431, 4, 87, NULL, NULL),
(2432, 4, 88, NULL, NULL),
(2433, 4, 90, NULL, NULL),
(2434, 4, 92, NULL, NULL),
(2435, 4, 93, NULL, NULL),
(2436, 4, 94, NULL, NULL),
(2437, 4, 95, NULL, NULL),
(2438, 4, 96, NULL, NULL),
(2439, 4, 57, NULL, NULL),
(2440, 4, 59, NULL, NULL),
(2441, 4, 60, NULL, NULL),
(2442, 4, 61, NULL, NULL),
(2443, 4, 63, NULL, NULL),
(2444, 4, 41, NULL, NULL),
(2445, 4, 43, NULL, NULL),
(2446, 4, 44, NULL, NULL),
(2447, 4, 45, NULL, NULL),
(2448, 4, 47, NULL, NULL),
(2449, 4, 73, NULL, NULL),
(2450, 4, 75, NULL, NULL),
(2451, 4, 76, NULL, NULL),
(2452, 4, 77, NULL, NULL),
(2453, 4, 79, NULL, NULL),
(2454, 4, 65, NULL, NULL),
(2455, 4, 67, NULL, NULL),
(2456, 4, 68, NULL, NULL),
(2457, 4, 69, NULL, NULL),
(2458, 4, 71, NULL, NULL),
(2459, 4, 25, NULL, NULL),
(2460, 4, 26, NULL, NULL),
(2461, 4, 97, NULL, NULL),
(2462, 4, 27, NULL, NULL),
(2463, 4, 28, NULL, NULL),
(2464, 4, 29, NULL, NULL),
(2465, 4, 31, NULL, NULL),
(2466, 4, 98, NULL, NULL),
(2467, 4, 99, NULL, NULL),
(2468, 4, 33, NULL, NULL),
(2469, 4, 35, NULL, NULL),
(2470, 4, 36, NULL, NULL),
(2471, 4, 37, NULL, NULL),
(2472, 4, 39, NULL, NULL),
(2473, 4, 9, NULL, NULL),
(2474, 4, 100, NULL, NULL),
(2475, 4, 11, NULL, NULL),
(2476, 4, 12, NULL, NULL),
(2477, 4, 13, NULL, NULL),
(2478, 4, 15, NULL, NULL),
(2479, 4, 101, NULL, NULL),
(2480, 4, 102, NULL, NULL),
(2481, 4, 103, NULL, NULL),
(2482, 4, 104, NULL, NULL),
(2483, 4, 105, NULL, NULL),
(2484, 4, 106, NULL, NULL),
(2485, 4, 107, NULL, NULL),
(2486, 4, 108, NULL, NULL),
(2487, 4, 109, NULL, NULL),
(2488, 4, 110, NULL, NULL),
(2489, 4, 111, NULL, NULL),
(2490, 4, 112, NULL, NULL),
(2491, 4, 113, NULL, NULL),
(2492, 4, 114, NULL, NULL),
(2493, 6, 1, NULL, NULL),
(2494, 6, 3, NULL, NULL),
(2495, 6, 4, NULL, NULL),
(2496, 6, 5, NULL, NULL),
(2497, 6, 7, NULL, NULL),
(2498, 6, 84, NULL, NULL),
(2499, 6, 86, NULL, NULL),
(2500, 6, 87, NULL, NULL),
(2501, 6, 88, NULL, NULL),
(2502, 6, 90, NULL, NULL),
(2503, 6, 92, NULL, NULL),
(2504, 6, 93, NULL, NULL),
(2505, 6, 94, NULL, NULL),
(2506, 6, 95, NULL, NULL),
(2507, 6, 96, NULL, NULL),
(2508, 6, 57, NULL, NULL),
(2509, 6, 59, NULL, NULL),
(2510, 6, 60, NULL, NULL),
(2511, 6, 61, NULL, NULL),
(2512, 6, 63, NULL, NULL),
(2513, 6, 41, NULL, NULL),
(2514, 6, 43, NULL, NULL),
(2515, 6, 44, NULL, NULL),
(2516, 6, 45, NULL, NULL),
(2517, 6, 47, NULL, NULL),
(2518, 6, 73, NULL, NULL),
(2519, 6, 75, NULL, NULL),
(2520, 6, 76, NULL, NULL),
(2521, 6, 77, NULL, NULL),
(2522, 6, 79, NULL, NULL),
(2523, 6, 65, NULL, NULL),
(2524, 6, 67, NULL, NULL),
(2525, 6, 68, NULL, NULL),
(2526, 6, 69, NULL, NULL),
(2527, 6, 71, NULL, NULL),
(2528, 6, 25, NULL, NULL),
(2529, 6, 26, NULL, NULL),
(2530, 6, 97, NULL, NULL),
(2531, 6, 27, NULL, NULL),
(2532, 6, 28, NULL, NULL),
(2533, 6, 29, NULL, NULL),
(2534, 6, 31, NULL, NULL),
(2535, 6, 98, NULL, NULL),
(2536, 6, 99, NULL, NULL),
(2537, 6, 33, NULL, NULL),
(2538, 6, 35, NULL, NULL),
(2539, 6, 36, NULL, NULL),
(2540, 6, 37, NULL, NULL),
(2541, 6, 39, NULL, NULL),
(2542, 6, 9, NULL, NULL),
(2543, 6, 100, NULL, NULL),
(2544, 6, 11, NULL, NULL),
(2545, 6, 12, NULL, NULL),
(2546, 6, 13, NULL, NULL),
(2547, 6, 15, NULL, NULL),
(2548, 6, 101, NULL, NULL),
(2549, 6, 102, NULL, NULL),
(2550, 6, 103, NULL, NULL),
(2551, 6, 104, NULL, NULL),
(2552, 6, 105, NULL, NULL),
(2553, 6, 106, NULL, NULL),
(2554, 6, 107, NULL, NULL),
(2555, 6, 108, NULL, NULL),
(2556, 6, 109, NULL, NULL),
(2557, 6, 110, NULL, NULL),
(2558, 6, 111, NULL, NULL),
(2559, 6, 112, NULL, NULL),
(2560, 6, 113, NULL, NULL),
(2561, 6, 114, NULL, NULL),
(2562, 7, 1, NULL, NULL),
(2563, 7, 3, NULL, NULL),
(2564, 7, 4, NULL, NULL),
(2565, 7, 5, NULL, NULL),
(2566, 7, 7, NULL, NULL),
(2567, 7, 84, NULL, NULL),
(2568, 7, 86, NULL, NULL),
(2569, 7, 87, NULL, NULL),
(2570, 7, 88, NULL, NULL),
(2571, 7, 90, NULL, NULL),
(2572, 7, 92, NULL, NULL),
(2573, 7, 93, NULL, NULL),
(2574, 7, 94, NULL, NULL),
(2575, 7, 95, NULL, NULL),
(2576, 7, 96, NULL, NULL),
(2577, 7, 57, NULL, NULL),
(2578, 7, 59, NULL, NULL),
(2579, 7, 60, NULL, NULL),
(2580, 7, 61, NULL, NULL),
(2581, 7, 63, NULL, NULL),
(2582, 7, 41, NULL, NULL),
(2583, 7, 43, NULL, NULL),
(2584, 7, 44, NULL, NULL),
(2585, 7, 45, NULL, NULL),
(2586, 7, 47, NULL, NULL),
(2587, 7, 73, NULL, NULL),
(2588, 7, 75, NULL, NULL),
(2589, 7, 76, NULL, NULL),
(2590, 7, 77, NULL, NULL),
(2591, 7, 79, NULL, NULL),
(2592, 7, 65, NULL, NULL),
(2593, 7, 67, NULL, NULL),
(2594, 7, 68, NULL, NULL),
(2595, 7, 69, NULL, NULL),
(2596, 7, 71, NULL, NULL),
(2597, 7, 25, NULL, NULL),
(2598, 7, 26, NULL, NULL),
(2599, 7, 97, NULL, NULL),
(2600, 7, 27, NULL, NULL),
(2601, 7, 28, NULL, NULL),
(2602, 7, 29, NULL, NULL),
(2603, 7, 31, NULL, NULL),
(2604, 7, 98, NULL, NULL),
(2605, 7, 99, NULL, NULL),
(2606, 7, 33, NULL, NULL),
(2607, 7, 35, NULL, NULL),
(2608, 7, 36, NULL, NULL),
(2609, 7, 37, NULL, NULL),
(2610, 7, 39, NULL, NULL),
(2611, 7, 9, NULL, NULL),
(2612, 7, 100, NULL, NULL),
(2613, 7, 11, NULL, NULL),
(2614, 7, 12, NULL, NULL),
(2615, 7, 13, NULL, NULL),
(2616, 7, 15, NULL, NULL),
(2617, 7, 101, NULL, NULL),
(2618, 7, 102, NULL, NULL),
(2619, 7, 103, NULL, NULL),
(2620, 7, 104, NULL, NULL),
(2621, 7, 105, NULL, NULL),
(2622, 7, 106, NULL, NULL),
(2623, 7, 107, NULL, NULL),
(2624, 7, 108, NULL, NULL),
(2625, 7, 109, NULL, NULL),
(2626, 7, 110, NULL, NULL),
(2627, 7, 111, NULL, NULL),
(2628, 7, 112, NULL, NULL),
(2629, 7, 113, NULL, NULL),
(2630, 7, 114, NULL, NULL),
(2631, 8, 1, NULL, NULL),
(2632, 8, 3, NULL, NULL),
(2633, 8, 4, NULL, NULL),
(2634, 8, 5, NULL, NULL),
(2635, 8, 7, NULL, NULL),
(2636, 8, 84, NULL, NULL),
(2637, 8, 86, NULL, NULL),
(2638, 8, 87, NULL, NULL),
(2639, 8, 88, NULL, NULL),
(2640, 8, 90, NULL, NULL),
(2641, 8, 92, NULL, NULL),
(2642, 8, 93, NULL, NULL),
(2643, 8, 94, NULL, NULL),
(2644, 8, 95, NULL, NULL),
(2645, 8, 96, NULL, NULL),
(2646, 8, 57, NULL, NULL),
(2647, 8, 59, NULL, NULL),
(2648, 8, 60, NULL, NULL),
(2649, 8, 61, NULL, NULL),
(2650, 8, 63, NULL, NULL),
(2651, 8, 41, NULL, NULL),
(2652, 8, 43, NULL, NULL),
(2653, 8, 44, NULL, NULL),
(2654, 8, 45, NULL, NULL),
(2655, 8, 47, NULL, NULL),
(2656, 8, 73, NULL, NULL),
(2657, 8, 75, NULL, NULL),
(2658, 8, 76, NULL, NULL),
(2659, 8, 77, NULL, NULL),
(2660, 8, 79, NULL, NULL),
(2661, 8, 65, NULL, NULL),
(2662, 8, 67, NULL, NULL),
(2663, 8, 68, NULL, NULL),
(2664, 8, 69, NULL, NULL),
(2665, 8, 71, NULL, NULL),
(2666, 8, 25, NULL, NULL),
(2667, 8, 26, NULL, NULL),
(2668, 8, 97, NULL, NULL),
(2669, 8, 27, NULL, NULL),
(2670, 8, 28, NULL, NULL),
(2671, 8, 29, NULL, NULL),
(2672, 8, 31, NULL, NULL),
(2673, 8, 98, NULL, NULL),
(2674, 8, 99, NULL, NULL),
(2675, 8, 33, NULL, NULL),
(2676, 8, 35, NULL, NULL),
(2677, 8, 36, NULL, NULL),
(2678, 8, 37, NULL, NULL),
(2679, 8, 39, NULL, NULL),
(2680, 8, 9, NULL, NULL),
(2681, 8, 100, NULL, NULL),
(2682, 8, 11, NULL, NULL),
(2683, 8, 12, NULL, NULL),
(2684, 8, 13, NULL, NULL),
(2685, 8, 15, NULL, NULL),
(2686, 8, 101, NULL, NULL),
(2687, 8, 102, NULL, NULL),
(2688, 8, 103, NULL, NULL),
(2689, 8, 104, NULL, NULL),
(2690, 8, 105, NULL, NULL),
(2691, 8, 106, NULL, NULL),
(2692, 8, 107, NULL, NULL),
(2693, 8, 108, NULL, NULL),
(2694, 8, 109, NULL, NULL),
(2695, 8, 110, NULL, NULL),
(2696, 8, 111, NULL, NULL),
(2697, 8, 112, NULL, NULL),
(2698, 8, 113, NULL, NULL),
(2699, 8, 114, NULL, NULL),
(2700, 9, 1, NULL, NULL),
(2701, 9, 3, NULL, NULL),
(2702, 9, 4, NULL, NULL),
(2703, 9, 5, NULL, NULL),
(2704, 9, 7, NULL, NULL),
(2705, 9, 84, NULL, NULL),
(2706, 9, 86, NULL, NULL),
(2707, 9, 87, NULL, NULL),
(2708, 9, 88, NULL, NULL),
(2709, 9, 90, NULL, NULL),
(2710, 9, 92, NULL, NULL),
(2711, 9, 93, NULL, NULL),
(2712, 9, 94, NULL, NULL),
(2713, 9, 95, NULL, NULL),
(2714, 9, 96, NULL, NULL),
(2715, 9, 57, NULL, NULL),
(2716, 9, 59, NULL, NULL),
(2717, 9, 60, NULL, NULL),
(2718, 9, 61, NULL, NULL),
(2719, 9, 63, NULL, NULL),
(2720, 9, 41, NULL, NULL),
(2721, 9, 43, NULL, NULL),
(2722, 9, 44, NULL, NULL),
(2723, 9, 45, NULL, NULL),
(2724, 9, 47, NULL, NULL),
(2725, 9, 73, NULL, NULL),
(2726, 9, 75, NULL, NULL),
(2727, 9, 76, NULL, NULL),
(2728, 9, 77, NULL, NULL),
(2729, 9, 79, NULL, NULL),
(2730, 9, 65, NULL, NULL),
(2731, 9, 67, NULL, NULL),
(2732, 9, 68, NULL, NULL),
(2733, 9, 69, NULL, NULL),
(2734, 9, 71, NULL, NULL),
(2735, 9, 25, NULL, NULL),
(2736, 9, 26, NULL, NULL),
(2737, 9, 97, NULL, NULL),
(2738, 9, 27, NULL, NULL),
(2739, 9, 28, NULL, NULL),
(2740, 9, 29, NULL, NULL),
(2741, 9, 31, NULL, NULL),
(2742, 9, 98, NULL, NULL),
(2743, 9, 99, NULL, NULL),
(2744, 9, 33, NULL, NULL),
(2745, 9, 35, NULL, NULL),
(2746, 9, 36, NULL, NULL),
(2747, 9, 37, NULL, NULL),
(2748, 9, 39, NULL, NULL),
(2749, 9, 9, NULL, NULL),
(2750, 9, 100, NULL, NULL),
(2751, 9, 11, NULL, NULL),
(2752, 9, 12, NULL, NULL),
(2753, 9, 13, NULL, NULL),
(2754, 9, 15, NULL, NULL),
(2755, 9, 101, NULL, NULL),
(2756, 9, 102, NULL, NULL),
(2757, 9, 103, NULL, NULL),
(2758, 9, 104, NULL, NULL),
(2759, 9, 105, NULL, NULL),
(2760, 9, 106, NULL, NULL),
(2761, 9, 107, NULL, NULL),
(2762, 9, 108, NULL, NULL),
(2763, 9, 109, NULL, NULL),
(2764, 9, 110, NULL, NULL),
(2765, 9, 111, NULL, NULL),
(2766, 9, 112, NULL, NULL),
(2767, 9, 113, NULL, NULL),
(2768, 9, 114, NULL, NULL),
(2769, 10, 1, NULL, NULL),
(2770, 10, 3, NULL, NULL),
(2771, 10, 4, NULL, NULL),
(2772, 10, 5, NULL, NULL),
(2773, 10, 7, NULL, NULL),
(2774, 10, 84, NULL, NULL),
(2775, 10, 86, NULL, NULL),
(2776, 10, 87, NULL, NULL),
(2777, 10, 88, NULL, NULL),
(2778, 10, 90, NULL, NULL),
(2779, 10, 92, NULL, NULL),
(2780, 10, 93, NULL, NULL),
(2781, 10, 94, NULL, NULL),
(2782, 10, 95, NULL, NULL),
(2783, 10, 96, NULL, NULL),
(2784, 10, 57, NULL, NULL),
(2785, 10, 59, NULL, NULL),
(2786, 10, 60, NULL, NULL),
(2787, 10, 61, NULL, NULL),
(2788, 10, 63, NULL, NULL),
(2789, 10, 41, NULL, NULL),
(2790, 10, 43, NULL, NULL),
(2791, 10, 44, NULL, NULL),
(2792, 10, 45, NULL, NULL),
(2793, 10, 47, NULL, NULL),
(2794, 10, 73, NULL, NULL),
(2795, 10, 75, NULL, NULL),
(2796, 10, 76, NULL, NULL),
(2797, 10, 77, NULL, NULL),
(2798, 10, 79, NULL, NULL),
(2799, 10, 65, NULL, NULL),
(2800, 10, 67, NULL, NULL),
(2801, 10, 68, NULL, NULL),
(2802, 10, 69, NULL, NULL),
(2803, 10, 71, NULL, NULL),
(2804, 10, 25, NULL, NULL),
(2805, 10, 26, NULL, NULL),
(2806, 10, 97, NULL, NULL),
(2807, 10, 27, NULL, NULL),
(2808, 10, 28, NULL, NULL),
(2809, 10, 29, NULL, NULL),
(2810, 10, 31, NULL, NULL),
(2811, 10, 98, NULL, NULL),
(2812, 10, 99, NULL, NULL),
(2813, 10, 33, NULL, NULL),
(2814, 10, 35, NULL, NULL),
(2815, 10, 36, NULL, NULL),
(2816, 10, 37, NULL, NULL),
(2817, 10, 39, NULL, NULL),
(2818, 10, 9, NULL, NULL),
(2819, 10, 100, NULL, NULL),
(2820, 10, 11, NULL, NULL),
(2821, 10, 12, NULL, NULL),
(2822, 10, 13, NULL, NULL),
(2823, 10, 15, NULL, NULL),
(2824, 10, 101, NULL, NULL),
(2825, 10, 102, NULL, NULL),
(2826, 10, 103, NULL, NULL),
(2827, 10, 104, NULL, NULL),
(2828, 10, 105, NULL, NULL),
(2829, 10, 106, NULL, NULL),
(2830, 10, 107, NULL, NULL),
(2831, 10, 108, NULL, NULL),
(2832, 10, 109, NULL, NULL),
(2833, 10, 110, NULL, NULL),
(2834, 10, 111, NULL, NULL),
(2835, 10, 112, NULL, NULL),
(2836, 10, 113, NULL, NULL),
(2837, 10, 114, NULL, NULL),
(2906, 1, 1, NULL, NULL),
(2907, 1, 3, NULL, NULL),
(2908, 1, 4, NULL, NULL),
(2909, 1, 5, NULL, NULL),
(2910, 1, 7, NULL, NULL),
(2911, 1, 84, NULL, NULL),
(2912, 1, 86, NULL, NULL),
(2913, 1, 87, NULL, NULL),
(2914, 1, 88, NULL, NULL),
(2915, 1, 90, NULL, NULL),
(2916, 1, 92, NULL, NULL),
(2917, 1, 93, NULL, NULL),
(2918, 1, 94, NULL, NULL),
(2919, 1, 95, NULL, NULL),
(2920, 1, 96, NULL, NULL),
(2921, 1, 57, NULL, NULL),
(2922, 1, 59, NULL, NULL),
(2923, 1, 60, NULL, NULL),
(2924, 1, 61, NULL, NULL),
(2925, 1, 63, NULL, NULL),
(2926, 1, 41, NULL, NULL),
(2927, 1, 43, NULL, NULL),
(2928, 1, 44, NULL, NULL),
(2929, 1, 45, NULL, NULL),
(2930, 1, 47, NULL, NULL),
(2931, 1, 73, NULL, NULL),
(2932, 1, 75, NULL, NULL),
(2933, 1, 76, NULL, NULL),
(2934, 1, 77, NULL, NULL),
(2935, 1, 79, NULL, NULL),
(2936, 1, 65, NULL, NULL),
(2937, 1, 67, NULL, NULL),
(2938, 1, 68, NULL, NULL),
(2939, 1, 69, NULL, NULL),
(2940, 1, 71, NULL, NULL),
(2941, 1, 25, NULL, NULL),
(2942, 1, 26, NULL, NULL),
(2943, 1, 97, NULL, NULL),
(2944, 1, 27, NULL, NULL),
(2945, 1, 28, NULL, NULL),
(2946, 1, 29, NULL, NULL),
(2947, 1, 31, NULL, NULL),
(2948, 1, 98, NULL, NULL),
(2949, 1, 99, NULL, NULL),
(2950, 1, 33, NULL, NULL),
(2951, 1, 35, NULL, NULL),
(2952, 1, 36, NULL, NULL),
(2953, 1, 37, NULL, NULL),
(2954, 1, 39, NULL, NULL),
(2955, 1, 9, NULL, NULL),
(2956, 1, 100, NULL, NULL),
(2957, 1, 11, NULL, NULL),
(2958, 1, 12, NULL, NULL),
(2959, 1, 13, NULL, NULL),
(2960, 1, 15, NULL, NULL),
(2961, 1, 101, NULL, NULL),
(2962, 1, 102, NULL, NULL),
(2963, 1, 103, NULL, NULL),
(2964, 1, 104, NULL, NULL),
(2965, 1, 105, NULL, NULL),
(2966, 1, 106, NULL, NULL),
(2967, 1, 107, NULL, NULL),
(2968, 1, 108, NULL, NULL),
(2969, 1, 109, NULL, NULL),
(2970, 1, 110, NULL, NULL),
(2971, 1, 111, NULL, NULL),
(2972, 1, 112, NULL, NULL),
(2973, 1, 113, NULL, NULL),
(2974, 1, 114, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `labs`
--

CREATE TABLE `labs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `image` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `department_id` bigint(20) UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2023_08_14_092314_create_table_rooms', 1),
(6, '2023_08_14_092507_create_table_devices', 1),
(7, '2023_08_14_092811_create_roles_table', 1),
(8, '2023_08_14_092956_create_groups_table', 1),
(9, '2023_08_14_093111_create_groups_roles_table', 1),
(10, '2023_08_14_093559_update_users_table', 1),
(11, '2023_08_14_102523_create_borrows_table', 1),
(12, '2023_08_14_130520_create_borrow_devices_table', 1),
(13, '2023_08_16_065832_add_soft_delete_devices_table', 1),
(14, '2023_08_16_083226_add_soft_delete_rooms_table', 1),
(15, '2023_08_17_021902_update_users_table_to_colum_delete_at', 1),
(16, '2023_08_17_045036_add_soft_delete_borrows_table', 1),
(17, '2023_08_18_023907_add_soft_delete_at_table', 1),
(18, '2023_08_18_092319_update_borrows_devices_table', 1),
(19, '2023_08_21_083307_create_device_types_table', 1),
(20, '2023_08_21_094249_add_device_type_id_to_devices_table', 1),
(21, '2023_08_21_094349_add_foreign_key_to_devices_table', 1),
(22, '2023_08_22_021616_add_soft_delete_devicetypes_table', 1),
(23, '2023_08_23_043256_modify_table_nullable_image_devices', 1),
(24, '2023_08_23_044338_add_status_and_approved_to_borrows_table', 1),
(25, '2023_08_23_044850_update_users_table_image_nullable', 1),
(26, '2023_08_28_091956_add_default_value_to_status_column_in_borrowdevices', 1),
(27, '2023_08_28_092418_add_default_value_to_status_column_in_borrow', 1),
(28, '2023_08_29_151535_create_nest_controllers_table', 1),
(29, '2023_08_29_163148_update_users_table_colum_nest_id', 1),
(30, '2023_08_30_084900_update_users_table_nest_id_change', 1),
(31, '2023_08_30_085832_update_users_table_nest_id_to_foregin', 1),
(32, '2023_08_31_042826_update_users_table_colum_token', 1),
(33, '2023_08_31_090742_add_password_reset_tokens_table', 1),
(34, '2023_09_14_115223_add_borrow_note_to_borrows', 1),
(35, '2023_09_29_102608_create_departments_table', 1),
(36, '2023_09_29_102810_add_department_id_to_devices_table', 1),
(37, '2023_09_29_135909_create_options_table', 1),
(38, '2023_09_29_154332_add_deleted_at_to_departments', 1);

-- --------------------------------------------------------

--
-- Table structure for table `nests`
--

CREATE TABLE `nests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `action`, `is_read`, `item_id`, `created_at`, `updated_at`) VALUES
(1, 1, 'new_borrow', 'site_to_user', 1, 311, '2024-01-07 14:15:51', '2024-01-07 14:15:51'),
(2, 1, 'new_borrow', 'site_to_user', 1, 311, '2024-01-07 14:15:59', '2024-01-07 14:15:59'),
(3, 1, 'new_borrow', 'site_to_user', 1, 311, '2024-01-07 14:16:28', '2024-01-07 14:16:28'),
(4, 1, 'new_borrow', 'site_to_user', 1, 314, '2024-01-07 14:19:06', '2024-01-07 14:19:06'),
(5, 1, 'new_borrow', 'site_to_user', 0, 334, '2024-01-08 07:41:55', '2024-01-08 07:41:55'),
(6, 1, 'new_borrow', 'site_to_user', 0, 333, '2024-01-08 07:43:07', '2024-01-08 07:43:07'),
(7, 1, 'new_borrow', 'site_to_user', 0, 332, '2024-01-08 07:43:49', '2024-01-08 07:43:49');

-- --------------------------------------------------------

--
-- Table structure for table `options`
--

CREATE TABLE `options` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `option_name` varchar(255) NOT NULL,
  `option_label` varchar(255) NOT NULL,
  `option_value` text DEFAULT NULL,
  `option_group` varchar(255) DEFAULT NULL,
  `option_group_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `options`
--

INSERT INTO `options` (`id`, `option_name`, `option_label`, `option_value`, `option_group`, `option_group_name`) VALUES
(1, 'company_name', 'Tên đơn vị', 'THPT Phan Đình Phùng', 'general', 'Chung'),
(2, 'company_email', 'Email', 'admin@gmail.com', 'general', 'Chung'),
(3, 'company_phone', 'Số điện thoại', '0123456789', 'general', 'Chung'),
(4, 'mail_mailer', 'Nhà cung cấp', 'smtp', 'mail', 'Gửi email'),
(5, 'mail_host', 'Host', 'smtp.gmail.com', 'mail', 'Gửi email'),
(6, 'mail_encryption', 'Encryption', 'tls', 'mail', 'Gửi email'),
(7, 'mail_port', 'Port', '587', 'mail', 'Gửi email'),
(8, 'mail_username', 'Tài khoản', 'hoangvanlong.vn1999@gmail.com', 'mail', 'Gửi email'),
(9, 'mail_password', 'Mật khẩu', 'wuxpxsarxahcktit', 'mail', 'Gửi email'),
(11, 'company_address', 'Địa chỉ', 'Hà Tĩnh', 'general', NULL),
(12, 'auto_approved', 'Tự động duyệt', '1', 'borrow_device', NULL),
(13, 'check_duplicate', 'Kiểm tra trùng lặp thiết bị', '1', 'borrow_device', NULL),
(14, 'check_duplicate', 'Kiểm tra trùng lặp thiết bị', '1', 'borrow_lab', NULL),
(15, 'app_verison', 'Phiên bản phần mềm', '2.1', 'system', NULL),
(16, 'company_parent', 'Tên sở', 'Sở GD&DT Hà Tĩnh', 'general', NULL),
(17, 'allow_edit_approved', 'Cho phép Giáo Viên <span class=\"fw-bold text-uppercase\">cập nhật</span> phiếu mượn sau khi phiếu <span class=\"fw-bold\">Đã Duyệt</span>', '0', 'borrow_device', NULL),
(18, 'allow_edit_pending', 'Cho phép Giáo Viên <span class=\"fw-bold text-uppercase\">cập nhật</span> phiếu mượn sau khi phiếu <span class=\"fw-bold\">Chờ Duyệt</span>', '1', 'borrow_device', NULL),
(19, 'allow_delete_approved', 'Cho phép Giáo Viên <span class=\"fw-bold text-danger text-uppercase\">xóa</span> phiếu mượn sau khi phiếu <span class=\"fw-bold\">Đã Duyệt</span>', '0', 'borrow_device', NULL),
(20, 'allow_delete_pending', 'Cho phép Giáo Viên <span class=\"fw-bold text-danger text-uppercase\">xóa</span> phiếu mượn sau khi phiếu <span class=\"fw-bold\">Chờ Duyệt</span>', '1', 'borrow_device', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `group_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `group_name`, `created_at`, `updated_at`) VALUES
(1, 'Device_viewAny', 'Device', NULL, NULL),
(2, 'Device_view', 'Device', NULL, NULL),
(3, 'Device_create', 'Device', NULL, NULL),
(4, 'Device_update', 'Device', NULL, NULL),
(5, 'Device_delete', 'Device', NULL, NULL),
(6, 'Device_restore', 'Device', NULL, NULL),
(7, 'Device_forceDelete', 'Device', NULL, NULL),
(8, 'Device_trash', 'Device', NULL, NULL),
(9, 'Group_viewAny', 'Group', NULL, NULL),
(10, 'Group_view', 'Group', NULL, NULL),
(11, 'Group_create', 'Group', NULL, NULL),
(12, 'Group_update', 'Group', NULL, NULL),
(13, 'Group_delete', 'Group', NULL, NULL),
(14, 'Group_restore', 'Group', NULL, NULL),
(15, 'Group_forceDelete', 'Group', NULL, NULL),
(16, 'Group_trash', 'Group', NULL, NULL),
(17, 'BorrowDevice_viewAny', 'BorrowDevice', NULL, NULL),
(18, 'BorrowDevice_view', 'BorrowDevice', NULL, NULL),
(19, 'BorrowDevice_create', 'BorrowDevice', NULL, NULL),
(20, 'BorrowDevice_update', 'BorrowDevice', NULL, NULL),
(21, 'BorrowDevice_delete', 'BorrowDevice', NULL, NULL),
(22, 'BorrowDevice_restore', 'BorrowDevice', NULL, NULL),
(23, 'BorrowDevice_forceDelete', 'BorrowDevice', NULL, NULL),
(24, 'BorrowDevice_trash', 'BorrowDevice', NULL, NULL),
(25, 'Borrow_viewAny', 'Borrow', NULL, NULL),
(26, 'Borrow_view', 'Borrow', NULL, NULL),
(27, 'Borrow_create', 'Borrow', NULL, NULL),
(28, 'Borrow_update', 'Borrow', NULL, NULL),
(29, 'Borrow_delete', 'Borrow', NULL, NULL),
(30, 'Borrow_restore', 'Borrow', NULL, NULL),
(31, 'Borrow_forceDelete', 'Borrow', NULL, NULL),
(32, 'Borrow_trash', 'Borrow', NULL, NULL),
(33, 'User_viewAny', 'User', NULL, NULL),
(34, 'User_view', 'User', NULL, NULL),
(35, 'User_create', 'User', NULL, NULL),
(36, 'User_update', 'User', NULL, NULL),
(37, 'User_delete', 'User', NULL, NULL),
(38, 'User_restore', 'User', NULL, NULL),
(39, 'User_forceDelete', 'User', NULL, NULL),
(40, 'User_trash', 'User', NULL, NULL),
(41, 'Room_viewAny', 'Room', NULL, NULL),
(42, 'Room_view', 'Room', NULL, NULL),
(43, 'Room_create', 'Room', NULL, NULL),
(44, 'Room_update', 'Room', NULL, NULL),
(45, 'Room_delete', 'Room', NULL, NULL),
(46, 'Room_restore', 'Room', NULL, NULL),
(47, 'Room_forceDelete', 'Room', NULL, NULL),
(48, 'Room_trash', 'Room', NULL, NULL),
(49, 'Role_viewAny', 'Role', NULL, NULL),
(50, 'Role_view', 'Role', NULL, NULL),
(51, 'Role_create', 'Role', NULL, NULL),
(52, 'Role_update', 'Role', NULL, NULL),
(53, 'Role_delete', 'Role', NULL, NULL),
(54, 'Role_restore', 'Role', NULL, NULL),
(55, 'Role_forceDelete', 'Role', NULL, NULL),
(56, 'Role_trash', 'Role', NULL, NULL),
(57, 'DeviceType_viewAny', 'DeviceType', NULL, NULL),
(58, 'DeviceType_view', 'DeviceType', NULL, NULL),
(59, 'DeviceType_create', 'DeviceType', NULL, NULL),
(60, 'DeviceType_update', 'DeviceType', NULL, NULL),
(61, 'DeviceType_delete', 'DeviceType', NULL, NULL),
(62, 'DeviceType_restore', 'DeviceType', NULL, NULL),
(63, 'DeviceType_forceDelete', 'DeviceType', NULL, NULL),
(64, 'DeviceType_trash', 'DeviceType', NULL, NULL),
(65, 'Nest_viewAny', 'Nest', NULL, NULL),
(66, 'Nest_view', 'Nest', NULL, NULL),
(67, 'Nest_create', 'Nest', NULL, NULL),
(68, 'Nest_update', 'Nest', NULL, NULL),
(69, 'Nest_delete', 'Nest', NULL, NULL),
(70, 'Nest_restore', 'Nest', NULL, NULL),
(71, 'Nest_forceDelete', 'Nest', NULL, NULL),
(72, 'Nest_trash', 'Nest', NULL, NULL),
(73, 'Department_viewAny', 'Department', NULL, NULL),
(74, 'Department_view', 'Department', NULL, NULL),
(75, 'Department_create', 'Department', NULL, NULL),
(76, 'Department_update', 'Department', NULL, NULL),
(77, 'Department_delete', 'Department', NULL, NULL),
(78, 'Department_restore', 'Department', NULL, NULL),
(79, 'Department_forceDelete', 'Department', NULL, NULL),
(80, 'Department_trash', 'Department', NULL, NULL),
(81, 'Option_update', 'Option', NULL, NULL),
(82, 'Borrow_update_status', 'Borrow', NULL, NULL),
(83, 'Borrow_update_approved', 'Borrow', NULL, NULL),
(84, 'Asset_viewAny', 'Asset', NULL, NULL),
(85, 'Asset_view', 'Asset', NULL, NULL),
(86, 'Asset_create', 'Asset', NULL, NULL),
(87, 'Asset_update', 'Asset', NULL, NULL),
(88, 'Asset_delete', 'Asset', NULL, NULL),
(89, 'Asset_restore', 'Asset', NULL, NULL),
(90, 'Asset_forceDelete', 'Asset', NULL, NULL),
(91, 'Asset_trash', 'Asset', NULL, NULL),
(92, 'Lab_viewAny', 'Lab', '2024-01-13 01:05:57', '2024-01-13 01:05:57'),
(93, 'Lab_create', 'Lab', '2024-01-13 01:05:57', '2024-01-13 01:05:57'),
(94, 'Lab_update', 'Lab', '2024-01-13 01:05:57', '2024-01-13 01:05:57'),
(95, 'Lab_delete', 'Lab', '2024-01-13 01:05:57', '2024-01-13 01:05:57'),
(96, 'Lab_forceDelete', 'Lab', '2024-01-13 01:05:57', '2024-01-13 01:05:57'),
(97, 'Borrow_approve', 'Borrow', '2024-01-13 01:05:57', '2024-01-13 01:05:57'),
(98, 'BorrowDevice_viewAny', 'BorrowDevices', '2024-01-13 01:05:57', '2024-01-13 01:05:57'),
(99, 'BorrowLab_viewAny', 'BorrowLabs', '2024-01-13 01:05:57', '2024-01-13 01:05:57'),
(100, 'Group_role', 'Group', '2024-01-13 01:05:57', '2024-01-13 01:05:57'),
(101, 'Import_Nest', 'Import', '2024-01-13 01:05:57', '2024-01-13 01:05:57'),
(102, 'Import_Department', 'Import', '2024-01-13 01:05:57', '2024-01-13 01:05:57'),
(103, 'Import_Room', 'Import', '2024-01-13 01:05:57', '2024-01-13 01:05:57'),
(104, 'Import_DeviceType', 'Import', '2024-01-13 01:05:57', '2024-01-13 01:05:57'),
(105, 'Import_Lab', 'Import', '2024-01-13 01:05:57', '2024-01-13 01:05:57'),
(106, 'Import_Asset', 'Import', '2024-01-13 01:05:57', '2024-01-13 01:05:57'),
(107, 'Import_Device', 'Import', '2024-01-13 01:05:57', '2024-01-13 01:05:57'),
(108, 'Export_BorrowDevicesNest', 'Export', '2024-01-13 01:05:57', '2024-01-13 01:05:57'),
(109, 'Export_BorrowDevicesUser', 'Export', '2024-01-13 01:05:57', '2024-01-13 01:05:57'),
(110, 'Export_BorrowDevice', 'Export', '2024-01-13 01:05:57', '2024-01-13 01:05:57'),
(111, 'Export_BorrowDetail', 'Export', '2024-01-13 01:05:57', '2024-01-13 01:05:57'),
(112, 'Export_BorrowLab', 'Export', '2024-01-13 01:05:57', '2024-01-13 01:05:57'),
(113, 'System_Option', 'System', '2024-01-13 01:05:57', '2024-01-13 01:05:57'),
(114, 'System_Update', 'System', '2024-01-13 01:05:57', '2024-01-13 01:05:57');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `group_id` bigint(20) UNSIGNED NOT NULL,
  `nest_id` bigint(20) UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `address`, `phone`, `image`, `gender`, `birthday`, `group_id`, `nest_id`, `deleted_at`, `token`) VALUES
(1, 'Quản Trị Viên', 'admin@gmail.com', NULL, '$2y$10$nJNjyupUk6P5dNLZtB4sbe6BObgej8mEKhzRjEI5hteTRA.hhkNGC', 'Y8toW5Do7oa5BSpkwNLZRRVW3G8XFUwlolW2m10pzn28qgCXI7UNovfbxuZa', '2023-10-09 17:20:11', '2023-11-05 12:54:23', 'test', '123456789', '/storage/users/WXBqTGLtYGntUdtPGym5uBOjcD8kuHJO9i1xnqG6.jpg', 'Nam', '1999-02-20', 1, 0, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assets`
--
ALTER TABLE `assets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assets_device_type_id_foreign` (`device_type_id`),
  ADD KEY `assets_department_id_foreign` (`department_id`);

--
-- Indexes for table `borrows`
--
ALTER TABLE `borrows`
  ADD PRIMARY KEY (`id`),
  ADD KEY `borrows_user_id_foreign` (`user_id`);

--
-- Indexes for table `borrow_devices`
--
ALTER TABLE `borrow_devices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `borrow_devices_borrow_id_foreign` (`borrow_id`),
  ADD KEY `borrow_devices_device_id_foreign` (`device_id`),
  ADD KEY `borrow_devices_room_id_foreign` (`room_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `devices`
--
ALTER TABLE `devices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `device_type_id` (`device_type_id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `device_types`
--
ALTER TABLE `device_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `groups_roles`
--
ALTER TABLE `groups_roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `groups_roles_group_id_foreign` (`group_id`),
  ADD KEY `groups_roles_role_id_foreign` (`role_id`);

--
-- Indexes for table `labs`
--
ALTER TABLE `labs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `labs_department_id_foreign` (`department_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `nests`
--
ALTER TABLE `nests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_group_id_foreign` (`group_id`),
  ADD KEY `users_nest_id_foreign` (`nest_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assets`
--
ALTER TABLE `assets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `borrows`
--
ALTER TABLE `borrows`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `borrow_devices`
--
ALTER TABLE `borrow_devices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `devices`
--
ALTER TABLE `devices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `device_types`
--
ALTER TABLE `device_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `groups_roles`
--
ALTER TABLE `groups_roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2975;

--
-- AUTO_INCREMENT for table `labs`
--
ALTER TABLE `labs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `nests`
--
ALTER TABLE `nests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `options`
--
ALTER TABLE `options`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `borrows`
--
ALTER TABLE `borrows`
  ADD CONSTRAINT `borrows_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `borrow_devices`
--
ALTER TABLE `borrow_devices`
  ADD CONSTRAINT `borrow_devices_borrow_id_foreign` FOREIGN KEY (`borrow_id`) REFERENCES `borrows` (`id`),
  ADD CONSTRAINT `borrow_devices_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);

--
-- Constraints for table `devices`
--
ALTER TABLE `devices`
  ADD CONSTRAINT `devices_ibfk_1` FOREIGN KEY (`device_type_id`) REFERENCES `device_types` (`id`),
  ADD CONSTRAINT `devices_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`),
  ADD CONSTRAINT `users_nest_id_foreign` FOREIGN KEY (`nest_id`) REFERENCES `nests` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
