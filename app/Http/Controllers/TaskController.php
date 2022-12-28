<?php

namespace App\Http\Controllers;

use App\Enums\DateInWords;
use App\Http\Requests\CreateTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\SubTask;
use App\Models\Task;
use BenSampo\Enum\Enum;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'due_date' => 'nullable|date_format:Y-m-d',
            'status' => 'nullable|in:PENDING,COMPLETED',
            'title' => 'nullable',
            'due_date_in_words' => 'nullable|in:Today,This Week,Next Week,Overdue'
        ]);

        $taskQuery = Task::with('sub_tasks')->where('user_id', Auth::user()->id)->orderBy('due_date');

        $request->whenFilled('due_date', function () use ($request, $taskQuery) {
            $taskQuery->where('due_date', $request->due_date);
        });
        $request->whenFilled('status', function () use ($request, $taskQuery) {
            $taskQuery->where('status', $request->status);
        });

        $request->whenFilled('title', function () use ($request, $taskQuery) {
//            $taskQuery->whereRaw('MATCH(title) AGAINST(?)', ["$request->title"]);
            $taskQuery->whereRaw('title LIKE ?', ["%$request->title%"]);
        });

        $request->whenFilled('due_date_in_words', function () use ($request, $taskQuery) {
            if ($request->due_date_in_words == DateInWords::OVERDUE) {
                $taskQuery->whereDate('due_date', '<=', now()->subDay()->endOfDay());
                return;
            }
            $dates = $this->getDateRangeFromWords($request->due_date_in_words);
            $taskQuery->whereBetween('due_date', [$dates]);
        });

        return response()->json($taskQuery->paginate($request->per_page));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateTaskRequest $request)
    {
        if ($request->is_sub_task) {
            $this->validate($request, ['task_id' => 'required|exists:tasks,id']);
            $task = SubTask::create($request->validated());
        } else {
            $task = Task::create($request->validated() + ['user_id' => Auth::user()->id]);
        }

        return response()->json(['data' => $task, 'message' => 'Success']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Task $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Task $task
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {

        DB::transaction(function () use ($request, $task) {
            if ($request->status == 'COMPLETED')
                $task->sub_tasks()->update(['status' => 'COMPLETED']);
            $task->status = $request->status;
            $task->save();
        });

        return response()->json(['data' => $task, 'message' => 'Updated']);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        $task->delete();
        $task->sub_tasks()->delete();
        return response()->json(['data' => $task, 'message' => 'Deleted']);
    }

    function getDateRangeFromWords($words)
    {

        if ($words == DateInWords::TODAY)
            return [now()->startOfDay()->toDateString(), now()->endOfDay()->toDateString()];
        if ($words == DateInWords::NEXT_WEEK)
            return [Carbon::parse(DateInWords::NEXT_WEEK)->startOfDay()->toDateString(), Carbon::parse(DateInWords::NEXT_WEEK)->endOfWeek()->toDateString()];
        if ($words == DateInWords::THIS_WEEK)
            return [Carbon::now()->startOfWeek(1)->toDateString(), Carbon::now()->endOfWeek(7)->toDateString()];


        return [now(), now()->endOfDay()]; #send today as default
    }

}
