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

Route::get('/{project?}', function (\Illuminate\Http\Request $request){
    $users = [
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
    $project = $request->project ?: 19;
    $status = ['wait','doing','done'];
    $delayedStatus = ['wait','doing'];
    // SELECT count(status) as num,`status` FROM `zt_task` WHERE (project=19 and assignedTo='zhangyamin' and `status` in('wait','doing','done')) GROUP BY `status` ;
    // SELECT * FROM `zt_task` WHERE (project=19 and assignedTo='zhangyamin' and `status` in('wait','doing') and `deadline` < CURRENT_DATE() AND deleted='0' ) ;
    $tempData = $data = [];
    $job = new \App\Models\Job;
    $db  = new \Illuminate\Support\Facades\DB;

    foreach ($users as $name => $user ) {
        $tempData[$name] = userByStauts($project, $user, $status, $job, $db);
        $tempData[$name]['delayed'] = userBydelayed($project, $user, $delayedStatus, $job);
    }

    $data = echartData($tempData);
    //dump($tempData, $data);

    return view('welcome', compact('data','users'));
});

/**
 * 按状态分组统计任务数
 * @param $project
 * @param $user
 * @param $status
 * @param $job
 * @param $db
 * @return mixed
 */
function userByStauts($project, $user, $status, $job, $db)
{
   return $job::where('project',$project)
        ->where('assignedTo',$user)
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
function userBydelayed($project, $user, $delayedStatus, $job)
{
    return $job::where('project',$project)
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
function echartData($tempData, $data = [])
{
    foreach ($tempData as $key => $item) {
        $data['wait'][] = $item['wait'] ?? 0;
        $data['doing'][] = $item['doing'] ?? 0;
        $data['done'][] = $item['done'] ?? 0;
        $data['delayed'][] = $item['delayed'] ?? 0;
    }

    return $data;
}
