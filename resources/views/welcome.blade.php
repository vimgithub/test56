<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a href="{{ url('/home') }}">Home</a>
                    @else
                        <a href="{{ route('login') }}">Login</a>
                        <a href="{{ route('register') }}">Register</a>
                    @endauth
                </div>
            @endif

            <div class="content" style="width: 100%">
                <div class="title m-b-md">
                    统计日期：{{ date('m-d', time()) }}
                </div>

                {{--<div class="links">
                    <a href="https://laravel.com/docs">Documentation</a>
                    <a href="https://laracasts.com">Laracasts</a>
                    <a href="https://laravel-news.com">News</a>
                    <a href="https://forge.laravel.com">Forge</a>
                    <a href="https://github.com/laravel/laravel">GitHub</a>
                </div>--}}
                <p></p>
                <div class="row">
                    <div class="col-md-12">
                        <div id="job" style="width: 100%;height:600px;"></div>
                    </div>
                </div>
            </div>
        </div>
        <script src="{{ asset('/assets/Echarts/echarts.min.js') }}"></script>
        <script type="application/javascript">
            var job = echarts.init(document.getElementById('job'));
            var posList = [
                'left', 'right', 'top', 'bottom',
                'inside',
                'insideTop', 'insideLeft', 'insideRight', 'insideBottom',
                'insideTopLeft', 'insideTopRight', 'insideBottomLeft', 'insideBottomRight'
            ];

            job.configParameters = {
                rotate: {
                    min: -90,
                    max: 90
                },
                align: {
                    options: {
                        left: 'left',
                        center: 'center',
                        right: 'right'
                    }
                },
                verticalAlign: {
                    options: {
                        top: 'top',
                        middle: 'middle',
                        bottom: 'bottom'
                    }
                },
                position: {
                    options: echarts.util.reduce(posList, function (map, pos) {
                        map[pos] = pos;
                        return map;
                    }, {})
                },
                distance: {
                    min: 0,
                    max: 100
                }
            };

            job.config = {
                rotate: 90,
                align: 'left',
                verticalAlign: 'middle',
                position: 'insideBottom',
                distance: 15,
                onChange: function () {
                    var labelOption = {
                        normal: {
                            rotate: job.config.rotate,
                            align: job.config.align,
                            verticalAlign: job.config.verticalAlign,
                            position: job.config.position,
                            distance: job.config.distance
                        }
                    };
                    myChart.setOption({
                        series: [{
                            label: labelOption
                        }, {
                            label: labelOption
                        }, {
                            label: labelOption
                        }, {
                            label: labelOption
                        }]
                    });
                }
            };


           /* var labelOption = {
                normal: {
                    show: true,
                    position: job.config.position,
                    distance: job.config.distance,
                    align: job.config.align,
                    verticalAlign: job.config.verticalAlign,
                    rotate: job.config.rotate,
                    formatter: '{c}  {name|{a}}',
                    fontSize: 16,
                    rich: {
                        name: {
                            textBorderColor: '#fff'
                        }
                    }
                }
            };*/

            var jobOption = {
                color: ['#2f4554', '#d48265', '#61a0a8', '#c23531'],
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'shadow'
                    }
                },
                legend: {
                    data: [@if($flag == 'task') '未开始', '进行中', '已完成', '延期', @elseif($flag == 'bug') '未解决','已解决','已关闭','延期' @endif]
                },
                toolbox: {
                    show: true,
                    orient: 'vertical',
                    left: 'right',
                    top: 'center',
                    feature: {
                        mark: {show: true},
                        dataView: {show: true, readOnly: false},
                        magicType: {show: true, type: ['line', 'bar', 'stack', 'tiled']},
                        restore: {show: true},
                        saveAsImage: {show: true}
                    }
                },
                calculable: true,
                xAxis: [
                    {
                        type: 'category',
                        axisTick: {show: false},
                        data: [@foreach($users as $name => $user) '{{ $name }}', @endforeach]
                    }
                ],
                yAxis: [
                    {
                        type: 'value'
                    }
                ],
                series: [
                    @if($flag == 'task')
                        {
                            name: '未开始',
                            type: 'bar',
                            barGap: 0,
                            label: jobOption,
                            data: [@foreach($data['wait'] as $wait) {{ $wait }}, @endforeach]
                        },
                        {
                            name: '进行中',
                            type: 'bar',
                            label: jobOption,
                            data: [@foreach($data['doing'] as $doing) {{ $doing }}, @endforeach]
                        },
                        {
                            name: '已完成',
                            type: 'bar',
                            label: jobOption,
                            data: [@foreach($data['done'] as $done) {{ $done }}, @endforeach]
                        },
                        {
                            name: '延期',
                            type: 'bar',
                            label: jobOption,
                            data: [@foreach($data['delayed'] as $delayed) {{ $delayed }}, @endforeach]
                        },
                    @elseif($flag == 'bug')
                        {
                            name: '未解决',
                            type: 'bar',
                            barGap: 0,
                            label: jobOption,
                            data: [@foreach($data['active'] as $active) {{ $active }}, @endforeach]
                        },
                        {
                            name: '已解决',
                            type: 'bar',
                            label: jobOption,
                            data: [@foreach($data['resolved'] as $resolved) {{ $resolved }}, @endforeach]
                        },
                        {
                            name: '已关闭',
                            type: 'bar',
                            label: jobOption,
                            data: [@foreach($data['closed'] as $closed) {{ $closed }}, @endforeach]
                        },
                        {
                            name: '延期',
                            type: 'bar',
                            label: jobOption,
                            data: [@foreach($data['delayed'] as $delayed) {{ $delayed }}, @endforeach]
                        }

                    @endif

                ]
            };
            job.setOption(jobOption);
        </script>
    </body>
</html>
