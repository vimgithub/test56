<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('project/{project?}', function (\Illuminate\Http\Request $request){

    $flag = 'task';
    $users = userList();

    $project = $request->project ?: 19;
    $status = ['wait','doing','done'];
    $delayedStatus = ['wait','doing'];

    $tempData = $data = [];
    $job = new \App\Models\Job;
    $db  = new \Illuminate\Support\Facades\DB;

    foreach ($users as $name => $user ) {
        $tempData[$name] = userByStauts($project, $user, $status, $job, $db, $flag);
        $tempData[$name]['delayed'] = userBydelayed($project, $user, $delayedStatus, $job);
    }

    $data = echartData($tempData, $flag);

    return view('welcome', compact('data','users','flag'));
});

Route::get('/bug/{project?}', function (\Illuminate\Http\Request $request){

    $flag = 'bug';
    $users = userList();

    $project = $request->project ?: 19;

    $status = ['active','resolved','closed'];
    $delayedStatus = ['active'];

    $tempData = $data = [];
    $bug = new \App\Models\Bug;
    $db  = new \Illuminate\Support\Facades\DB;

    foreach ($users as $name => $user ) {
        $tempData[$name] = userByStauts($project, $user, $status, $bug, $db, $flag);
        $tempData[$name]['delayed'] = userBydelayed($project, $user, $delayedStatus, $bug);
    }

    $data = echartData($tempData, $flag);

    return view('welcome', compact('data','users','flag'));
});

/**
 * 用户列表
 * @return array
 */
function userList()
{
    return  [
        '张亚敏'  => 'zhangyamin',
        '杨长青'  => 'yangchangqing',
        '乔俊颖'  => 'qiaojunying',
        '安亚琼'  => 'anyaqiong',
        '姚心杰'  => 'yaoxinjie',
        '吴昊'    => 'wuhao',
        '代玮'    => 'daiwei',
        '唐宇'    => 'tangyu',
        '翟维'    => 'zhaiwei'
    ];
}

/**
 * 按状态分组统计任务数
 * @param $project
 * @param $user
 * @param $status
 * @param $model
 * @param $db
 * @param $flag
 * @return mixed
 */
function userByStauts($project, $user, $status, $model, $db, $flag)
{
    $where = $flag == 'task' ? 'assignedTo' : 'resolvedBy';
    return $model::where('project',$project)
        ->where($where,$user)
        ->whereIn('status',$status)
        ->select($db::raw('count(status) as num'), 'status')
        ->groupBy('status')
        ->pluck('num','status')->toArray();
}

/**
 * 获取延期任务数
 * @param $project
 * @param $user
 * @param $delayedStatus
 * @param $job
 * @return mixed
 */
function userBydelayed($project, $user, $delayedStatus, $model)
{
    return $model::where('project',$project)
        ->where('assignedTo',$user)
        ->whereIn('status',$delayedStatus)
        ->where('deadline','<', date('y-m-d',time()))
        ->where('deleted','0')
        ->value(\Illuminate\Support\Facades\DB::raw('count(*)'));
}

/**
 * 整理数据
 * @param $tempData
 * @param array $data
 * @return array
 */
function echartData($tempData, $flag, $data = [])
{
    foreach ($tempData as $key => $item) {

        if ($flag == 'task') {
            $data['wait'][] = $item['wait'] ?? 0;
            $data['doing'][] = $item['doing'] ?? 0;
            $data['done'][] = $item['done'] ?? 0;
            $data['delayed'][] = $item['delayed'] ?? 0;
        }

        if ($flag == 'bug') {
            $data['active'][] = $item['active'] ?? 0;
            $data['resolved'][] = $item['resolved'] ?? 0;
            $data['closed'][] = $item['closed'] ?? 0;
            $data['delayed'][] = $item['delayed'] ?? 0;
        }

    }

    return $data;
}


