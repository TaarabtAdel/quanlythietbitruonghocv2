<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Borrow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $currentMonth    = Carbon::now()->format('m');
        $currentYear    = Carbon::now()->format('Y');

        $user_id = Auth::id();
        
        $total_borrow = Borrow::query(true)
        ->where('user_id',$user_id)
        ->whereMonth('borrow_date',$currentMonth)
        ->whereYear('borrow_date',$currentYear)
        ->whereIn('status',[
            Borrow::ACTIVE,
            Borrow::INACTIVE
        ])->count();
        ;
        $total_borrow_active = Borrow::query(true)
        ->where('user_id',$user_id)
        ->whereMonth('borrow_date',$currentMonth)
        ->whereYear('borrow_date',$currentYear)
        ->where('status',Borrow::ACTIVE)->count();

        $total_borrow_inactive = Borrow::query(true)
        ->where('user_id',$user_id)
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
        return view('teacher.home.index',$params);
    }
    private function getDataForCalendar($request = null){
        $currentMonth    = Carbon::now()->format('m');
        $currentYear    = Carbon::now()->format('Y');
        $user_id = Auth::id();

        $borrows = Borrow::query(true)
        ->where('user_id',$user_id)
        ->whereMonth('borrow_date',$currentMonth)
        ->whereYear('borrow_date',$currentYear)
        ->where('status',Borrow::ACTIVE)->get();

        $events = [];
        foreach($borrows as $borrow){
            $events[] = [
                'title' => '#'.$borrow->id,
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