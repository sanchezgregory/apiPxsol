<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasFactory, Notifiable, softdeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'last_name',
    ];

    public function user_files()
    {
        return $this->hasMany(UserFile::class);
    }

//    public function getUploadFile()
//    {
//        $files = $this->getImagesForUserByRaw();
//        return User::with(['user_files' => function($q) {
//            $q->orderBy('created_at', 'ASC')->orderBy('file_name', 'ASC');
//        }])->where('id', '=', $this->id)->get();
//    }

    public function getImagesForUserByRaw($uploadedFile = false)
    {

        $files = DB::select( DB::raw("SELECT t2.id, t2.file_name, t2.url, t2.created_at FROM users t1
                    join user_files t2 ON t1.id = t2.user_id
                    WHERE t1.id = '$this->id'") );

        if ($uploadedFile) {
            $uploaded_file = [
                'id' => $uploadedFile->id,
                'file_name' => $uploadedFile->file_name,
                'url' => $uploadedFile->url,
                'created_at' => $uploadedFile->created_at,
            ];

            return [
                'user_id' => $this->id,
                'uploaded_file' => $uploaded_file,
                'files' => $files,
            ];
        }

        return [
            'user_id' => $this->id,
            'files' => $files,
        ];
    }
}
