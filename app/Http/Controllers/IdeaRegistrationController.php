<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class IdeaRegistrationController extends Controller
{
    public function store(Request $request)
    {
        // Validate dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            // Thông tin cá nhân
            'research_field' => 'required|string|max:255',
            'other_field' => 'nullable|string|max:255',
            'fullname' => 'required|string|max:255',
            'phone' => 'required|string|regex:/^[0-9]{10,11}$/',
            'student_code' => 'required|string|max:50',
            'bank_account' => 'required|string|max:50',
            'bank_name' => 'required|string|max:255',
            
            // Thông tin ý tưởng
            'idea_name' => 'required|string|max:255',
            'idea_description' => 'required|string',
            'expected_products' => 'required|string',
            
            // Đánh giá
            'urgency' => 'required|string',
            'innovation' => 'required|string',
            'feasibility' => 'required|string',
            'potential' => 'required|string',
            'support_need' => 'required|in:yes,no',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Xử lý lĩnh vực nghiên cứu
            $researchField = $request->research_field;
            if ($researchField === 'Khác' && $request->other_field) {
                $researchField = $request->other_field;
            }

            // Kiểm tra và tạo/lấy bank_id
            $bank = DB::table('banks')
                ->where('bank_name', $request->bank_name)
                ->first();

            if (!$bank) {
                $bankId = DB::table('banks')->insertGetId([
                    'bank_name' => $request->bank_name,
                    'bank_account_number' => null,
                    'note' => null
                ]);
            } else {
                $bankId = $bank->bank_id;
            }

            // Kiểm tra user đã tồn tại chưa (theo phone hoặc student_code)
            $user = DB::table('users')
                ->where('phone', $request->phone)
                ->orWhere('staff_student_code', $request->student_code)
                ->first();

            if ($user) {
                // Cập nhật thông tin user nếu đã tồn tại
                DB::table('users')
                    ->where('user_id', $user->user_id)
                    ->update([
                        'full_name' => $request->fullname,
                        'phone' => $request->phone,
                        'staff_student_code' => $request->student_code,
                        'bank_account_no' => $request->bank_account,
                        'bank_id' => $bankId
                    ]);
                $userId = $user->user_id;
            } else {
                // Tạo user mới
                $userId = DB::table('users')->insertGetId([
                    'full_name' => $request->fullname,
                    'phone' => $request->phone,
                    'staff_student_code' => $request->student_code,
                    'bank_account_no' => $request->bank_account,
                    'bank_id' => $bankId
                ]);
            }

            // Tạo bản ghi ý tưởng
            $ideaId = DB::table('registration_ideas')->insertGetId([
                'user_id' => $userId,
                'group_id' => null, // Có thể cập nhật sau
                'research_field' => $researchField,
                'idea_title' => $request->idea_name,
                'idea_description' => $request->idea_description,
                'expected_products' => $request->expected_products,
                'urgency' => $request->urgency,
                'innovation' => $request->innovation,
                'feasibility' => $request->feasibility,
                'potential_development' => $request->potential,
                'need_support_round2' => $request->support_need === 'yes' ? 1 : 0
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đăng ký ý tưởng thành công',
                'data' => [
                    'idea_id' => $ideaId,
                    'user_id' => $userId
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi đăng ký',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        try {
            $ideas = DB::table('registration_ideas')
                ->join('users', 'registration_ideas.user_id', '=', 'users.user_id')
                ->leftJoin('banks', 'users.bank_id', '=', 'banks.bank_id')
                ->select(
                    'registration_ideas.*',
                    'users.full_name',
                    'users.phone',
                    'users.staff_student_code',
                    'users.bank_account_no',
                    'banks.bank_name'
                )
                ->orderBy('registration_ideas.idea_id', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $ideas
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy danh sách',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $idea = DB::table('registration_ideas')
                ->join('users', 'registration_ideas.user_id', '=', 'users.user_id')
                ->leftJoin('banks', 'users.bank_id', '=', 'banks.bank_id')
                ->where('registration_ideas.idea_id', $id)
                ->select(
                    'registration_ideas.*',
                    'users.full_name',
                    'users.phone',
                    'users.staff_student_code',
                    'users.bank_account_no',
                    'banks.bank_name'
                )
                ->first();

            if (!$idea) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy ý tưởng'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $idea
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
