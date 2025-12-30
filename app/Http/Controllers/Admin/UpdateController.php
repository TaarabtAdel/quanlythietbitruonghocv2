<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UpdateController extends Controller
{
    public function __construct(){
        $this->lastVersion = env('SYSTEM_VERSION',2.2);
    }
    public function index()
    {
        $option = \App\Models\Option::where('option_name','app_verison')->first();
        $currentVersion = $option->option_value ?? '1.0';
        $params = [
            'currentVersion' => $currentVersion,
            'lastVersion' => $this->lastVersion,
        ];
        return view('admin.update.index',$params);
    }
    public function doUpdate()
    {
        // Lấy phiên bản hiện tại của ứng dụng
        $currentVersion = \App\Models\Option::get_option_name('app_verison'); // Ví dụ: '2.5'
        $targetVersion = $this->lastVersion; // Ví dụ: '2.6' hoặc '3.0'
        $updated = true;
        $versionToUpdate = null; // Biến lưu lại phiên bản bị lỗi nếu có

        // Danh sách các phiên bản cập nhật theo thứ tự
        // TẬP HỢP TẤT CẢ CÁC BƯỚC CẬP NHẬT MÀ HỆ THỐNG CÓ
        $allAvailableUpdates = [
            '2.5' => \App\Models\Versions\Ver25::class,
            '2.6' => \App\Models\Versions\Ver26::class,
            // Thêm các phiên bản tiếp theo ở đây
            '2.7' => \App\Models\Versions\Ver27::class,
            '2.8' => \App\Models\Versions\Ver28::class,
            // '3.0' => \App\Models\Versions\Ver30::class,
        ];

        // Lấy ra danh sách các key (số phiên bản) và sắp xếp chúng theo thứ tự tăng dần
        $updateVersions = array_keys($allAvailableUpdates);
        usort($updateVersions, 'version_compare'); // Đảm bảo thứ tự là 2.5, 2.6, 2.7, ...

        // --- Bắt đầu quá trình cập nhật tuần tự ---
        foreach ($updateVersions as $versionKey) {
            $versionToUpdate = $versionKey;
            $updaterClass = $allAvailableUpdates[$versionKey];

            // 1. Kiểm tra: Bỏ qua các bước cập nhật mà phiên bản hiện tại đã VƯỢT QUA hoặc ĐANG BẰNG
            if (version_compare($versionToUpdate, $currentVersion, '<=')) {
                continue; // Bỏ qua, đã cập nhật phiên bản này rồi
            }

            // 2. Kiểm tra: Dừng lại nếu đã đạt đến phiên bản đích
            if (version_compare($versionToUpdate, $targetVersion, '>')) {
                break; // Đã vượt quá phiên bản đích, dừng cập nhật
            }

            // 3. Thực hiện cập nhật
            try {
                // Gọi phương thức doUpdate() của lớp tương ứng
                $updated = $updaterClass::doUpdate();

                if (!$updated) {
                    // Nếu một bước cập nhật thất bại, dừng lại ngay lập tức
                    break;
                }
            } catch (\Exception $e) {
                $updated = false;
                // Ghi log lỗi nếu cần
                // Log::error("Cập nhật phiên bản {$versionToUpdate} thất bại: " . $e->getMessage());
                break;
            }
        }

        // --- CẬP NHẬT PHIÊN BẢN VÀ XỬ LÝ KẾT QUẢ ---
        if ($updated) {
            // Sau khi hoàn thành tất cả các bước cần thiết, cập nhật phiên bản lên $targetVersion
            
            // Đoạn code lưu phiên bản mới vào DB (nên giữ nguyên như code ban đầu)
            \App\Models\Option::where('option_name', 'app_verison')->delete();
            $option = \App\Models\Option::where('option_name', 'app_verison')->first();
            if ($option) {
                $option->option_value = $targetVersion;
                $option->save();
            } else {
                \App\Models\Option::create([
                    'option_value' => $targetVersion,
                    'option_name' => 'app_verison',
                    'option_label' => 'Phiên bản phần mềm',
                    'option_group' => 'system',
                    'option_group_name' => 'Hệ Thống',
                ]);
            }
            return redirect()->back()->with('success', 'Cập nhật thành công lên phiên bản ' . $targetVersion . '!');
        } else {
            // Trả về lỗi
            $errorMsg = 'Cập nhật không thành công!';
            if ($versionToUpdate) {
                $errorMsg .= ' Đã dừng lại ở bước: ' . $versionToUpdate . '.';
            }
            return redirect()->back()->with('error', $errorMsg);
        }
    }
}