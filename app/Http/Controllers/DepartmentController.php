<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Member;
use App\Models\DeptLogo;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DepartmentController extends Controller
{
    public function index()
    {
        return Inertia::render('Departments/Index', [
            'departments' => Department::with('members')->latest()->get(),
            'deptLogos' => DeptLogo::latest()->get(),
        ]);
    }

    public function storeDepartment(Request $request)
    {
        $request->validate([
            'dept_name' => 'required|string|max:255',
            'image' => 'required|image|max:10248',
            'desc' => 'nullable|string|max:1000',
        ]);

        $path = $request->file('image')->store('departments', 'public');

        Department::create([
            'dept_name' => $request->dept_name,
            'image' => $path,
            'desc' => $request->desc,
        ]);

        return redirect()->back()->with('success', 'Department added successfully');
    }

    public function updateDepartment(Request $request, Department $department)
    {
        $request->validate([
            'dept_name' => 'required|string|max:255',
            'image' => 'nullable|image|max:10248',
            'desc' => 'nullable|string|max:1000',
        ]);

        if ($request->hasFile('image')) {
            if ($department->image && Storage::disk('public')->exists($department->image)) {
                Storage::disk('public')->delete($department->image);
            }
            $path = $request->file('image')->store('departments', 'public');
            $department->image = $path;
        }

        $department->dept_name = $request->dept_name;
        $department->desc = $request->desc;
        $department->save();

        return redirect()->back()->with('success', 'Department updated successfully');
    }

    public function destroyDepartment(Department $department)
    {
        if ($department->image && Storage::disk('public')->exists($department->image)) {
            Storage::disk('public')->delete($department->image);
        }
        $department->delete();

        return redirect()->back()->with('success', 'Department deleted successfully');
    }

    public function storeMember(Request $request)
    {
        try {
            $request->validate([
                'department_id' => 'required|exists:departments,id',
                'name' => 'required|string|max:255',
                'image' => 'required|image|max:10248',
                'job_type' => 'required|in:board_of_dept,vice_board_of_dept,section_head_dept,staff',
                'job_titles' => 'required|string|max:255',
                'position' => 'nullable|string|max:255',
            ]);

            $path = $request->file('image')->store('members', 'public');

            $member = Member::create([
                'department_id' => $request->department_id,
                'name' => $request->name,
                'image' => $path,
                'job_type' => $request->job_type,
                'job_titles' => $request->job_titles,
                'position' => $request->position,
            ]);

            return redirect()->back()->with('success', 'Member added successfully');
        } catch (\Exception $e) {
            Log::error('Member creation error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to add member: ' . $e->getMessage());
        }
    }

    public function updateMember(Request $request, Member $member)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|max:10248',
            'job_type' => 'required|in:board_of_dept,vice_board_of_dept,section_head_dept,staff',
            'job_titles' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('image')) {
            if ($member->image && Storage::disk('public')->exists($member->image)) {
                Storage::disk('public')->delete($member->image);
            }
            $path = $request->file('image')->store('members', 'public');
            $member->image = $path;
        }

        $member->update([
            'department_id' => $request->department_id,
            'name' => $request->name,
            'job_type' => $request->job_type,
            'job_titles' => $request->job_titles,
            'position' => $request->position,
        ]);

        return redirect()->back()->with('success', 'Member updated successfully');
    }

    public function destroyMember(Member $member)
    {
        if ($member->image && Storage::disk('public')->exists($member->image)) {
            Storage::disk('public')->delete($member->image);
        }

        $member->delete();

        return redirect()->back()->with('success', 'Member deleted successfully');
    }

    public function storeDeptLogo(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|max:10248',
        ]);

        $path = $request->file('image')->store('dept-logos', 'public');

        DeptLogo::create([
            'title' => $request->title,
            'image' => $path,
        ]);

        return redirect()->back()->with('success', 'Department logo added successfully');
    }

    public function updateDeptLogo(Request $request, DeptLogo $deptLogo)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|max:10248',
        ]);

        if ($request->hasFile('image')) {
            if ($deptLogo->image && Storage::disk('public')->exists($deptLogo->image)) {
                Storage::disk('public')->delete($deptLogo->image);
            }
            $path = $request->file('image')->store('dept-logos', 'public');
            $deptLogo->image = $path;
        }

        $deptLogo->title = $request->title;
        $deptLogo->save();

        return redirect()->back()->with('success', 'Department logo updated successfully');
    }

    public function destroyDeptLogo(DeptLogo $deptLogo)
    {
        if ($deptLogo->image && Storage::disk('public')->exists($deptLogo->image)) {
            Storage::disk('public')->delete($deptLogo->image);
        }
        $deptLogo->delete();

        return redirect()->back()->with('success', 'Department logo deleted successfully');
    }
}
