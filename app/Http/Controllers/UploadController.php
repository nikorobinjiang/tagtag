<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\MaterialAnnexs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public static $allowed_extensions = ["png", "jpg", "gif", 'jpeg', 'mp4', 'zip'];

    /**
     * Storage instance.
     *
     * @var string
     */
    protected $storage = '';

    public function __construct()
    {
        $this->model = MaterialAnnexs::class;
        $this->storage = Storage::disk(MaterialAnnexs::$storage_name);
    }

    /**
     * Store a newly created resource in storage by chunk.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function chunk(Request $request)
    {
        $chunkInfo = $request->input();
        $chunkSize = 1024 * 1024;

        if (isset($chunkInfo['phase']) && $chunkInfo['phase'] == 'start') {
            $chunkInfo['name'] = preg_replace('/\.\.+/', '', $chunkInfo['name']);
            $chunkInfo['name'] = str_replace('/', '_', $chunkInfo['name']);
            $fileExt = explode('.', $chunkInfo['name']);
            $fileExt = strtolower(end($fileExt));
            
            // 检查上传文件后缀
            if ($fileExt && !in_array($fileExt, self::$allowed_extensions)) {
                return response()->json(
                    [
                    'status' => 'failure',
                    'error' => '你只能上传 png, jpg, gif, mp4, zip'
                    ]
                );
            }
            
            // 后缀转换
            if ($fileExt == 'jpeg') {
                $fileExt = 'jpg';
            }

            $sessionInfo = [
                'name' => isset($chunkInfo['name']) ? $chunkInfo['name'] : microtime(),
                'ext' => $fileExt,
                'type' => isset($chunkInfo['mime_type']) ? $chunkInfo['mime_type']: '',
                'size' => $chunkInfo['size'],
                'chunks' => ceil($chunkInfo['size']/$chunkSize),
                'microtime' => microtime(),
                'time' => time()
            ];
            return response()->json([
                'status' => 'success',
                'data' => [
                    'session_id' => json_encode($sessionInfo),
                    'end_offset' => $chunkSize
                ]
            ]);
        } elseif (isset($chunkInfo['phase']) && $chunkInfo['phase'] == 'upload') {
            $sessionInfo = json_decode($chunkInfo['session_id'], 1);
            $chunksPath = implode(DIRECTORY_SEPARATOR, [
                getUploadCategory($sessionInfo['ext']),
                date('Y'),
                date('m'),
                'chunks',
                $sessionInfo['name'] . '__' . $sessionInfo['time'],
                sprintf("%05s", $chunkInfo['start_offset']/$chunkSize)
            ]);
            $this->storage->put(
                $chunksPath,
                file_get_contents($request->file('chunk')->getRealPath())
            );
            return response()->json([
                'status' => 'success'
            ]);
        } elseif (isset($chunkInfo['phase']) && $chunkInfo['phase'] == 'finish') {
            $sessionInfo = json_decode($chunkInfo['session_id'], 1);
            $fileExt = $sessionInfo['ext'];
            $chunksPath = implode(DIRECTORY_SEPARATOR, [
                getUploadCategory($fileExt),
                date('Y'),
                date('m'),
                'chunks',
                $sessionInfo['name'] . '__' . $sessionInfo['time']
            ]);
            $files = $this->storage->files($chunksPath);
            if (count($files) != $sessionInfo['chunks']) {
                return response()->json(['success' => false,'error' => '文件上传失败']);
            }

            $destinationFile = implode(DIRECTORY_SEPARATOR, [
                getUploadCategory($fileExt),
                date('Y'),
                date('m'),
                date('d'),
                str_random(10).'.'.$fileExt
            ]);
            $fullFilePath = $this->storage->path($destinationFile);

            if (!file_exists($fullFilePath)) {
                $this->storage->put($destinationFile, '');
            }

            foreach ($files as $key => $value) {
                file_put_contents(
                    $fullFilePath,
                    file_get_contents($this->storage->path($value)),
                    FILE_APPEND
                );
            }

            // 删除 chunks
            // $this->storage->deleteDirectory($chunksPath);

            $insdata = [];
            $insdata['file_path'] = $destinationFile;
            $insdata['file_url'] = $this->storage->url($destinationFile);
            $insdata['file_type'] = $fileExt;
            $insdata['file_size'] = $sessionInfo['size'];
            $insdata['file_name'] = $sessionInfo['name'];
            $insdata['add_time'] = time();

            if (in_array($fileExt, array('jpeg', 'jpg', 'png', 'gif'))) {
                $image = \Image::make($this->storage->path($insdata['file_path']));
                $insdata['file_width'] = $image->width();
                $insdata['file_height'] = $image->height();
            } elseif (in_array($fileExt, array('mp4'))) {
                // 解析视频
                $full_file_path = $this->storage->path($insdata['file_path']);
                $preview_file_path = str_replace('.mp4', '.jpg', $full_file_path);
                $video_info = getVideoInfo($full_file_path);
                if (empty($video_info[0])) {
                    return response()->json(['success' => false,'error' => '视频解析失败']);
                }

                $insdata['file_width'] = isset($video_info[0]['width'])?$video_info[0]['width']:0;
                $insdata['file_height'] = isset($video_info[0]['height'])?$video_info[0]['height']:0;
                $insdata['video_time'] = isset($video_info[0]['seconds'])?$video_info[0]['seconds']:0;
                
                if (isset($video_info[0]['seconds']) && $video_info[0]['seconds']<2.4) {
                    $screen_time = 1;
                } else {
                    $screen_time = sprintf('%02s', rand(1, min(floor(isset($video_info[0]['seconds'])?$video_info[0]['seconds']:0), 60)));
                }
                passthru("ffmpeg -i {$full_file_path} -y -f image2 -ss 00:00:{$screen_time} -t 0.001 -s {$insdata['file_width']}x{$insdata['file_height']} {$preview_file_path}");
            } else {
                $insdata['file_width'] = 0;
                $insdata['file_height'] = 0;
            }

            $res = [];
            $item = MaterialAnnexs::create($insdata);
            $res = [
                'success' => true,
                'item' => $item,
                'id' => $item->annex_id
            ];

            $scope = $request->input('scope', '');
            if ($scope == 'lp') {
                $unzipPath = $item->unzip(\App\Http\Controllers\Material\LandingPageController::$zipFilesCheck);
                if ($unzipPath) {
                    $prewiewFile = $unzipPath . '/preview.jpg';
                    if ($this->storage->exists($prewiewFile)) {
                        $res['preview'] = $this->storage->url($prewiewFile);
                    } else {
                        $res['preview'] =  '';
                    }
                } else {
                    $res['error'] = '压缩包格式不正确';
                    $res['success'] = false;
                }
            }
            return response()->json($res);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $file = $request->file('file_data');
        if ($file) {
            return $this->store_chunk($request);
        }

        $file = $request->file('file');
        // dd($file);

        $file_ext = strtolower($file->getClientOriginalExtension());
        $allowed_extensions = ["png", "jpg", "gif", 'jpeg', 'mp4', 'zip'];

        // 检查上传文件后缀
        if ($file_ext && !in_array($file_ext, $allowed_extensions)) {
            return response()->json(['success' => false,'error' => '你只能上传 png, jpg, gif, mp4, zip']);
        }

        // 后缀转换
        if ($file_ext == 'jpeg') {
            $file_ext = 'jpg';
        }

        // if (in_array($file_ext, array('mp4'))) {
        // }

        $destinationPath = getUploadCategory($file_ext)
            . DIRECTORY_SEPARATOR
            . date('Y')
            .DIRECTORY_SEPARATOR
            . date('m')
            .DIRECTORY_SEPARATOR
            . date('d')
            . DIRECTORY_SEPARATOR;

        $fileName = str_random(10).'.'.$file_ext;
        $this->storage->put($destinationPath.$fileName, file_get_contents($file->getRealPath()));

        $insdata = [];
        $insdata['file_path'] = $destinationPath.$fileName;
        $insdata['file_url'] = $this->storage->url($destinationPath.$fileName);
        $insdata['file_type'] = $file_ext;
        $insdata['file_size'] = $file->getClientSize();
        $insdata['file_name'] = $file->getClientOriginalName();
        $insdata['add_time'] = time();

        // dd($insdata);

        if (in_array($file_ext, array('jpeg', 'jpg', 'png', 'gif'))) {
            $image = \Image::make($this->storage->path($insdata['file_path']));
            $insdata['file_width'] = $image->width();
            $insdata['file_height'] = $image->height();
        } elseif (in_array($file_ext, array('mp4'))) {
            // 解析视频
            $full_file_path = $this->storage->path($insdata['file_path']);
            $preview_file_path = str_replace('.mp4', '.jpg', $full_file_path);
            $video_info = getVideoInfo($full_file_path);
            if (empty($video_info[0])) {
                return response()->json(['success' => false,'error' => '视频解析失败']);
            }

            $insdata['file_width'] = isset($video_info[0]['width'])?$video_info[0]['width']:0;
            $insdata['file_height'] = isset($video_info[0]['height'])?$video_info[0]['height']:0;
            $insdata['video_time'] = isset($video_info[0]['seconds'])?$video_info[0]['seconds']:0;
            
            if (isset($video_info[0]['seconds']) && $video_info[0]['seconds']<2.4) {
                $screen_time = 1;
            } else {
                $screen_time = sprintf('%02s', rand(1, min(floor(isset($video_info[0]['seconds'])?$video_info[0]['seconds']:0), 60)));
            }
            passthru("ffmpeg -i {$full_file_path} -y -f image2 -ss 00:00:{$screen_time} -t 0.001 -s {$insdata['file_width']}x{$insdata['file_height']} {$preview_file_path}");
        } else {
            $insdata['file_width'] = 0;
            $insdata['file_height'] = 0;
        }

        $res = [];
        $item = MaterialAnnexs::create($insdata);
        $res = [
            'success' => true,
            'item' => $item,
            'id' => $item->annex_id
        ];

        $scope = $request->input('scope', '');
        if ($scope == 'lp') {
            $unzipPath = $item->unzip(\App\Http\Controllers\Material\LandingPageController::$zipFilesCheck);
            if ($unzipPath) {
                $prewiewFile = $unzipPath . '/preview.jpg';
                if ($this->storage->exists($prewiewFile)) {
                    $res['preview'] = $this->storage->url($prewiewFile);
                } else {
                    $res['preview'] =  '';
                }
            } else {
                $res['error'] = '压缩包格式不正确';
                $res['success'] = false;
            }
        }

        return response()->json($res);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function download(Request $request)
    {
        $url = $request->input('url');
        $urlInfo = parse_url($url);
        $file = isset($urlInfo['path']) ? preg_replace('/\.+/', '.', $urlInfo['path']) : '';
        if (preg_match('/^\/uploads/', $file)) {
            $file = str_replace('/uploads', '', $file);
            if ($this->storage->exists($file)) {
                return response()->download($this->storage->path($file), basename($file));
            }
        }
        return view('common/msg', ['content' => '文件不存在']);
    }

    private function store_chunk(Request $request)
    {
        $file = $request->file('file_data');
        $chunk = $request->input('chunk');
        $chunks = $request->input('chunks');

        $file_ext = strtolower($file->getClientOriginalExtension());
        $allowed_extensions = ["png", "jpg", "gif", 'jpeg', 'mp4', 'zip'];

        // 检查上传文件后缀
        if ($file_ext && !in_array($file_ext, $allowed_extensions)) {
            return response()->json(['success' => false,'error' => '你只能上传 png, jpg, gif, mp4, zip']);
        }

        // 后缀转换
        if ($file_ext == 'jpeg') {
            $file_ext = 'jpg';
        }

        $chunksPath = getUploadCategory($file_ext)
            . DIRECTORY_SEPARATOR
            . date('Y')
            .DIRECTORY_SEPARATOR
            . date('m')
            .DIRECTORY_SEPARATOR
            . 'chunks'
            . DIRECTORY_SEPARATOR
            . $file->getClientOriginalName()
            . DIRECTORY_SEPARATOR;
        $fileName = sprintf("%05s", $chunk).'.'.$file_ext;
        $this->storage->put($chunksPath.$fileName, file_get_contents($file->getRealPath()));

        if ($chunk+1 == $chunks) {
            sleep(1);
            $files = $this->storage->files($chunksPath);
            if (count($files) > $chunks) {
                return response()->json(['success' => false,'error' => '文件上传失败']);
            } elseif (count($files) < $chunks) {
                sleep(5);
                $files = $this->storage->files($chunksPath);
            }

            if (count($files) < $chunks) {
                return response()->json(['success' => false,'error' => '文件上传失败']);
            }

            $destinationPath = getUploadCategory($file_ext)
            . DIRECTORY_SEPARATOR
            . date('Y')
            .DIRECTORY_SEPARATOR
            . date('m')
            .DIRECTORY_SEPARATOR
            . date('d')
            . DIRECTORY_SEPARATOR;
            $extension = $file_ext;
            $fileName = str_random(10).'.'.$file_ext;
            $fullFilePath = $this->storage->path($destinationPath.$fileName);

            if (!file_exists($fullFilePath)) {
                $this->storage->put($destinationPath.$fileName, '');
            }
            
            foreach ($files as $key => $value) {
                file_put_contents($fullFilePath, file_get_contents($this->storage->path($value)), FILE_APPEND);
                // $this->storage->append($destinationPath.$fileName, file_get_contents($this->storage->path($value)));
            }

            $insdata = [];
            $insdata['file_path'] = $destinationPath.$fileName;
            $insdata['file_url'] = $this->storage->url($destinationPath.$fileName);
            $insdata['file_type'] = $file_ext;
            $insdata['file_size'] = $request->input('file_size');
            $insdata['file_name'] = $request->input('file_name');
            $insdata['add_time'] = time();

            // dd($insdata);

            if (in_array($file_ext, array('jpeg', 'jpg', 'png', 'gif'))) {
                $image = \Image::make($this->storage->path($insdata['file_path']));
                $insdata['file_width'] = $image->width();
                $insdata['file_height'] = $image->height();
            } elseif (in_array($file_ext, array('mp4'))) {
                // 解析视频
                $full_file_path = $this->storage->path($insdata['file_path']);
                $preview_file_path = str_replace('.mp4', '.jpg', $full_file_path);
                $video_info = getVideoInfo($full_file_path);
                if (empty($video_info[0])) {
                    return response()->json(['success' => false,'error' => '视频解析失败']);
                }

                $insdata['file_width'] = isset($video_info[0]['width'])?$video_info[0]['width']:0;
                $insdata['file_height'] = isset($video_info[0]['height'])?$video_info[0]['height']:0;
                $insdata['video_time'] = isset($video_info[0]['seconds'])?$video_info[0]['seconds']:0;
                
                if (isset($video_info[0]['seconds']) && $video_info[0]['seconds']<2.4) {
                    $screen_time = 1;
                } else {
                    $screen_time = sprintf('%02s', rand(1, min(floor(isset($video_info[0]['seconds'])?$video_info[0]['seconds']:0), 60)));
                }
                passthru("ffmpeg -i {$full_file_path} -y -f image2 -ss 00:00:{$screen_time} -t 0.001 -s {$insdata['file_width']}x{$insdata['file_height']} {$preview_file_path}");
            } else {
                $insdata['file_width'] = 0;
                $insdata['file_height'] = 0;
            }

            $res = [];
            $item = MaterialAnnexs::create($insdata);
            $res = [
                'success' => true,
                'item' => $item,
                'id' => $item->annex_id
            ];

            $scope = $request->input('scope', '');
            if ($scope == 'lp') {
                $unzipPath = $item->unzip(\App\Http\Controllers\Material\LandingPageController::$zipFilesCheck);
                if ($unzipPath) {
                    $prewiewFile = $unzipPath . '/preview.jpg';
                    if ($this->storage->exists($prewiewFile)) {
                        $res['preview'] = $this->storage->url($prewiewFile);
                    } else {
                        $res['preview'] =  '';
                    }
                } else {
                    $res['error'] = '压缩包格式不正确';
                    $res['success'] = false;
                }
            }

            return response()->json($res);
        }
    }

    // 删除 TODO
    public function destroy(&$insdata)
    {
        switch ($this->action_name) {
            case 'destroy':
                if (!Advertisement::canDeleteIt($insdata->annex_id, $insdata->annex_type==2?'lp_annex':'material_annex')) {
                    return '该文件目前不能删除';
                }
                break;
            default:
                break;
        }
    }
}
