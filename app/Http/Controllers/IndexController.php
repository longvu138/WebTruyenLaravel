<?php

namespace App\Http\Controllers;

use App\Models\chapter;
use Illuminate\Http\Request;
use App\Models\DanhMucTruyen;
use App\Models\Sach;
use App\Models\Truyen;
use App\Models\TheLoai;
use Illuminate\Validation\Rules\Exists;

use function PHPUnit\Framework\isEmpty;

class IndexController extends Controller
{
    //
    public function timkiemajax(Request $request)
    {

        $data = $request->all();
        if ($data['keyword']) {
            $truyen = Truyen::where('kichhoat', 1)->where('tentruyen', 'LIKE', '%' . $data['keyword'] . '%')->Orwhere('tomtat', 'LIKE', '%' . $data['keyword']  . '%')->Orwhere('tacgia', 'LIKE', '%' .  $data['keyword']  . '%')->get();;
            if ($truyen != '') {

                $output = '<ul class="dropdown-menu dropdown-menu-right " style="position: absolute;display:block;padding: 5px 15px;top: 75%;"  >';
                foreach ($truyen as $key => $tr) {
                    // $err = " không tìm thấy";
                    // if (!isset($tr)) {
                    //     $output  .= '<li class="li_search_ajax"><a href="#">' . $err . '</a></li>';
                    // }
                    // else {

                    $output  .= '<li class="li_search_ajax"><a href="#">' . $tr->tentruyen . '</a></li>';
                    // }
                }

                $output .= '</ul>';
                echo $output;
            }
        }
    }



    public function home()
    {
        // 
        $theloai = TheLoai::orderBy('id', 'DESC')->get();
        //  lấy tất cả các danh mục truyện sắp xếp theo id từ DESC là gì đéo nhớ
        $danhmuc = DanhMucTruyen::orderBy('id', 'DESC')->get();
        // 
        $slide_truyen = Truyen::orderBy('id', 'DESC')->where('kichhoat', 1)->take(8)->get();

        // Lấy tất cả các truyện theo id sắp xếp theo DESC điều kiện có kích hoạt là 1 
        $truyen = Truyen::orderBy('id', 'DESC')->where('kichhoat', 1)->get();
        // gửi ra trang home trong thư mục pages tất cả data từ danhmuc và truyen
        return view('pages.home')->with(compact('danhmuc', 'truyen', 'theloai', 'slide_truyen'));
        # code...
    }

    public function danhmuc($slug)
    {
        // 
        $theloai = TheLoai::orderBy('id', 'DESC')->get();
        //
        $slide_truyen = Truyen::orderBy('id', 'DESC')->where('kichhoat', 1)->take(8)->get();

        // 

        $danhmuc = DanhMucTruyen::orderBy('id', 'DESC')->get();
        // lấy id theo slug truyền qua lấy 1 bản ghi
        $danhmuc_id = DanhMucTruyen::where('slug_danhmuc', $slug)->first();
        // 
        $tendanhmuc = $danhmuc_id->tendanhmuc;
        // lấy danh sách truyện theo id sắp xếp desc điuef kiện kích hoạt = 1 và danhmuc_id bằng iddanhmuc
        $truyen = Truyen::orderBy('id', 'DESC')->where('kichhoat', 1)->where('danhmuc_id', $danhmuc_id->id)->get();
        return view('pages.danhmuc')->with(compact('danhmuc', 'tendanhmuc', 'truyen', 'slide_truyen', 'theloai'));
    }

    public function xemtruyen($slug)
    {
        // 
        $theloai = TheLoai::orderBy('id', 'DESC')->get();
        // 
        $danhmuc = DanhMucTruyen::orderBy('id', 'DESC')->get();
        // lấy truyện với danhmuctruyen có slug bằng slug truyền vào và  kích hoạt bằng 1
        $truyen = Truyen::with('danhmuctruyen', 'theloai')->where('slug_truyen', $slug)->where('kichhoat', 1)->first();
        // 
        $chapter = chapter::with('truyen')->orderBy('id', 'ASC')->where('truyen_id', $truyen->id)->get();
        // 
        $chapter_dau = chapter::with('truyen')->orderBy('id', 'ASC')->where('truyen_id', $truyen->id)->first();
        // 
        $chapter_moinhat = chapter::with('truyen')->orderBy('id', 'DESC')->where('truyen_id', $truyen->id)->first();
        // 
        $slide_truyen = Truyen::orderBy('id', 'DESC')->where('kichhoat', 1)->take(8)->get();
        // 
        $truyennoibat = Truyen::Where('truyennoibat',1)->where('kichhoat', 1)->take(20)->get();
        // 
        $truyenxemnhieu = Truyen::Where('truyennoibat',2)->where('kichhoat', 1)->take(20)->get();
        // lấy truyện có cùng danh mục id danh mục = truyện => danhmuctruyen=> id wherenotin loại bỏ truyện có id trùng
        $cungdanhmuc = Truyen::with('danhmuctruyen', 'theloai')->where('danhmuc_id', $truyen->danhmuctruyen->id)->whereNotIn('id', [$truyen->id])->get();
        return view('pages.truyen')->with(compact('truyennoibat','truyenxemnhieu','danhmuc', 'slide_truyen', 'truyen', 'chapter', 'cungdanhmuc', 'chapter_moinhat','chapter_dau', 'theloai'));
    }
    public function xemchapter($slug)
    {
        // 
        $theloai = TheLoai::orderBy('id', 'DESC')->get();
        // 
        $danhmuc = DanhMucTruyen::orderBy('id', 'DESC')->get();
        // 
        $truyen = chapter::where('slug_chapter', $slug)->first();
        // breadrum
        $truyen_breadcrumb = Truyen::with('danhmuctruyen', 'theloai')->where('id', $truyen->truyen_id)->first();
        //
        $slide_truyen = Truyen::orderBy('id', 'DESC')->where('kichhoat', 1)->take(8)->get();
        // 
        $chapter = chapter::with('truyen')->where('slug_chapter', $slug)->where('truyen_id', $truyen->truyen_id)->first();
        // 
        $allchapter = chapter::with('truyen')->orderBy('id', 'ASC')->where('truyen_id', $truyen->truyen_id)->get();
        //  
        $maxid = chapter::where('truyen_id', $truyen->truyen_id)->orderBy('id', 'DESC')->first();
        // 
        $minid = chapter::where('truyen_id', $truyen->truyen_id)->orderBy('id', 'ASC')->first();

        // 
        $next_chapter = chapter::where('truyen_id', $truyen->truyen_id)->where('id', '>', $chapter->id)->min('slug_chapter');
        // 
        $previous_chapter = chapter::where('truyen_id', $truyen->truyen_id)->where('id', '<', $chapter->id)->max('slug_chapter');

        return view('pages.chapter')->with(compact('danhmuc', 'slide_truyen', 'theloai', 'truyen_breadcrumb', 'chapter', 'allchapter', 'next_chapter', 'previous_chapter', 'maxid', 'minid'));
    }

    public function theloai($slug)
    {
        // 
        $theloai = TheLoai::orderBy('id', 'DESC')->get();
        // 
        $danhmuc = DanhMucTruyen::orderBy('id', 'DESC')->get();
        // 
        $theloai_id = TheLoai::where('slug_theloai', $slug)->first();
        // 
        $slide_truyen = Truyen::orderBy('id', 'DESC')->where('kichhoat', 1)->take(8)->get();

        // 
        $tentheloai = $theloai_id->tentheloai;
        // 
        $truyen = Truyen::orderBy('id', 'DESC')->where('kichhoat', 1)->where('theloai_id', $theloai_id->id)->get();

        return view('pages.theloai')->with(compact('danhmuc', 'tentheloai', 'slide_truyen', 'truyen', 'theloai'));
    }
    //   tìm kiếm theo get không cần request
    public function timkiem(Request $request)
    {
        $data = $request->all();
        // 
        $slide_truyen = Truyen::orderBy('id', 'DESC')->where('kichhoat', 1)->take(8)->get();
        // 
        $theloai = TheLoai::orderBy('id', 'DESC')->get();
        // 
        $danhmuc = DanhMucTruyen::orderBy('id', 'DESC')->get();
        // 
        $tukhoa = $data['tukhoa'];
        $truyen = Truyen::with('danhmuctruyen')->where('tentruyen', 'LIKE', '%' . $tukhoa . '%')
            ->Orwhere('tomtat', 'LIKE', '%' . $tukhoa . '%')->Orwhere('tacgia', 'LIKE', '%' . $tukhoa . '%')->get();
           

        return view('pages.timkiem')->with(compact('danhmuc', 'truyen', 'theloai', 'slide_truyen', 'tukhoa'));
    }

    public function tag($tag)
    {

        $theloai = TheLoai::orderBy('id', 'DESC')->get();
        // 
        $danhmuc = DanhMucTruyen::orderBy('id', 'DESC')->get();
        // 
        $tags = explode('-', $tag);
        $truyen = Truyen::with('danhmuctruyen','theloai')->where(
            function ($query) use ($tags) {
                for ($i = 0; $i < count($tags); $i++) {
                    $query->Orwhere('tukhoa', 'LIKE', '% ' . $tags[$i] . ' %');         
                }
            }

        )->get();
        return view('pages.tag')->with(compact('truyen','tag','danhmuc','theloai'));
    }

    public function docsach()
    {
        $theloai = TheLoai::orderBy('id', 'DESC')->get();
        //  lấy tất cả các danh mục truyện sắp xếp theo id từ DESC là gì đéo nhớ
        $danhmuc = DanhMucTruyen::orderBy('id', 'DESC')->get();
        // 
        $slide_truyen = Truyen::orderBy('id', 'DESC')->where('kichhoat', 1)->take(8)->get();

        // Lấy tất cả các truyện theo id sắp xếp theo DESC điều kiện có kích hoạt là 1 
        $truyen = Truyen::orderBy('id', 'DESC')->where('kichhoat', 1)->get();
        // 
        $sach = Sach::orderBy('id','DESC')->where('kichhoat',1)->paginate(12);
        return view('pages.sach')->with(compact('sach','theloai','danhmuc','truyen'));
    }


    public function xemsachnhanh(Request $request)
    {
        $sachid = $request->sach_id;
        $sach = Sach::find($sachid);
        $output['tieudesach'] = $sach -> tensach;
        $output['noidungsach'] = $sach -> noidung;

        echo json_encode($output);
    }

    // public function tabsdanhmuc(Request $request )
    // {
    //     $data = $request->all();
    //     $output="";
    //     $truyen = Truyen::with('danhmuctruyen','theloai')->where('danhmuc_id',$data['danhmuc_id'])->get();
    //     // dd($truyen);
    //     foreach($truyen as $key => $value)
    //     {
    //         $output.='<div class="col-md-3">
    //                 <div class="card mb-3 box-shadow">
    //                     <img  class="card-img-top img-responsive"
    //                         src="'. url('public/uploads/truyen/'.$value->hinhanh).'">
    //                     <hr>
    //                     <div class="card-body p-3 ">
    //                         <h5 style="height: 40px;">'. $value->tentruyen.'</h5>
                           
    //                         <p style="height: 40px;" class="card-text"> $tomtat </p>
    //                         <div class="d-flex justify-content-between align-items-center">
    //                             <div class="btn-group">
    //                                 <a href="{{url("xem-truyen/".$value->slug_truyen)}}"
    //                                     class="btn btn-sm btn-outline-secondary">Đọc Ngay</a>
    //                                 <a class="btn btn-sm btn-outline-secondary"> <i class="fas fa-eye"></i> 50</a>
    //                             </div>
    //                             <small class="text-muted"> {{$value->created_at->diffForHumans() }}</small>
    //                         </div>
    //                     </div>
    //                     </a>
    //                 </div>
    //             </div>';
    //     }
    //     echo $output;
      
    // }
}
