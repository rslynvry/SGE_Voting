<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Election;
use App\Models\StudentOrganization;
use App\Models\VotingsTracker;
use Inertia\Inertia;

class StudentController extends Controller
{
    public function home(Request $request)
    {
        $get_user_info = json_decode($request->cookie('voting_user_info'), true);

        //$student_id = $get_user_info['student_id'];
        $student_number = $get_user_info['student_number'];
        $student = Student::where('StudentNumber', $student_number)->first();

        //$student_number = $student->StudentNumber;

        $first_name = $student->FirstName;
        $last_name = $student->LastName;
        $middle_name = $student->MiddleName;

        // get full name and check if middle name is null
        if ($middle_name == null) {
            $full_name = $first_name . ' ' . $last_name;
        } else {
            $full_name = $first_name . ' ' . $middle_name . ' ' . $last_name;
        }

        return Inertia::render('Home', [
            'student_number' => $student_number,
            'full_name' => $full_name,
        ]);
    }
    
    public function votingProcess(Request $request)
    {
        $id = $request->id;
        $electionTable = Election::where('ElectionId', $id)->first();

        if (!$id || !$electionTable) {
            return redirect()->route('home');
        }

        // Check if in Voting Period

        $now = date('Y-m-d H:i:s');
        $votingStart = $electionTable->VotingStart;
        $votingEnd = $electionTable->VotingEnd;

        if ($now <= $votingStart) {
            return redirect()->route('home');
        }

        if ($now > $votingEnd) {
            return redirect()->route('home');
        }

        return Inertia::render('VotingProcess', [
            'id' => $id,
        ]);
    }

    public function votingPreview(Request $request)
    {   
        $id = $request->id;
        $votes = $request->votes;
        $abstainList = $request->abstainList;

        if (!$id || !$votes) {
            // If there's no id or votes in the request, try to get them from the session
            $id = $request->session()->get('id', $id);
            $votes = $request->session()->get('votes', $votes);
            $abstainList = $request->session()->get('abstainList', $abstainList);
        }

        if (!$id || !$votes) {
            return redirect()->back();
        }

        // Store the id and votes in the session
        $request->session()->put('id', $id);
        $request->session()->put('votes', $votes);
        $request->session()->put('abstainList', $abstainList);

        // Check if student course is same as election course
        $electionTable = Election::where('ElectionId', $id)->first();

        $student_number = $request->session()->get('student_number');

        // Check if in Voting Period

        $now = date('Y-m-d H:i:s');
        $votingStart = $electionTable->VotingStart;
        $votingEnd = $electionTable->VotingEnd;

        if ($now <= $votingStart) {
            return redirect()->route('home');
        }

        if ($now > $votingEnd) {
            return redirect()->route('home');
        }

        return Inertia::render('VotingPreview', [
            'id' => $id,
            'votes' => $votes,
            'abstainList' => $abstainList,
            'student_number' => $student_number,
        ]);
    }

}
