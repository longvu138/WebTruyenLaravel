<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Truyen extends Model
{
    use HasFactory;
    // khai báo danh mục truyện
    public $timestamps = false; 
    protected $fillable= [
        'tentruyen','tomtat','kichhoat','slug_truyen','hinhanh','danhmuc_id','theloai_id'
    ];
    protected $primaryKey='id';
    protected $table="truyen";

    public function danhmuctruyen()
    {
        return $this->belongsTo('App\Models\DanhMucTruyen','danhmuc_id','id');
    }
    public function chapter()
    {
        # 1 truyện nhiều chapter
        return $this->hasMany('App\Models\chapter','truyen_id','id');
    }
    public function TheLoai()
    {
        return $this->belongsTo('App\Models\TheLoai','theloai_id','id');
    }
}
