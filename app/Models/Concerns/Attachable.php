<?php

namespace App\Models\Concerns;

use App\Models\Attachment;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;

trait Attachable
{
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function avatar()
    {
        return $this->morphOne(Attachment::class, 'attachable')
            ->where('type', 'avatar');
    }

    public function upload(File $file, $type = null)
    {
        return $this->uploadOne(
            $file->getRealPath(),
            $file->getClientOriginalName(),
            $file->getClientOriginalExtension(),
            $type,
            pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)
        );
    }

    public function uploadOne($file, $name, $extension, $type = 'file')
    {
        $morphKey = getMorphKey(self::class);

        $path = "{$morphKey}/{$this->id}/{$type}/{$name}";

        $disk = (config('app.env') === 'production') ? Storage::disk('s3') : Storage::disk('local');

        $disk->put($path, file_get_contents($file));

        $disk->setVisibility($path, 'private');

        if ($type === 'avatar') {
            $this->deleteAvatar();
        }

        $avatar = $this->attachments()->create([
            'path' => $path,
            'name' => $name,
            'extension' => $extension,
            'type' => $type,
            'url' => $disk->url($path)
        ]);

        $avatar['url'] = $disk->url($path);

        return $avatar;
    }

    public function deleteAvatar()
    {
        $avatar = $this->load('avatar')['avatar'];
        if (!empty($avatar)) {
            $disk = (config('app.env') == 'testing') ? $disk = Storage::disk('local') : $disk = Storage::disk('s3');

            $disk->delete($avatar['path']);

            return $this->attachments()->find($avatar['id'])->delete();
        }
        return null;
    }
}
