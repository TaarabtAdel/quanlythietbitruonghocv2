<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Notification;
use App\Models\Borrow;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $currentMonth    = Carbon::now()->format('m');
        $currentYear    = Carbon::now()->format('Y');
        
        $total_borrow = Borrow::query(true)
        ->whereMonth('borrow_date',$currentMonth)
        ->whereYear('borrow_date',$currentYear)
        ->whereIn('status',[
            Borrow::ACTIVE,
            Borrow::INACTIVE
        ])->count();
        ;
        $total_borrow_active = Borrow::query(true)
        ->whereMonth('borrow_date',$currentMonth)
        ->whereYear('borrow_date',$currentYear)
        ->where('status',Borrow::ACTIVE)->count();

        $total_borrow_inactive = Borrow::query(true)
        ->whereMonth('borrow_date',$currentMonth)
        ->whereYear('borrow_date',$currentYear)
        ->where('status',Borrow::INACTIVE)->count();

        $events = $this->getDataForCalendar($request);

        $params = [
            'total_borrow' => $total_borrow,
            'total_borrow_active' => $total_borrow_active,
            'total_borrow_inactive' => $total_borrow_inactive,
            'events' => $events
        ];
        return view('admin.home.index',$params);
    }
    private function getDataForCalendar($request = null){
        $currentMonth    = Carbon::now()->format('m');
        $currentYear    = Carbon::now()->format('Y');

        $borrows = Borrow::query(true)
        ->whereMonth('borrow_date',$currentMonth)
        ->whereYear('borrow_date',$currentYear)
        ->where('status',Borrow::ACTIVE)->get();

        $events = [];
        foreach($borrows as $borrow){
            $events[] = [
                'title' => '#'.$borrow->id.' - '.$borrow->user->name,
                'start' => $borrow->borrow_date
            ];
        }
        return $events;
        
    }
    public function is_read(Request $request)
    {
        try {
            DB::beginTransaction();
            DB::table('notifications')->where('is_read', 0)->update(['is_read' => 1]);
            DB::commit();
            $unreadCount = DB::table('notifications')->where('is_read', 0)->count();
            return redirect()->back()->with('success', 'Đã đánh dấu tất cả là đã đọc.')->with('unreadCount', $unreadCount);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Có lỗi xảy ra.');
        }
    }
}
